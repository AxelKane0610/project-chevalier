<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
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
use Illuminate\Support\Facades\Log;

class VerifyTrainingTicketJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    protected $ticketId;
    public $timeout = 300;

    public function __construct($id)
    {
        //
        $this->ticketId = $id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        try {

            $ticket = Training_Tickets_Model::with(
                'user_owner',
                'active_attachments',
                'training_courses'
            )->findOrFail($this->ticketId);

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

            // if ($courseList->isEmpty()) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Training number này không có course nào.'
            //     ], 422);
            // }

            if ($courseList->isEmpty()) {
                // Cập nhật trạng thái ticket thành lỗi
                Training_Tickets_Model::where('id', $this->ticketId)->update([
                    'status' => 'error_no_course' // Thay bằng mã lỗi của bạn
                ]);
                
                // Ghi comment để user biết
                Comments_Model::create([
                    'ticket_id' => $this->ticketId,
                    'type_of_ticket' => 5,
                    'user_id' => 10,
                    'comment'=> 'Lỗi: Training number này không có course nào.'

                ]);
                \Log::warning("Ticket ID {$this->ticketId}: Training number không có course nào.");
                
                // Kết thúc tiến trình
                return;
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

                CRITICAL INSTRUCTION: 
                - The allowed course list below may contain typos or spelling errors (for example, "Engag" instead of "Engage"). 
                - You MUST match the certificate to the correct course, but your output course_name **MUST BE COPIED VERBATIM (EXACTLY character-for-character)** as it is written in the ALLOWED COURSES list below, even if it has a typo. Do not fix typos in the allowed list.

                Also, provide a confidence score (an integer from 0 to 100) indicating how closely the certificate matches the course.
                
                ALLOWED COURSES:
                {$courseListForPrompt}

                Return ONLY valid JSON in this exact format:

                {
                    "course_name": null,
                    "score": 0
                }

                Rules:
                - course_name must be copied character-for-character from ALLOWED COURSES.
                - Do not create a new course name.
                - score must be an integer between 0 and 100.
                - Do not explain.

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
                    !array_key_exists('course_name', $answer) ||
                    !array_key_exists('score', $answer)
                ) {

                    $results[] = [
                        'certificate' => $attachment->name,
                        'matched' => false,
                        'course' => null,
                        'reason' => 'Ollama returned invalid JSON (missing score or course_name).',
                        'method' => 'AI',
                        'raw_response' => $rawAnswer
                    ];

                    continue;
                }

                $aiCourseName = $answer['course_name'];
                $aiScore = (int) ($answer['score'] ?? 0);

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
                        'method' => 'AI',
                        'score' => $aiScore
                    ];

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | 9. Kiểm tra course Llama trả về có nằm trong DB không
                |--------------------------------------------------------------------------
                */
                // $validatedCourse = null;

                // foreach ($courseList as $course) {

                //     if (
                //         $this->normalizeCourse($course) ===
                //         $this->normalizeCourse($aiCourseName)
                //     ) {
                //         $validatedCourse = $course;
                //         break;
                //     }
                // }

                // if ($validatedCourse === null) {

                //     $results[] = [
                //         'certificate' => $attachment->name,
                //         'matched' => false,
                //         'course' => $aiCourseName,
                //         'reason' => 'AI returned a course that is not in the allowed course list.',
                //         'method' => 'AI',
                //         'score' => $aiScore
                //     ];

                //     continue;
                // }

                $validatedCourse = null;

                foreach ($courseList as $course) {
                    $normDb = $this->normalizeCourse($course);
                    $normAi = $this->normalizeCourse($aiCourseName);

                    // 1. So khớp chính xác tuyệt đối
                    if ($normDb === $normAi) {
                        $validatedCourse = $course;
                        break;
                    }

                    // 2. Tính độ tương đồng (%) để bỏ qua lỗi chính tả nhẹ (ví dụ: engag vs engage)
                    similar_text($normDb, $normAi, $percent);
                    if ($percent >= 85) { // Nếu độ giống nhau từ 85% trở lên thì coi như khớp
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
                        'method' => 'AI',
                        'score' => $aiScore
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
                    'method' => 'AI',
                    'score' => $aiScore
                ];

                $results[] = [
                    'certificate' => $attachment->name,
                    'matched' => true,
                    'course' => $validatedCourse,
                    'reason' => null,
                    'method' => 'AI',
                    'score' => $aiScore
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
            
            // if ($completed) {

            //     /*
            //     * Nếu status của bạn là enum số, ví dụ 3 = Completed,
            //     * hãy đổi 'Completed' thành giá trị enum thực tế.
            //     */
            //     Training_Tickets_Model::where(
            //         'training_no',
            //         $ticket->training_no
            //     )->update([
            //         'status' => '4'
            //     ]);
            // }

            /*
            |--------------------------------------------------------------------------
            | 13. Trả kết quả
            |--------------------------------------------------------------------------
            */
            // return response()->json([
            //     'success' => true,
            //     'training_no' => $ticket->training_no,
            //     'completed' => $completed,
            //     'message' => $completed
            //         ? 'All required certificates were found. Training marked as Completed.'
            //         : 'Some required certificates are missing.',
            //     'certificate_results' => $results,
            //     'course_status' => $courseStatus,
            //     'missing_courses' => $missingCourses
            // ]);

            if ($completed) {
                // Training_Tickets_Model::where('training_no', $ticket->training_no)->update([
                //     'status' => '4'
                // ]);
                $ticket->status = '4';
                $ticket->save();
                Comments_Model::create([
                    'ticket_id' => $this->ticketId,
                    'type_of_ticket' => 5,
                    'user_id' => 10,
                    'comment'=> 'Hệ thống xác nhận hoàn thành, có thể check log dưới đây: ' . "\n" . json_encode($results)

                ]);
                tracking_info_service::add(
                    $this->ticketId,
                    10,
                    5,
                    'confirmed training completed at'
                );
                \Log::info("Ticket ID {$this->ticketId} đã verify thành công.");
            } else {
                Training_Tickets_Model::where('training_no', $ticket->training_no)->update([
                    'status' => '5', // Mã status Báo thiếu chứng chỉ
                ]);
                Comments_Model::create([
                    'ticket_id' => $this->ticketId,
                    'type_of_ticket' => 5,
                    'user_id' => 10,
                    'comment'=> 'Complete training thất bại do thiếu chứng chỉ, có thể check log dưới đây: ' . "\n" . json_encode($results) . "\nThiếu các khóa học:\n" . json_encode($missingCourses)

                ]);
                tracking_info_service::add(
                    $this->ticketId,
                    10,
                    5,
                    'rejected training at'
                );
                \Log::info("Ticket ID {$this->ticketId} verify thất bại do thiếu chứng chỉ.");
            }

        } catch (\Exception $e) {

            // return response()->json([
            //     'success' => false,
            //     'message' => $e->getMessage(),
            //     'line' => $e->getLine(),
            //     'file' => $e->getFile()
            // ], 500);
            Comments_Model::create([
                'ticket_id' => $this->ticketId,
                'type_of_ticket' => 5,
                'user_id' => 10,
                'comment'=> 'Lỗi hệ thống: ' .$e->getMessage()

            ]);
            
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
}
