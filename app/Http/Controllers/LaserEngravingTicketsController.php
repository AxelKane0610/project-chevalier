<?php

namespace App\Http\Controllers;

use App\Models\Laser_Engraving_Tickets_Model;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use App\Services\tracking_info_service;
use App\Models\Attachments_Model;
use App\Models\Comments_Model;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;



class LaserEngravingTicketsController extends Controller
{
    //
    public function Show_Pending_Tickets()
    {
        // $tickets = Laser_Engraving_Tickets_Model::all();
        // return view('laser-engraving-menu', compact('tickets'));
        if (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_LASER_ENGRAVING_ADMIN')) {
            $tickets = Laser_Engraving_Tickets_Model::whereIn('status', ['1', '2'])->get();
            $all_tickets = Laser_Engraving_Tickets_Model::all();
            return view('laser-engraving-menu', compact('tickets', 'all_tickets'));
        } 
        else {
            $tickets = Laser_Engraving_Tickets_Model::where('user_id', auth()->id()) //lọc ra ticket của user đó
                ->whereIn('status', ['1', '2']) // lọc ra ticket đang pending
                ->get();
            
            $all_tickets = Laser_Engraving_Tickets_Model::where('user_id',auth()->id())->get();
            return view('laser-engraving-menu', compact('tickets', 'all_tickets'));
        }
    }

    public function Create_Laser_Engraving_Ticket(Request $request)
    {
        try {
        // Validate dữ liệu đầu vào
            $validatedData = $request->validate([
                'ticket_receipt' => 'required|string|max:255',
                'priority' => 'required|in:1,2,3,4',
                'info_base' => 'required|string|max:255',
                'description' => 'required|string',
                'attachments.*' => 'file|max:20480|mimes:jpg,png,pdf,jpeg,xlsx,gif,cdr'
            ]);

            $validatedData['ticket_receipt'] = strip_tags($validatedData['ticket_receipt']);
            $validatedData['info_base'] = strip_tags($validatedData['info_base']);
            $validatedData['description'] = strip_tags($validatedData['description']);

        // Tạo một ticket mới trong cơ sở dữ liệu
        
            $ticket = Laser_Engraving_Tickets_Model::create([
                'user_id' => auth()->id(), // Lấy ID của người dùng hiện tại
                'ticket_receipt' => $validatedData['ticket_receipt'],
                'priority' => $validatedData['priority'],
                'info_base' => $validatedData['info_base'],
                'description' => $validatedData['description'],
                // Thêm các trường khác nếu cần thiết
            ]);

            if ($request->hasFile('attachments')) { //Kiểm tra xem có file nào được upload lên không

                foreach ($request->file('attachments') as $file) { //Duyệt qua từng file được upload lên
                    $originalName = $file->getClientOriginalName();
                    $folderPath = '3/'.$ticket->id;
                    $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục '/'
                    
                    Attachments_Model::create([
                        'type_of_ticket' => 3, // Giả sử 3 là mã cho laser engraving ticket
                        'ticket_id' => $ticket->id,
                        'file_path' => $filePath,   
                        'name' => $originalName,// Lưu tên gốc của file vào cơ sở dữ liệu
                    ]);
                }
                
            }

            tracking_info_service::add(
                $ticket->id, 
                auth()->id(), 
                3,
                'created ticket at'
            );

            // Trả về phản hồi (có thể là JSON hoặc chuyển hướng)
            return response()->json([
                'message' => 'Ticket created successfully',
                'id' =>  $ticket->id,
                'receipt' => $ticket->ticket_receipt,
                'priority' => match ($ticket->priority) 
                {
                    '1' => 'Not started',
                    '2' => 'In progress',
                    '3' => 'Completed',
                    '4' => 'Rejected',
                    default => 'Unknown',
                },
                'info_base' => $ticket->info_base,
                'description' => $ticket->description,
                'success' => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ticket due to ' .$e->getMessage(),
            ], 500);
        }

    }

    public function Show_Laser_Engraving_Ticket_Details($id)
    {
        $ticket = Laser_Engraving_Tickets_Model::with('user_owner', 'active_attachments')->findOrFail($id);
        return view('laser-engraving-menu-details', compact('ticket'));
    }

