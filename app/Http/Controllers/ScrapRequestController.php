<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Scrap_Request_Model;
use App\Models\Attachments_Model;
use App\Models\Comments_Model;
use App\Models\User;
use Carbon\Carbon;
use App\Services\tracking_info_service;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ScrapRequestController extends Controller
{
    //
    public function index(){ 
        if (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_SCRAP_LV1_APPROVER') || auth()->user()->hasRole('ROLE_SCRAP_LV2_APPROVER')) {
            $pending_tickets = Scrap_Request_Model::whereIn('status', ['1', '2', '3'])->paginate(10);
            $all_tickets = Scrap_Request_Model::query()->orderByDesc('created_at')->paginate(10);
            return view('scrap-request-menu', compact('pending_tickets', 'all_tickets'));
        } 
        else {
            $pending_tickets = Scrap_Request_Model::where('user_id', auth()->id()) //lọc ra ticket của user đó
                ->whereIn('status', ['1', '2', '3']) // lọc ra ticket đang pending
                ->paginate(10);

            
            $all_tickets = Scrap_Request_Model::where('user_id', auth()->id())->orderByDesc('created_at')->paginate(10);
            return view('scrap-request-menu', compact('pending_tickets', 'all_tickets'));

            
        }
        
    }

    public function Create_Scrap_Ticket(Request $request) {
        try {
            $new_ticket = $request->validate([
                'scrap_type' => 'required',
                'scrap_date' => 'required',
                'scrap_description' => 'required',
                'attachments.*' => 'file|max:20480|mimes:jpg,png,pdf,jpeg,xlsx'

            ]);

            $new_ticket['user_id'] = auth()->id();
            $new_ticket['status'] = '2';
            $new_ticket['scrap_type'] = strip_tags($new_ticket['scrap_type']);
            $new_ticket['scrap_date'] = strip_tags($new_ticket['scrap_date']);
            $new_ticket['scrap_description'] = strip_tags($new_ticket['scrap_description']);

            $new_ticket = Scrap_Request_Model::create($new_ticket);
            if ($request->hasFile('attachments')) { 

                foreach ($request->file('attachments') as $file) { 
                    $originalName = $file->getClientOriginalName();
                    $folderPath = '14/'.$new_ticket->id;
                    $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); 
                    
                    Attachments_Model::create([
                        'type_of_ticket' => 14, 
                        'ticket_id' => $new_ticket->id,
                        'file_path' => $filePath,   
                        'name' => $originalName,
                    ]);
                }
                
            }


            tracking_info_service::add(
                $new_ticket->id, 
                auth()->id(), 
                14,
                'created ticket at'
            );

            $new_ticket = Scrap_Request_Model::with('user_owner', 'active_attachments')->findOrFail($new_ticket->id);
            $attachments = $new_ticket->active_attachments->map(function ($file) {
                return [
                    'fileName' => basename($file->file_path),
                    'fileContent' => base64_encode(
                        Storage::disk('attachments')->get(
                            $file->file_path
                        )
                    ),
                ];
            });
            $date = Carbon::parse($new_ticket->scrap_date);

            try {
                $send_approve = Http::post(config('services.api_service.send_approve_scrap_ticket_url'), [
                    'ticket_id' => $new_ticket->id,
                    'user_name' => $new_ticket->user_owner->fullname ?? 'N/A',
                    'email_address' => $new_ticket->user_owner->email ?? 'N/A',
                    'leader_email_address' => User::where('id', $new_ticket->user_owner->leader_id)->value('email'),
                    'scrap_date' => 'tháng ' . $date->format('n') . ' năm ' . $date->format('Y'),
                    'scrap_type' => $new_ticket->scrap_type,
                    'scrap_description' => $new_ticket->scrap_description,
                    'attachments' => $attachments,
                ]);
                if ($send_approve->successful()) {
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Tạo ticket thành công',
                    ]);
                } else {
                    // Xử lý lỗi nếu phản hồi không thành công
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to send approval request. API responded with status: ' . $send_approve->body(),
                    ], 500);
                } 
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tạo ticket lỗi do ' .$e->getMessage(),
                ], 500);
            }
            

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tạo ticket lỗi do ' .$e->getMessage(),
            ], 500);
        }
    }

    public function Ticket_Details($id) {
        $ticket = Scrap_Request_Model::with(['user_owner', 'active_attachments','ticket_tracking_info','ticket_comments.attachments', 'ticket_comments.user'])->findOrFail($id);
        return view('scrap-request-details', compact('ticket'));
    }

    public function Add_Comment_Scrap_Ticket(Request $request, $id) {
        $ticket = Scrap_Request_Model::findOrFail($id);

        $validatedData = $request->validate([
            'comment' => 'required_without_all:attachments|string|nullable',
            'attachments' => 'required_without_all:comment|array|nullable',
            'attachments.*' => 'file|max:20480|mimes:jpg,jpeg,png,pdf,xlsx,docx',
        ]);

        $comment = Comments_Model::create([
            'type_of_ticket' => 14,
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'comment' => strip_tags($validatedData['comment']),
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $originalName = $file->getClientOriginalName();
                $folderPath = '14/'.$id;
                $filePath = $file->storeAs($folderPath, $originalName, 'attachments');

                Attachments_Model::create([
                    'type_of_ticket' => 14,
                    'ticket_id' => $id,
                    'comment_id' => $comment->id,
                    'file_path' => $filePath,
                    'name' => $originalName,
                ]);
            }
        }

        

        return back()->with('success');
    }

    public function Edit_Scrap_Ticket_Details (Request $request, $id) {
        $ticket = Scrap_Request_Model::findOrFail($id);
        try {
            if ($ticket->status != '1' && auth()->user()->hasRole('ROLE_SCRAP_USER')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể edit ticket do ticket đã chuyển mục',
                ], 200);
            } else { 
                $validatedData = $request->validate([
                    'scrap_type' => 'required',
                    'scrap_date' => 'required',
                    'scrap_description' => 'required',
                    'attachments.*' => 'file|max:20480|mimes:jpg,png,pdf,jpeg,xlsx'
                ]);

                $ticket->update([
                    'scrap_type' => strip_tags($validatedData['scrap_type']),
                    'scrap_date' => strip_tags($validatedData['scrap_date']),
                    'scrap_description' => strip_tags($validatedData['scrap_description']),
                ]);

                tracking_info_service::add(
                    $ticket->id, 
                    auth()->id(), 
                    14,
                    'edited ticket at'
                );

                if ($request->hasFile('attachments')) { //Kiểm tra xem có file nào được upload lên không

                    foreach ($request->file('attachments') as $file) { //Duyệt qua từng file được upload lên
                        $originalName = $file->getClientOriginalName();
                        $folderPath = '14/'.$id;
                        $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục 'attachments' đã được cấu hình trong config/filesystems.php, với đường dẫn là 'attachments/1/{ticket_id}/{original_file_name}'
                        
                        Attachments_Model::create([
                            'type_of_ticket' => 14, 
                            'ticket_id' => $ticket->id,
                            'file_path' => $filePath,
                            'name' => $originalName,// Lưu tên gốc của file vào cơ sở dữ liệu
                        ]);
                    }
                    
                }

                if ($request->has('delete_files')) {
                // Cập nhật tất cả các ID được tích chọn thành status = 0 trong 1 câu lệnh duy nhất
                    Attachments_Model::whereIn('id', $request->input('delete_files'))->update(['status' => '0']);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Ticket chỉnh sửa thành công ',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update ticket details due to: '.$e->getMessage(),
            ], 500);
        }
    }

    public function Send_Approve_Scrap_Request ($id) {
        $ticket = Scrap_Request_Model::findOrFail($id);
        $ticket->status = '2';
        $ticket->save();
        tracking_info_service::add(
            $ticket->id, 
            auth()->id(), 
            14,
            're-sent approve ticket at'
        );
        $date = Carbon::parse($ticket->scrap_date);
        $attachments = $ticket->active_attachments->map(function ($file) {
            return [
                'fileName' => basename($file->file_path),
                'fileContent' => base64_encode(
                    Storage::disk('attachments')->get(
                        $file->file_path
                    )
                ),
            ];
        });

        try {
            $send_approve = Http::post(config('services.api_service.send_approve_scrap_ticket_url'), [
                'ticket_id' => $ticket->id,
                'user_name' => $ticket->user_owner->fullname ?? 'N/A',
                'email_address' => $ticket->user_owner->email ?? 'N/A',
                'leader_email_address' => User::where('id', $ticket->user_owner->leader_id)->value('email'),
                'scrap_date' => 'tháng ' . $date->format('n') . ' năm ' . $date->format('Y'),
                'scrap_type' => $ticket->scrap_type,
                'scrap_description' => $ticket->scrap_description,
                'attachments' => $attachments,
            ]);
            if ($send_approve->successful()) {
                
                return response()->json([
                    'success' => true,
                    'message' => 'Gửi request thành công, chờ leader duyệt',
                ]);
            } else {
                // Xử lý lỗi nếu phản hồi không thành công
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send approval request. API responded with status: ' . $send_approve->body(),
                ], 500);
            } 
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gửi request fail bị lỗi do ' .$e->getMessage(),
            ], 500);
        }
    }


    public function Filter_All_Scrap_Tickets(Request $request) {
        if (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_SCRAP_LV1_APPROVER') || auth()->user()->hasRole('ROLE_SCRAP_LV2_APPROVER')) {
            $query = Scrap_Request_Model::query();
        } else {
            $query = Scrap_Request_Model::where('user_id', auth()->id())->orderBy('created_at', 'desc');
        }
        

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('scrap_type', 'like', "%{$search}%")
                    ->orWhere('scrap_description', 'like', "%{$search}%")
                    ->orWhereHas('user_owner', function ($user) use ($search) {
                        $user->where('fullname', 'like', "%{$search}%");
                    });
                    
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        

        // Phân trang kết quả
        $all_tickets = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('tables.all-scrap-tickets-table', compact('all_tickets'))->render();
        }

    }

    public function Re_Open_Scrap_Ticket ($id) {
        $ticket = Scrap_Request_Model::with('user_owner')->findOrFail($id);
        try {
            if ($ticket->status == '5') {
                $ticket->status = 1; //đổi status thành "Đang chờ"
                tracking_info_service::add(
                    $ticket->id,
                    auth()->id(),
                    14, //1 là mã cho software ticket
                    're-opened ticket at',
                );
                $ticket->save();
                return response()->json([
                    'success' => true,
                    'message' => 'Ticket re-opened successfully',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có ticket ở trạng thái "Rejected" mới có thể re-open !',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to re-open ticket due to ' .$e->getMessage(),
            ], 500);
        }
    }


}
