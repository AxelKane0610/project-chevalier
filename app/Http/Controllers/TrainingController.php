<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\tracking_info_service;
use App\Models\Training_Tickets_Model;
use App\Models\Training_Courses_Model;
use App\Models\User;
use App\Models\Comments_Model;
use App\Models\Attachments_Model;
use Illuminate\Support\Facades\DB;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Http;
use App\Jobs\VerifyTrainingTicketJob;


class TrainingController extends Controller
{
    //
    public function index()
    {
        $all_training_courses = Training_Courses_Model::query()->paginate(10);
        $all_training_no_numbers = Training_Courses_Model::query()
        ->select('training_no')
        ->distinct()
        ->orderBy('training_no')
        ->pluck('training_no');
        $all_your_training_tickets = Training_Tickets_Model::where('user_id', auth()->id())->paginate(10);
        $pending_tickets = Training_Tickets_Model::whereIn('status', ['1', '2', '3'])->where('user_id', auth()->id())->get();
        if (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_TRAINING_ADMIN')) {
            $all_country_team_training_tickets = Training_Tickets_Model::query()->paginate(10);
            return view('submit-training-menu', compact('pending_tickets', 'all_your_training_tickets', 'all_country_team_training_tickets', 'all_training_courses', 'all_training_no_numbers'));
        } else {

            $all_country_team_training_tickets = Training_Tickets_Model::whereHas('user_owner', function ($query) { //Lọc ra những ticket có user_owner có leader_id là id của user đang đăng nhập, tức là lọc ra những ticket của những user mà user đang đăng nhập là leader của họ, rồi mới lấy ra những ticket đó để trả về view
                $query->where('leader_id', auth()->id());
            })->paginate(10);

            return view('submit-training-menu', compact('pending_tickets', 'all_your_training_tickets', 'all_country_team_training_tickets', 'all_training_courses', 'all_training_no_numbers'));
        }
    }

    private function trainingTicketQuery()
    {
        $query = Training_Tickets_Model::query();

        if (!auth()->user()->hasRole('ROLE_SUPER_ADMIN') || !auth()->user()->hasRole('ROLE_TRAINING_ADMIN')) {
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

    public function Filter_Your_Training_Tickets_Table(Request $request)
    {
        $query = Training_Tickets_Model::query()->where('user_id', auth()->id());;


        // Training No Filter
        if ($request->filled('training_no')) {
            $query->where('training_no', $request->training_no);
        }

        if ($request->filled('status')) {

            $query->where('status', $request->status);
        }

        $all_your_training_tickets = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {

            return view(
                'tables.all-individual-training-tickets',
                compact('all_your_training_tickets')
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
                $courses = [];


                // Lưu từng dòng
                foreach ($request->course_id as $index => $courseId) {
                    $courseName = $request->course_name[$index];
                    Training_Courses_Model::create([
                        'training_no' => $trainingNo,
                        'course_id' => $courseId,
                        'course_name' => $courseName,
                        'start_date' => $request->start_date,
                        'end_date' => $request->end_date,
                    ]);

                    $courses[] = [
                        'course_id' => $courseId,
                        'course_name' => $courseName,
                    ];
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

                $emails = $users
                ->pluck('email')
                ->filter()
                ->unique()
                ->values()
                ->toArray();

                $payload = [
                    'training_no' => $trainingNo,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'emails' => $emails,
                    'courses' => $courses,
                ];

                Http::post(
                    config('services.api_service.request_training_url'),
                    $payload
                );
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


    public function Send_Verify_Training_Ticket($id)
    {
        try {
            $ticket = Training_Tickets_Model::with('user_owner', 'active_attachments')->findOrFail($id);
            $training_courses = Training_Courses_Model::where('training_no', $ticket->training_no);

            if($ticket->active_attachments()->count() == $training_courses->count() )
            {
                VerifyTrainingTicketJob::dispatch($id);
                tracking_info_service::add(
                    $ticket->id,
                    auth()->id(),
                    5,
                    'sent verify training at'
                );
                return response()->json([
                    'success' => true,
                    'message' => 'Hệ thống đã tiếp nhận chứng chỉ và đang kiểm tra ngầm. Vui lòng tải lại trang sau ít phút để xem kết quả.'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng kiểm tra lại certificate do số lượng nộp không trùng khớp với số lượng course.'
                ], 500);
            }
            
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi khởi tạo quá trình kiểm tra: ' . $e->getMessage()
            ], 500);
        }
    }




    public function Edit_Upload_Training_Ticket(Request $request, $id)
    {
        try {
            $ticket = Training_Tickets_Model::with('user_owner')->findOrFail($id);

            $validate_data = $request->validate([
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
                'message' => 'Failed to edit/upload certificates due to ' . $e->getMessage(),
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