    public function Edit_Laser_Engraving_Ticket(Request $request, $id)
    {
        // Validate dữ liệu đầu vào
        $validatedData = $request->validate([
            'ticket_receipt' => 'required',
            'priority' => 'required',
            'info_base' => 'required',
            'description' => 'required',
            'attachments.*' => 'file|max:20480|mimes:jpg,png,pdf,jpeg,xlsx'
        ]);
        $ticket = Laser_Engraving_Tickets_Model::with('user_owner')->findOrFail($id);
        try {
            if ($ticket->status == 1) {
            $validatedData['ticket_receipt'] = strip_tags($validatedData['ticket_receipt']);
            $validatedData['info_base'] = strip_tags($validatedData['info_base']);
            $validatedData['description'] = strip_tags($validatedData['description']);

            // Cập nhật thông tin ticket
            
            $ticket->ticket_receipt = $validatedData['ticket_receipt'];
            $ticket->priority = $validatedData['priority'];
            $ticket->info_base = $validatedData['info_base'];
            $ticket->description = $validatedData['description'];
            
            $ticket->save();


            if ($request->hasFile('attachments')) { //Kiểm tra xem có file nào được upload lên không

                foreach ($request->file('attachments') as $file) { //Duyệt qua từng file được upload lên
                    $originalName = $file->getClientOriginalName();
                    $folderPath = '3/'.$ticket->id;
                    $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục '/'
                    
                    Attachments_Model::create([
                        'type_of_ticket' => 3, // Giả sử 3 là mã cho laser engraving ticket
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

            
            tracking_info_service::add(
                $ticket->id, 
                auth()->id(), 
                3,
                'edited ticket at'
            );
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
                'message' => 'Failed to create ticket due to ' .$e->getMessage(),
            ], 500);
        }
    }

    public function Add_Comment_Laser_Engraving_Ticket(Request $request, $id)
    {
        $validatedData = $request->validate([
            'comment' => 'required_without_all:attachments|string|nullable',
            'attachments' => 'required_without_all:comment|array|nullable',
            'attachments.*' => 'file|max:20480|mimes:jpg,png,pdf,jpeg,xlsx,gif,cdr',
        ]);

        $validatedData['comment'] = strip_tags($validatedData['comment']);
        $validatedData['ticket_id'] = $id;
        $validatedData['type_of_ticket'] = 3; //3 là mã cho laser engraving ticket
        $validatedData['user_id'] = auth()->id();

        $comment = Comments_Model::create($validatedData);

        if($request->hasFile('attachments'))
        {
            foreach($request->file('attachments') as $file)
            {
                $originalName = $file->getClientOriginalName();
                $folderPath = '3/'.$id;
                $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục 'attachments' đã được cấu hình trong config/filesystems.php, với đường dẫn là 'attachments/3/{ticket_id}/{original_file_name}'
                
                Attachments_Model::create([
                    'type_of_ticket' => 3,
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

    public function Change_Laser_Engraving_Status_To_In_Progress($id)
    {
        $ticket = Laser_Engraving_Tickets_Model::findOrFail($id);
        $ticket->status = 2; // Giả sử 2 là mã cho trạng thái "In Progress"
        $ticket->save();

        tracking_info_service::add(
            $ticket->id, 
            auth()->id(), 
            3,
            'changed status to In Progress at'
        );

        return response()->json([
            'success' => true,
            'message' => 'Ticket status updated to In Progress !',
        ]);
    }

    
    public function Close_Laser_Engraving_Ticket(Request $request, $id)
    {
        try {
            $ticket = Laser_Engraving_Tickets_Model::with('user_owner')->findOrFail($id);
            $validatedData = $request->validate([
                'ticket_status' => 'required',
                'ticket_comment' => 'nullable|string',
                'attachments.*' => 'file|max:20480|mimes:jpg,png,pdf,jpeg,xlsx,gif,cdr'
            ]);

            $validatedData['ticket_comment'] = strip_tags($validatedData['ticket_comment']);
            $ticket->status = $validatedData['ticket_status'];
            $ticket->save();

            // 1. Khởi tạo một mảng rỗng để chứa các model attachments được tạo ra
            $uploadedAttachments = [];

            if ($request->hasFile('attachments')) { 
                foreach ($request->file('attachments') as $file) { 
                    $originalName = $file->getClientOriginalName();
                    $folderPath = '3/' . $ticket->id;
                    $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); 
                    
                    // Thêm instance vừa tạo vào mảng thay vì ghi đè biến
                    $uploadedAttachments[] = Attachments_Model::create([
                        'type_of_ticket' => '3', 
                        'ticket_id' => $ticket->id,
                        'file_path' => $filePath,   
                        'name' => $originalName,
                    ]);
                }
            }

            switch ($ticket->status) {
                case '3':
                    $action = 'completed ticket at';
                    $status = 'Completed';
                    break;
                case '4':
                    $action = 'rejected ticket at';
                    $status = 'Rejected';
                    break;
                default:
                    $action = 'updated ticket at';
                    $status = 'Updated';
                    break;
            }
            
            
            tracking_info_service::add(
                $ticket->id, 
                auth()->id(), 
                3,
                $action,
            );

            // 2. Chuyển đổi mảng thành Collection và thực hiện map() an toàn
            $attachmentsData = collect($uploadedAttachments)->map(function ($file) {
                return [
                    'fileName' => basename($file->file_path),
                    'fileContent' => base64_encode(
                        Storage::disk('attachments')->get($file->file_path)
                    ),
                ];
            })->toArray(); // Chuyển về mảng thuần để chuẩn bị gửi API

            $leader_email = User::where('id', $ticket->user_owner->leader_id)->value('email');
            
            try {
                // Gửi dữ liệu qua Http Post
                $send_approval = Http::post(config('services.api_service.laser_engraving_complete_url'), [
                    'ticket_id' => $ticket->id,
                    'ticket_owner' => $ticket->user_owner->fullname,
                    'ticket_owner_email' => $ticket->user_owner->email,
                    'receipt' => $ticket->ticket_receipt,
                    'info_base' => $ticket->info_base,
                    'attachments' => $attachmentsData, // Gửi mảng dữ liệu đã xử lý an toàn
                    'leader_email' => $leader_email,
                    'status' => $status,
                    'comment' => $validatedData['ticket_comment'],
                ]);

                if ($send_approval->successful()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Ticket completed & Notification sent successfully',
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ticket completed but notification send failed due to: ' . $send_approval->body(),
                    ], 500);
                } 
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send notification due to: ' . $e->getMessage(),
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to close ticket due to: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function Re_Open_Laser_Engraving_Ticket($id){
        $ticket = Laser_Engraving_Tickets_Model::with('user_owner')->findOrFail($id);
        try {
            if ($ticket->status == '3' || $ticket->status == '4') {
                $ticket->status = '1'; //đổi status thành "Đang chờ"
                tracking_info_service::add(
                    $ticket->id,
                    auth()->id(),
                    3, //1 là mã cho software ticket
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

}
