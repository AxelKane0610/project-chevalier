<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\tracking_info_service;
use App\Services\EmbeddingService;
use App\Models\Training_Tickets_Model;
use App\Models\Training_Courses_Model;
use App\Models\User;
use App\Models\Comments_Model;
use App\Models\Attachments_Model;
use Illuminate\Support\Facades\DB;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Http;


class TrainingController extends Controller
{
    //
    public function index()
    {
        $all_training_courses = Training_Courses_Model::query()->paginate(10);
        $all_your_training_tickets = Training_Tickets_Model::where('user_id', auth()->id())->get();
        $pending_tickets = Training_Tickets_Model::whereIn('status', ['1', '2', '3'])->where('user_id', auth()->id())->get();
        if (auth()->user()->hasRole('ROLE_SUPER_ADMIN')) {
            $all_country_team_training_tickets = Training_Tickets_Model::query()->paginate(10);
            return view('submit-training-menu', compact('pending_tickets', 'all_your_training_tickets', 'all_country_team_training_tickets', 'all_training_courses'));
        } else {

            $all_country_team_training_tickets = Training_Tickets_Model::whereHas('user_owner', function ($query) { //Lọc ra những ticket có user_owner có leader_id là id của user đang đăng nhập, tức là lọc ra những ticket của những user mà user đang đăng nhập là leader của họ, rồi mới lấy ra những ticket đó để trả về view
                $query->where('leader_id', auth()->id());
            })->paginate(10);

            return view('submit-training-menu', compact('pending_tickets', 'all_your_training_tickets', 'all_country_team_training_tickets', 'all_training_courses'));
        }
    }

    private function trainingTicketQuery()
    {
        $query = Training_Tickets_Model::query();

        if (!auth()->user()->hasRole('ROLE_SUPER_ADMIN')) {
            $query->whereHas('user_owner', function ($q) {
                $q->where('leader_id', auth()->id());
            });
        }

        return $query;
    }

