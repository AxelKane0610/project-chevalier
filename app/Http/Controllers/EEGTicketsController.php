<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UserController;
use App\Models\EEG_Software_Ticket;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AttachmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Attachments_Model;
use App\Models\Comments_Model;
use App\Services\tracking_info_service;
use App\Models\User;
use App\Models\tracking_info_model;
use Illuminate\Support\Facades\Storage;

class EEGTicketsController extends Controller
{
    //
    public function Create_Software_Ticket(Request $request){
        try {
            $ticket_info_input = $request->validate([
                'ticket_receipt' => 'required',
                'support_type' => 'required',
                'priority' => 'required',
                'description' => 'required',
                'attachments.*' => 'file|max:5120|mimes:jpg,png,pdf,jpeg,xlsx'
            ]);

            $ticket_info_input['ticket_receipt'] = strip_tags($ticket_info_input['ticket_receipt']);//remove code xấu do người dùng input
            $ticket_info_input['support_type'] = strip_tags($ticket_info_input['support_type']);
            $ticket_info_input['priority'] = strip_tags($ticket_info_input['priority']);
            $ticket_info_input['description'] = strip_tags($ticket_info_input['description']);
            $ticket_info_input['user_id'] = auth()->id();

            
                $ticket = EEG_Software_Ticket::create($ticket_info_input); //Phải tạo model EEG_Software_Ticket để có thể sử dụng hàm create() này, và phải khai báo fillable trong model đó nữa
            
                if ($request->hasFile('attachments')) { //Kiểm tra xem có file nào được upload lên không

                    foreach ($request->file('attachments') as $file) { //Duyệt qua từng file được upload lên
                        $originalName = $file->getClientOriginalName();
                        $folderPath = '1/'.$ticket->id;
                        $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục '/'
                        
                        Attachments_Model::create([
                            'type_of_ticket' => 1, // Giả sử 1 là mã cho software ticket
                            'ticket_id' => $ticket->id,
                            'file_path' => $filePath,   
                            'name' => $originalName,// Lưu tên gốc của file vào cơ sở dữ liệu
                        ]);
                    }
                    
                }

                tracking_info_service::add(
                    $ticket->id, 
                    auth()->id(), 
                    1,
                    'created ticket at'
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Ticket created successfully !',
                    'ticket_id' => $ticket->id,
                    'ticket_receipt' => $ticket->ticket_receipt,
                    'support_type' => match ($ticket->support_type) 
                        {
                            '1' => 'Thêm mã part',
                            '2' => 'Rollback',
                            '3' => 'Hủy số phiếu',
                            '4' => 'Điều chỉnh thông tin',
                            default => 'Unknown',
                        },
                    'priority' => match ($ticket->priority) {
                            '1' => 'Normal',
                            '2' => 'Critical',
                            '3' => 'High',
                            '4' => 'Low',
                            default => 'Unknown',
                        },
                    'description' => $ticket->description,
                ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ticket due to ' .$e->getMessage(),
            ], 500);
        }
        


        
    }

    public function Show_Pending_Tickets(){ 
        if (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_TICKET_SW_ADMIN')) {
            $tickets = EEG_Software_Ticket::whereIn('status', ['1', '2', '3'])->get();
            $tickets_waiting_approval = EEG_Software_Ticket::where('status', 3)->get();
            return view('software-tickets-menu', compact('tickets', 'tickets_waiting_approval'));
        } 
        else {
            $tickets = EEG_Software_Ticket::where('user_id', auth()->id()) //lọc ra ticket của user đó
                ->whereIn('status', ['1', '2', '3']) // lọc ra ticket đang pending
                ->get();

            

            $tickets_waiting_approval = EEG_Software_Ticket::where('status', 3)
                ->whereHas('user_owner', function ($query) { //Lọc ra những ticket có user_owner có leader_id là id của user đang đăng nhập, tức là lọc ra những ticket của những user mà user đang đăng nhập là leader của họ, rồi mới lấy ra những ticket đó để trả về view
                    $query->where('leader_id', auth()->id());
                })
                ->get();
            
            return view('software-tickets-menu', compact('tickets', 'tickets_waiting_approval'));

            
        }
        
    }

    public function Show_Software_Ticket_Details($id, $type_of_ticket = 1){
        $ticket = EEG_Software_Ticket::with('user_owner', 'active_attachments','ticket_tracking_info','ticket_comments.attachments', 'ticket_comments.user')->findOrFail($id); 
        return view('software-tickets-menu-details', compact('ticket'));

    }

    public function Re_Open_Ticket($id){
        $ticket = EEG_Software_Ticket::with('user_owner')->findOrFail($id);
        try {
            if ($ticket->status == 4 || $ticket->status ==5 || $ticket->status == 6) {
                $ticket->status = 1; //đổi status thành "Đang chờ"
                tracking_info_service::add(
                    $ticket->id,
                    auth()->id(),
                    1, //1 là mã cho software ticket
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
                    'message' => 'Chỉ có ticket ở trạng thái "Completed", "Rejected" hoặc "Canceled" mới có thể re-open !',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ticket due to ' .$e->getMessage(),
            ], 500);
        }
        
        
    }

    public function Send_Approval_Request($id, Request $request){
        $ticket = EEG_Software_Ticket::with('user_owner', 'active_attachments')->findOrFail($id);
        try {
            if ($ticket->status == '1' || $ticket->status == '2') {
                $approval_type = $request->input('approval_type');

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
                
                $leader_email = User::where('id', $ticket->user_owner->leader_id)->value('email'); //Lấy email của leader của user owner của ticket này để gửi vào API, nếu không có leader thì trả về null
                // dd($leader_email);
                try 
                {
                    $send_approval = Http::post(config('services.api_service.sw_ticket_url'), [
                        'type_of_ticket' => 1,
                        'ticket_id' => $ticket->id,
                        'ticket_owner'   => $ticket->user_owner->fullname,
                        'receipt' => $ticket->ticket_receipt,
                        'description' => $ticket->description,
                        'attachments' => $attachments,
                        'approval_type' => $approval_type,
                        'leader_email' => $leader_email,
                    ]);
                    if ($send_approval->successful()) {
                        // Xử lý phản hồi thành công nếu cần
                        $ticket->status = 3;
                        $ticket->save();
                        tracking_info_service::add(
                            $ticket->id, 
                            auth()->id(), 
                            1,
                            'sent approval request at',
                        );
                        return response()->json([
                            'success' => true,
                            'message' => 'Approval request sent successfully',
                        ]);
                    } else {
                        // Xử lý lỗi nếu phản hồi không thành công
                        return response()->json([
                            'success' => false,
                            'message' => 'Failed to send approval request. API responded with status: ' . $send_approval->body(),
                        ], 500);
                    } 
                } catch (\Exception $e) {
                    // Xử lý lỗi nếu có
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to send approve due to ' . $e->getMessage(),
                    ], 500);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có ticket ở trạng thái "Open" hoặc "In Progress" mới có thể gửi approval request !',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send approve due to ' .$e->getMessage(),
            ], 500);
        }
        

    }

    public function Close_Software_Ticket(Request $request, $id){
        $ticket = EEG_Software_Ticket::with('user_owner','completed_by')->findOrFail($id);
        try {
            if ($ticket->status == '1' || $ticket->status == '2') {
                $ticket_info_input = $request->validate([
                    'ticket_status' => 'required',
                    'issue_owner' => 'required',
                    'ticket_comment' => 'nullable'
                ]);

                $ticket_info_input['ticket_status'] = strip_tags($ticket_info_input['ticket_status']);
                $ticket_info_input['issue_owner'] = strip_tags($ticket_info_input['issue_owner']);
                $ticket_info_input['ticket_comment'] = strip_tags($ticket_info_input['ticket_comment']);
                
                
                $ticket->status = $ticket_info_input['ticket_status'];
                $ticket->issue_owner = $ticket_info_input['issue_owner'];
                $ticket->completed_date = now();
                $ticket->ticket_completed_by = auth()->id();
                $ticket->save();

                switch ($ticket_info_input['ticket_status']) {
                    case '4':
                        $action = 'completed ticket at';
                        $status = 'Completed';
                        break;
                    case '5':
                        $action = 'rejected ticket at';
                        $status = 'Rejected';
                        break;
                    case '6':
                        $action = 'canceled ticket at';
                        $status = 'Canceled';
                        break;
                    default:
                        $action = 'updated ticket status to ' . $ticket_info_input['ticket_status'] . ' at';
                }
                tracking_info_service::add(
                    $ticket->id,
                    auth()->id(),
                    1, //1 là mã cho software ticket
                    $action,
                );

                $send_ticket_complete_notification = Http::post(config('services.api_service.sw_ticket_complete_url'), [
                    'ticket_id' => $ticket->id,
                    'ticket_owner_name'   => $ticket->user_owner->fullname,
                    'ticket_owner_email' => $ticket->user_owner->email,
                    'receipt' => $ticket->ticket_receipt,
                    'description' => $ticket->description,
                    'completed by' => $ticket->completed_by->fullname,
                    'ticket_comment' => $ticket_info_input['ticket_comment'],
                    'status' => $status,
                ]);
                if ($send_ticket_complete_notification->successful()) {
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Ticket closed and notification sent successfully',
                    ]);
                } else {
                    // Xử lý lỗi nếu phản hồi không thành công
                    return response()->json([
                        'success' => false,
                        'message' => 'Ticket closed but cant send notification due to: ' . $send_ticket_complete_notification->body(),
                    ], 500);
                } 
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có ticket ở trạng thái "Open" hoặc "In Progress" mới có thể đóng !',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to close ticket due to ' .$e->getMessage(),
            ], 500);
        } 
        
    }

    public function Edit_Software_Ticket(Request $request, $id){
        $ticket = EEG_Software_Ticket::with('user_owner')->findOrFail($id);
        try {
            if ($ticket->status == 1) {
                $ticket_info_input = $request->validate([
                    'ticket_receipt' => 'required',
                    'support_type' => 'required',
                    'priority' => 'required',
                    'description' => 'required',
                    'attachments.*' => 'file|max:5120|mimes:jpg,png,pdf,jpeg,xlsx'
                ]);

                $ticket_info_input['ticket_receipt'] = trim(strip_tags($ticket_info_input['ticket_receipt']));
                $ticket_info_input['support_type'] = trim(strip_tags($ticket_info_input['support_type']));
                $ticket_info_input['priority'] = trim(strip_tags($ticket_info_input['priority']));
                $ticket_info_input['description'] = trim(strip_tags($ticket_info_input['description']));

            
                $ticket->ticket_receipt = $ticket_info_input['ticket_receipt'];
                $ticket->support_type = $ticket_info_input['support_type'];
                $ticket->priority = $ticket_info_input['priority'];
                $ticket->description = $ticket_info_input['description'];
                
                $ticket->save();
                tracking_info_service::add(
                    $ticket->id, 
                    auth()->id(), 
                    1,
                    'edited ticket at'
                );
                

                

                if ($request->has('delete_files')) {
                // Cập nhật tất cả các ID được tích chọn thành status = 0 trong 1 câu lệnh duy nhất
                    Attachments_Model::whereIn('id', $request->input('delete_files'))->update(['status' => '0']);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Ticket edited successfully',
                ]);
            } else return response()->json([
                'success' => false,
                'message' => 'Chỉ có ticket đang ở trạng thái "Open" mới được phép edit !',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to edit ticket due to ' .$e->getMessage(),
            ], 500);
        }
        

        


    }

    public function Add_Comment_Software_Ticket(Request $request, $id){
        $comment_info_input = $request->validate([
            'comment' => 'required_without_all:attachments|string|nullable',
            'attachments' => 'required_without_all:comment|array|nullable',
            'attachments.*' => 'file|max:20480|mimes:jpg,jpeg,png,pdf,xlsx,docx',
        ]);

        $comment_info_input['comment'] = strip_tags($comment_info_input['comment']);
        $comment_info_input['ticket_id'] = $id;
        $comment_info_input['type_of_ticket'] = 1; //1 là mã cho software ticket
        $comment_info_input['user_id'] = auth()->id();
        
        
        $comment = Comments_Model::create($comment_info_input);
        

        if($request->hasFile('attachments'))
        {
            foreach($request->file('attachments') as $file)
            {
                $originalName = $file->getClientOriginalName();
                $folderPath = '1/'.$id;
                $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục 'attachments' đã được cấu hình trong config/filesystems.php, với đường dẫn là 'attachments/1/{ticket_id}/{original_file_name}'
                
                Attachments_Model::create([
                    'type_of_ticket' => 1,
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

    public function Approve_Ticket($id){
        $ticket = EEG_Software_Ticket::with('user_owner')->findOrFail($id);
        try {
            if ($ticket->status != 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể approve ticket. Ticket không ở trạng thái đang chờ phê duyệt.',
                ], 400);
            }
            else {
                $ticket->status = 2;
                $ticket->save();

                tracking_info_service::add(
                    $ticket->id,
                    auth()->id(),
                    1, //1 là mã cho software ticket
                    'approved ticket at',
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Ticket approved !',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ticket due to ' .$e->getMessage(),
            ], 500);
        }
        
    }

    public function Reject_Ticket($id){
        $ticket = EEG_Software_Ticket::with('user_owner')->findOrFail($id);
        try {
            if ($ticket->status != 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể reject ticket. Ticket không ở trạng thái đang chờ phê duyệt.',
                ], 400);
            }
            else {
                $ticket->status = 2;
                $ticket->save();

                tracking_info_service::add(
                    $ticket->id,
                    auth()->id(),
                    1, //1 là mã cho software ticket
                    'rejected ticket at',
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Ticket rejected !',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ticket due to ' .$e->getMessage(),
            ], 500);
        }
        
    }

    public function Change_Ticket_Software_Status_To_In_Progress($id) {
        $ticket = EEG_Software_Ticket::findOrFail($id);
        try {
            if ($ticket->status == 1) {
                $ticket->status = 2; // Giả sử 2 là mã cho trạng thái "In Progress"
                $ticket->save();

                tracking_info_service::add(
                    $ticket->id, 
                    auth()->id(), 
                    1,
                    'changed status to In Progress at'
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Ticket status updated to In Progress !',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có ticket ở trạng thái "Open" mới có thể chuyển sang "In Progress" !',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to change ticket status due to ' .$e->getMessage(),
            ], 500);
        } 
    }
    

}