    public function Filter_All_Courses_Table(Request $request)
    {
        $query = Training_Courses_Model::query();

        // Search
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('course_id', 'like', "%{$search}%")
                    ->orWhere('course_name', 'like', "%{$search}%");
            });
        }

        // Training No Filter
        if ($request->filled('training_no')) {

            $query->where('training_no', $request->training_no);
        }

        $all_training_courses = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {

            return view(
                'tables.all-training-courses-table',
                compact('all_training_courses')
            )->render();
        }
    }

    public function Filter_All_Country_Team_Training_Tickets_Table(Request $request)
    {
        $query = $this->trainingTicketQuery();

        // Search
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->orWhereHas('user_owner', function ($user) use ($search) {
                    $user->where('fullname', 'like', "%{$search}%");
                });
            });
        }

        // Training No Filter
        if ($request->filled('training_no')) {

            $query->where('training_no', $request->training_no);
        }

        if ($request->filled('status')) {

            $query->where('status', $request->status);
        }

        $all_country_team_training_tickets = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {

            return view(
                'tables.all-country-team-training-tickets-table',
                compact('all_country_team_training_tickets')
            )->render();
        }
    }

    public function Show_Training_Ticket_Details($id)
    {
        $ticket_details = Training_Tickets_Model::with('user_owner', 'active_attachments', 'ticket_tracking_info', 'ticket_comments.attachments', 'ticket_comments.user', 'training_courses')->findOrFail($id);
        return view('training-ticket-details', compact('ticket_details'));
    }

    public function Request_Training(Request $request)
    {
        try {
            $request->validate([
                'course_id' => 'required|array|min:1',
                'course_name' => 'required|array|min:1',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            DB::transaction(function () use ($request) {

                // Lấy Training No lớn nhất
                $trainingNo = (Training_Courses_Model::max('training_no') ?? 0) + 1;

                // Lưu từng dòng
                foreach ($request->course_id as $index => $courseId) {

                    Training_Courses_Model::create([
                        'training_no' => $trainingNo,
                        'course_id' => $courseId,
                        'course_name' => $request->course_name[$index],
                        'start_date' => $request->start_date,
                        'end_date' => $request->end_date,
                    ]);
                }

                $users = User::whereIn('team', ['2', '3'])->get();
                foreach ($users as $user) {

                    Training_Tickets_Model::create([
                        'user_id' => $user->id,
                        'training_no' => $trainingNo,
                        'status' => '2', // hoặc 'Pending'
                        'start_date' => $request->start_date,
                        'end_date' => $request->end_date,
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Training requested successfully ! ',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to request training due to ' . $e->getMessage(),
            ], 500);
        }
    }

    public function Add_Comment_Training_Ticket(Request $request, $id)
    {
        $comment_info_input = $request->validate([
            'comment' => 'required_without_all:attachments|string|nullable',
            'attachments' => 'required_without_all:comment|array|nullable',
            'attachments.*' => 'file|max:20480|mimes:jpg,jpeg,png,pdf,xlsx,docx',
        ]);

        $comment_info_input['comment'] = strip_tags($comment_info_input['comment']);
        $comment_info_input['ticket_id'] = $id;
        $comment_info_input['type_of_ticket'] = 5;
        $comment_info_input['user_id'] = auth()->id();

        $comment = Comments_Model::create($comment_info_input);


        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $originalName = $file->getClientOriginalName();
                $folderPath = '5/' . $id;
                $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục 'attachments' đã được cấu hình trong config/filesystems.php, với đường dẫn là 'attachments/1/{ticket_id}/{original_file_name}'

                Attachments_Model::create([
                    'type_of_ticket' => 5,
                    'ticket_id' => $id,
                    'comment_id' => $comment->id,
                    'file_path' => $filePath,
                    'name' => $originalName,
                    'status' => 1
                ]);
            }
        }


        return back()->with('success');
    }

    // public function Send_Verify_Training_Ticket($id)
    // {
    //     try {
    //         $ticket = Training_Tickets_Model::with('user_owner', 'active_attachments', 'training_courses')->findOrFail($id);
    //         $parser = new Parser();
    //         $courseNames = Training_Courses_Model::where('training_no', $ticket->training_no)
    //         ->pluck('course_name')->implode("\n- ");
    //         $certificates = $ticket->active_attachments->map(function ($attachment) use ($parser) {
    //             $pdfPath = Storage::disk('attachments')->path($attachment->file_path);
    //             try {
    //                 $pdf = $parser->parseFile($pdfPath);
    //                 $text = $pdf->getText();
    //             } catch (\Exception $e) {
    //                 $text = null;
    //             }
    //             return ['name' => $attachment->name, 'path' => $pdfPath, 'text' => $text,];
    //         });

    //         dd($courseNames, $certificates);
    //         $ticket->status = '3';
    //         $ticket->save();
    //         tracking_info_service::add($ticket->id, auth()->id(), 5, 'sent training verify at');
    //         return response()->json(['success' => true, 'message' => 'Send verify success !',]);
    //     } catch (\Exception $e) {
    //         return response()->json(['success' => false, 'message' => 'Failed to send verify training due to ' . $e->getMessage(),], 500);
    //     }
    // }

    // public function Send_Verify_Training_Ticket($id)
    // {
    //     try {

    //         $ticket = Training_Tickets_Model::with(
    //             'user_owner',
    //             'active_attachments',
    //             'training_courses'
    //         )->findOrFail($id);

    //         $parser = new Parser();

    //         $courseList = Training_Courses_Model::where(
    //             'training_no',
    //             $ticket->training_no
    //         )->pluck('course_name');

    //         $courseText = "";
    //         foreach ($courseList as $index => $course) {
    //             $courseText .= ($index + 1) . ". {$course}\n";
    //         }

    //         $courseStatus = [];

    //         foreach ($courseList as $course) {

    //             $courseStatus[$course] = [

    //                 'matched' => false,

    //                 'certificate' => null,

    //                 'method' => null

    //             ];
    //         }

    //         $results = [];

    //         foreach ($ticket->active_attachments as $attachment) {

    //             $pdfPath = Storage::disk('attachments')->path($attachment->file_path);

    //             try {

    //                 $pdf = $parser->parseFile($pdfPath);
    //                 $text = $pdf->getText();
    //             } catch (\Exception $e) {

    //                 $results[] = [
    //                     'certificate' => $attachment->name,
    //                     'matched' => false,
    //                     'reason' => 'Cannot extract PDF.'
    //                 ];

    //                 continue;
    //             }

    //             $matchedCourse = null;

    //             foreach ($courseList as $course) {

    //                 if (
    //                     stripos(
    //                         $this->normalizeCourse($text),
    //                         $this->normalizeCourse($course)
    //                     ) !== false
    //                 ) {

    //                     $matchedCourse = $course;
    //                     break;
    //                 }
    //             }

    //             if ($matchedCourse) {

    //                 $courseStatus[$matchedCourse] = [
    //                     'matched' => true,
    //                     'certificate' => $attachment->name,
    //                     'method' => 'PHP'
    //                 ];

    //                 $results[] = [
    //                     'certificate' => $attachment->name,
    //                     'course' => $matchedCourse,
    //                     'method' => 'PHP'

    //                 ];

    //                 continue;
    //             }

    //             /*
    //             ======================================================
    //             Nếu chạy tới đây nghĩa là PHP không tìm thấy.
    //             Chút nữa mới gọi Llama ở đây.
    //             ======================================================
    //             */
    //             $prompt = "
    //             You are an AI that verifies HP training certificates.

    //             Training Courses:

    //             {$courseText}

    //             Certificate:

    //             {$text}

    //             Rules:

    //             - Return ONLY valid JSON.
    //             - course_name MUST be EXACTLY one value from the Training Courses list.
    //             - If no course matches, return null.

    //             Example:

    //             {
    //                 \"course_name\": \"HP Future Ready AI\"
    //             }

    //             or

    //             {
    //                 \"course_name\": null
    //             }
    //             ";

    //             $response = Http::timeout(120)
    //                 ->post('http://127.0.0.1:11434/api/generate', [
    //                     'model' => 'llama3.1:latest',
    //                     'prompt' => $prompt,
    //                     'stream' => false
    //                 ]);

    //             if (!$response->successful()) {

    //                 $results[] = [
    //                     'certificate' => $attachment->name,
    //                     'matched' => false,
    //                     'reason' => 'Cannot connect to Ollama.',
    //                     'method' => 'AI'
    //                 ];

    //                 continue;
    //             }

    //             $ollama = $response->json();

    //             $answer = json_decode($ollama['response'], true);

    //             if (!$answer) {

    //                 $results[] = [
    //                     'certificate' => $attachment->name,
    //                     'matched' => false,
    //                     'reason' => 'Invalid JSON.',
    //                     'method' => 'AI'
    //                 ];

    //                 continue;
    //             }

    //             $results[] = [
    //                 'certificate' => $attachment->name,
    //                 'matched' => false,
    //                 'reason' => 'Need AI verification',
    //                 'method' => 'Pending Llama'
    //             ];
    //         }

    //         dd($results);
    //     } catch (\Exception $e) {

    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function Send_Verify_Training_Ticket($id)
    {
        try {

            $ticket = Training_Tickets_Model::with(
                'user_owner',
                'active_attachments',
                'training_courses'
            )->findOrFail($id);

            $parser = new Parser();

            /*
            |--------------------------------------------------------------------------
            | 1. Lấy danh sách course thuộc training_no
            |--------------------------------------------------------------------------
            */
            $courseList = Training_Courses_Model::where(
                'training_no',
                $ticket->training_no
            )
                ->pluck('course_name')
                ->filter()
                ->values();

            if ($courseList->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Training number này không có course nào.'
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | 2. Khởi tạo trạng thái cho từng course
            |--------------------------------------------------------------------------
            */
            $courseStatus = [];

            foreach ($courseList as $course) {
                $courseStatus[$course] = [
                    'matched' => false,
                    'certificate' => null,
                    'method' => null
                ];
            }

            $results = [];

            /*
            |--------------------------------------------------------------------------
            | 3. Chuẩn bị danh sách course cho prompt của Llama
            |--------------------------------------------------------------------------
            */
            $courseListForPrompt = $courseList
                ->map(function ($course, $index) {
                    return ($index + 1) . '. ' . $course;
                })
                ->implode("\n");

            /*
            |--------------------------------------------------------------------------
            | 4. Đọc từng certificate
            |--------------------------------------------------------------------------
            */
            foreach ($ticket->active_attachments as $attachment) {

                $pdfPath = Storage::disk('attachments')
                    ->path($attachment->file_path);

                /*
                |--------------------------------------------------------------------------
                | Đọc nội dung PDF
                |--------------------------------------------------------------------------
                */
                try {

                    $pdf = $parser->parseFile($pdfPath);
                    $text = trim($pdf->getText());

                } catch (\Exception $e) {

                    $results[] = [
                        'certificate' => $attachment->name,
                        'matched' => false,
                        'course' => null,
                        'reason' => 'Cannot extract PDF.',
                        'method' => null
                    ];

                    continue;
                }

                if ($text === '') {

                    $results[] = [
                        'certificate' => $attachment->name,
                        'matched' => false,
                        'course' => null,
                        'reason' => 'PDF contains no readable text.',
                        'method' => null
                    ];

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | 5. PHP exact match trước
                |--------------------------------------------------------------------------
                */
                $matchedCourse = null;
                $normalizedPdfText = $this->normalizeCourse($text);

                foreach ($courseList as $course) {

                    $normalizedCourse = $this->normalizeCourse($course);

                    if (
                        $normalizedCourse !== '' &&
                        stripos($normalizedPdfText, $normalizedCourse) !== false
                    ) {
                        $matchedCourse = $course;
                        break;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Nếu PHP tìm thấy course
                |--------------------------------------------------------------------------
                */
                if ($matchedCourse !== null) {

                    $courseStatus[$matchedCourse] = [
                        'matched' => true,
                        'certificate' => $attachment->name,
                        'method' => 'PHP'
                    ];

                    $results[] = [
                        'certificate' => $attachment->name,
                        'matched' => true,
                        'course' => $matchedCourse,
                        'reason' => null,
                        'method' => 'PHP'
                    ];

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | 6. PHP không tìm thấy thì gọi Llama
                |--------------------------------------------------------------------------
                */
                $prompt = <<<PROMPT
                You verify HP training certificates.

                Choose the course name that appears in the certificate from the allowed course list below.

                ALLOWED COURSES:
                {$courseListForPrompt}

                Return ONLY valid JSON in this exact format:

                {
                    "course_name": null
                }

                Rules:
                - course_name must exactly match one value from ALLOWED COURSES.
                - Do not create a new course name.
                - Do not explain.
                - Do not guess.
                - If the certificate does not clearly match any allowed course, return null.

                CERTIFICATE CONTENT:
                {$text}
                PROMPT;

                try {

                    $response = Http::timeout(120)
                        ->post('http://127.0.0.1:11434/api/generate', [
                            'model' => 'llama3.1:latest',
                            'prompt' => $prompt,
                            'stream' => false,
                            'format' => 'json'
                        ]);

                } catch (\Exception $e) {

                    $results[] = [
                        'certificate' => $attachment->name,
                        'matched' => false,
                        'course' => null,
                        'reason' => 'Cannot connect to Ollama.',
                        'method' => 'AI'
                    ];

                    continue;
                }

                if (!$response->successful()) {

                    $results[] = [
                        'certificate' => $attachment->name,
                        'matched' => false,
                        'course' => null,
                        'reason' => 'Ollama returned HTTP error.',
                        'method' => 'AI'
                    ];

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | 7. Đọc JSON Llama trả về
                |--------------------------------------------------------------------------
                */
                $ollama = $response->json();

                $rawAnswer = $ollama['response'] ?? null;

                if (!$rawAnswer) {

                    $results[] = [
                        'certificate' => $attachment->name,
                        'matched' => false,
                        'course' => null,
                        'reason' => 'Ollama returned an empty response.',
                        'method' => 'AI'
                    ];

                    continue;
                }

                $answer = json_decode($rawAnswer, true);

                if (
                    !is_array($answer) ||
                    !array_key_exists('course_name', $answer)
                ) {

                    $results[] = [
                        'certificate' => $attachment->name,
                        'matched' => false,
                        'course' => null,
                        'reason' => 'Ollama returned invalid JSON.',
                        'method' => 'AI',
                        'raw_response' => $rawAnswer
                    ];

                    continue;
                }

                $aiCourseName = $answer['course_name'];

                /*
                |--------------------------------------------------------------------------
                | 8. Llama không tìm thấy course
                |--------------------------------------------------------------------------
                */
                if (
                    $aiCourseName === null ||
                    trim((string) $aiCourseName) === ''
                ) {

                    $results[] = [
                        'certificate' => $attachment->name,
                        'matched' => false,
                        'course' => null,
                        'reason' => 'No matching course found in certificate.',
                        'method' => 'AI'
                    ];

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | 9. Kiểm tra course Llama trả về có nằm trong DB không
                |--------------------------------------------------------------------------
                */
                $validatedCourse = null;

                foreach ($courseList as $course) {

                    if (
                        $this->normalizeCourse($course) ===
                        $this->normalizeCourse($aiCourseName)
                    ) {
                        $validatedCourse = $course;
                        break;
                    }
                }

                if ($validatedCourse === null) {

                    $results[] = [
                        'certificate' => $attachment->name,
                        'matched' => false,
                        'course' => $aiCourseName,
                        'reason' => 'AI returned a course that is not in the allowed course list.',
                        'method' => 'AI'
                    ];

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | 10. Llama tìm thấy course hợp lệ
                |--------------------------------------------------------------------------
                */
                $courseStatus[$validatedCourse] = [
                    'matched' => true,
                    'certificate' => $attachment->name,
                    'method' => 'AI'
                ];

                $results[] = [
                    'certificate' => $attachment->name,
                    'matched' => true,
                    'course' => $validatedCourse,
                    'reason' => null,
                    'method' => 'AI'
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | 11. Tìm những course còn thiếu certificate
            |--------------------------------------------------------------------------
            */
            $missingCourses = [];

            foreach ($courseStatus as $course => $status) {

                if ($status['matched'] === false) {
                    $missingCourses[] = $course;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | 12. Nếu không thiếu course nào thì mark Completed
            |--------------------------------------------------------------------------
            */
            $completed = count($missingCourses) === 0;
            
            if ($completed) {

                /*
                * Nếu status của bạn là enum số, ví dụ 3 = Completed,
                * hãy đổi 'Completed' thành giá trị enum thực tế.
                */
                Training_Tickets_Model::where(
                    'training_no',
                    $ticket->training_no
                )->update([
                    'status' => '4'
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 13. Trả kết quả
            |--------------------------------------------------------------------------
            */
            return response()->json([
                'success' => true,
                'training_no' => $ticket->training_no,
                'completed' => $completed,
                'message' => $completed
                    ? 'All required certificates were found. Training marked as Completed.'
                    : 'Some required certificates are missing.',
                'certificate_results' => $results,
                'course_status' => $courseStatus,
                'missing_courses' => $missingCourses
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    private function normalizeCourse($text)
    {
        $text = strtolower($text);

        $removeWords = [
            "\r",
            "\n",
            "\t"
        ];

        // Chuẩn hóa khoảng trắng và dấu gạch
        $text = str_replace(
            ["\xc2\xa0", "-", "–", "—"],
            [" ", "-", "-", "-"],
            $text
        );

        $text = str_replace($removeWords, '', $text);

        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }





    public function Edit_Upload_Training_Ticket(Request $request, $id)
    {
        try {
            $ticket = Training_Tickets_Model::with('user_owner')->findOrFail($id);

            $validate_date = $request->validate([
                'attachments.*' => 'file|max:20480|mimes:pdf'
            ]);

            if ($request->hasFile('attachments')) { //Kiểm tra xem có file nào được upload lên không

                foreach ($request->file('attachments') as $file) { //Duyệt qua từng file được upload lên
                    $originalName = $file->getClientOriginalName();
                    $folderPath = '5/' . $ticket->user_id . '/' . $ticket->training_no;
                    $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục 'attachments' đã được cấu hình trong config/filesystems.php, với đường dẫn là 'attachments/1/{ticket_id}/{original_file_name}'

                    Attachments_Model::create([
                        'type_of_ticket' => 5, // Giả sử 1 là mã cho software ticket
                        'ticket_id' => $ticket->id,
                        'file_path' => $filePath,
                        'name' => $originalName, // Lưu tên gốc của file vào cơ sở dữ liệu
                        'user_id' => $ticket->user_id
                    ]);
                }
            }


            if ($request->has('delete_files')) {
                // Cập nhật tất cả các ID được tích chọn thành status = 0 trong 1 câu lệnh duy nhất
                Attachments_Model::whereIn('id', $request->input('delete_files'))->update(['status' => '0']);
            }

            tracking_info_service::add(
                $ticket->id,
                auth()->id(),
                5,
                'edited/uploaded certificates at'
            );

            return response()->json([
                'success' => true,
                'message' => 'Certificates edit/upload thành công ',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to request training due to ' . $e->getMessage(),
            ], 500);
        }
    }

    public function Confirm_Training_Completed($id)
    {
        try {
            $ticket = Training_Tickets_Model::with('user_owner')->findOrFail($id);
            tracking_info_service::add(
                $ticket->id,
                auth()->id(),
                5,
                'confirmed training completed at'
            );
            $ticket->status = '4';
            $ticket->save();


            return response()->json([
                'success' => true,
                'message' => 'Training completed confirmed ! ',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm due to ' . $e->getMessage(),
            ], 500);
        }
    }

    public function Reject_Training_Completed($id)
    {
        try {
            $ticket = Training_Tickets_Model::with('user_owner')->findOrFail($id);
            tracking_info_service::add(
                $ticket->id,
                auth()->id(),
                5,
                'rejected training completed at'
            );
            $ticket->status = '5';
            $ticket->save();

            return response()->json([
                'success' => true,
                'message' => 'Training rejected ! ',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject due to ' . $e->getMessage(),
            ], 500);
        }
    }
}
