<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TTEX_Tickets_Model;
use Illuminate\Http\Request;
use App\Models\Attachments_Model;
use App\Models\Comments_Model;
use App\Models\User;
use App\Services\tracking_info_service;

class TTEXTicketsController extends Controller
{
    //
    public function Show_Pending_Tickets(){ 
        if (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_TTEX_TICKET_ADMIN')) {
            $tickets = TTEX_Tickets_Model::all();
            $tickets_good_part_pending = TTEX_Tickets_Model::where([
                ['status', '1'],
                ['part_status', '1'],
            ])
            ->get();

            $tickets_def_part_pending = TTEX_Tickets_Model::where('status', '1')
            ->whereIn('part_status', ['2', '3'])
            ->get();
            return view('ttex-tickets-menu', compact('tickets', 'tickets_good_part_pending', 'tickets_def_part_pending'));
        } 
        else {
            $tickets = TTEX_Tickets_Model::where('user_id', auth()->id())->get();
            $tickets_good_part_pending = TTEX_Tickets_Model::where([
                ['status', '1'],
                ['part_status', '1'],
                ['user_id', auth()->id()]
            ])
            ->get();

            $tickets_def_part_pending = TTEX_Tickets_Model::where([
                ['status', '1'],
                ['user_id', auth()->id()]
            ])
            ->whereIn('part_status', ['2', '3'])
            ->get();

            return view('ttex-tickets-menu', compact('tickets', 'tickets_good_part_pending', 'tickets_def_part_pending'));


            
        }
        
    }

    public function Create_TTEX_Ticket(Request $request){
        try {
            $ticket_info_input = $request->validate([
                'category' => 'required',
                'shipment_type' => 'required',
                'part_status' => 'required',
                'sender_info' => 'required',
                'receiver_info' => 'required',
                'shipment_description' => 'required',
                'note' => 'nullable',
                'part_returned_check' => 'required',
                'attachments.*' => 'file|max:5120|mimes:jpg,png,pdf,jpeg,xlsx'
            ]);
            
            $ticket_info_input['user_id'] = auth()->id();
            $ticket_info_input['status'] = '1';
            $ticket_info_input['sender_info'] = strip_tags($ticket_info_input['sender_info']);
            $ticket_info_input['receiver_info'] = strip_tags($ticket_info_input['receiver_info']);
            $ticket_info_input['shipment_description'] = strip_tags($ticket_info_input['shipment_description']);
            $ticket_info_input['note'] = strip_tags($ticket_info_input['note']);


            
            $ticket = TTEX_Tickets_Model::create($ticket_info_input); //Phải tạo model EEG_Software_Ticket để có thể sử dụng hàm create() này, và phải khai báo fillable trong model đó nữa
            if ($request->hasFile('attachments')) { //Kiểm tra xem có file nào được upload lên không

                    foreach ($request->file('attachments') as $file) { //Duyệt qua từng file được upload lên
                        $originalName = $file->getClientOriginalName();
                        $folderPath = '2/'.$ticket->id;
                        $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục '/'
                        
                        Attachments_Model::create([
                            'type_of_ticket' => 2, // Giả sử 1 là mã cho software ticket
                            'ticket_id' => $ticket->id,
                            'file_path' => $filePath,   
                            'name' => $originalName,// Lưu tên gốc của file vào cơ sở dữ liệu
                        ]);
                    }
                    
                }

            tracking_info_service::add(
                $ticket->id, 
                auth()->id(), 
                2,
                'created ticket at'
            );

            if ($ticket_info_input['part_returned_check'] == '1') {
                $temp = $ticket_info_input['sender_info'];
                $ticket_info_input['sender_info'] = $ticket_info_input['receiver_info'];
                $ticket_info_input['receiver_info'] = $temp;
                $ticket_info_input['part_return_deadline'] = now('UTC')->addDays(14)->toDateString();
                $ticket_info_input['part_status'] = '2';
                $ticket_def = TTEX_Tickets_Model::create($ticket_info_input);

                if ($request->hasFile('attachments')) { //Kiểm tra xem có file nào được upload lên không

                    foreach ($request->file('attachments') as $file) { //Duyệt qua từng file được upload lên
                        $originalName = $file->getClientOriginalName();
                        $folderPath = '2/'.$ticket->id;
                        $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục '/'
                        
                        Attachments_Model::create([
                            'type_of_ticket' => 2, // Giả sử 1 là mã cho software ticket
                            'ticket_id' => $ticket_def->id,
                            'file_path' => $filePath,   
                            'name' => $originalName,// Lưu tên gốc của file vào cơ sở dữ liệu
                        ]);
                    }
                    
                }

                tracking_info_service::add(
                    $ticket_def->id, 
                    auth()->id(), 
                    2,
                    'created ticket at'
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Ticket điều tin good và thu hồi def tạo thành công !',
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'Ticket điều tin tạo thành công !',
                ]);
            }
                

                

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ticket due to ' .$e->getMessage(),
            ], 500);
        }
        


        
    }

    public function Show_TTEX_Ticket_Details ($id){
        $ticket = TTEX_Tickets_Model::with(['user_owner', 'active_attachments','ticket_tracking_info','ticket_comments.attachments', 'ticket_comments.user'])->findOrFail($id);
        return view('ttex-ticket-menu-details', compact('ticket'));
    }

    public function Add_Comment_TTEX_Ticket(Request $request, $id){
        $comment_info_input = $request->validate([
            'comment' => 'required_without_all:attachments|string|nullable',
            'attachments' => 'required_without_all:comment|array|nullable',
            'attachments.*' => 'file|max:20480|mimes:jpg,jpeg,png,pdf,xlsx,docx',
        ]);

        $comment_info_input['comment'] = strip_tags($comment_info_input['comment']);
        $comment_info_input['ticket_id'] = $id;
        $comment_info_input['type_of_ticket'] = 2;
        $comment_info_input['user_id'] = auth()->id();
        
        
        $comment = Comments_Model::create($comment_info_input);
        

        if($request->hasFile('attachments'))
        {
            foreach($request->file('attachments') as $file)
            {
                $originalName = $file->getClientOriginalName();
                $folderPath = '2/'.$id;
                $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục 'attachments' đã được cấu hình trong config/filesystems.php, với đường dẫn là 'attachments/1/{ticket_id}/{original_file_name}'
                
                Attachments_Model::create([
                    'type_of_ticket' => 2,
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

    public function Edit_TTEX_Ticket(Request $request, $id){
        $ticket = TTEX_Tickets_Model::with('user_owner')->findOrFail($id);
        try {
            if ($ticket->status == '1') {
                $validate_data = $request->validate([
                    'category' => 'required',
                    'shipment_type' => 'required',
                    'part_status'=> 'required',
                    'part_return_deadline'=> 'nullable',
                    'sender_info' => 'required',
                    'receiver_info' => 'required',
                    'shipment_description' => 'required',
                    'note' => 'required',
                    'attachments.*' => 'file|max:20480|mimes:jpg,png,jpeg'
            ]);

                $validate_data['category'] = strip_tags($validate_data['category']);
                $validate_data['shipment_type'] = strip_tags($validate_data['shipment_type']);
                $validate_data['part_status'] = strip_tags($validate_data['part_status']);
                if (empty($ticket_info_input['part_return_deadline'])) {
                    $ticket_info_input['part_return_deadline'] = null;
                }
                $validate_data['sender_info'] = strip_tags($validate_data['sender_info']);
                $validate_data['receiver_info'] = strip_tags($validate_data['receiver_info']);
                $validate_data['shipment_description'] = strip_tags($validate_data['shipment_description']);
                $validate_data['note'] = strip_tags($validate_data['note']);

            
                $ticket->category = $validate_data['category'];
                $ticket->shipment_type = $validate_data['shipment_type'];
                $ticket->part_status = $validate_data['part_status'];
                $ticket->part_return_deadline = $validate_data['part_return_deadline'];
                $ticket->sender_info = $validate_data['sender_info'];
                $ticket->receiver_info = $validate_data['receiver_info'];
                $ticket->shipment_description = $validate_data['shipment_description'];
                $ticket->note = $validate_data['note'];

                tracking_info_service::add(
                    $ticket->id, 
                    auth()->id(), 
                    2,
                    'edited ticket at'
                );

                $ticket->save();

                if ($request->hasFile('attachments')) { //Kiểm tra xem có file nào được upload lên không

                    foreach ($request->file('attachments') as $file) { //Duyệt qua từng file được upload lên
                        $originalName = $file->getClientOriginalName();
                        $folderPath = '2/'.$id;
                        $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục 'attachments' đã được cấu hình trong config/filesystems.php, với đường dẫn là 'attachments/1/{ticket_id}/{original_file_name}'
                        
                        Attachments_Model::create([
                            'type_of_ticket' => 2, // Giả sử 1 là mã cho software ticket
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
                    'message' => 'Ticket edited successfully',
                ]);
            } else return response()->json([
                'success' => false,
                'message' => 'Chỉ có ticket đang ở trạng thái "Open - Chưa điều tin" mới được phép edit !',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to edit ticket due to ' .$e->getMessage(),
            ], 500);
        }
        
    }

    public function Close_TTEX_Ticket(Request $request, $id){
        $ticket = TTEX_Tickets_Model::with('user_owner')->findOrFail($id);
        try {
            if ($ticket->status == '1') {
                $ticket_info_input = $request->validate([
                    'status' => 'required',
                    'ttex_bill' => 'nullable',
                    'comment' => 'nullable'
                ]);

                $ticket_info_input['ttex_bill'] = strip_tags($ticket_info_input['ttex_bill']);
                $ticket_info_input['comment'] = strip_tags($ticket_info_input['comment']);
                
                
                $ticket->status = $ticket_info_input['status'];
                $ticket->ttex_bill = $ticket_info_input['ttex_bill'];
                $ticket->booking_date = now();
                $ticket->save();

                switch ($ticket_info_input['status']) {
                    case '2':
                        $action = 'completed booking ticket at';
                        $status = 'Completed';
                        break;
                    case '3':
                        $action = 'rejected ticket at';
                        $status = 'Rejected';
                        break;
                    default:
                        $action = 'updated ticket status to ' . $ticket_info_input['status'] . ' at';
                }
                tracking_info_service::add(
                    $ticket->id,
                    auth()->id(),
                    2, //1 là mã cho software ticket
                    $action,
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Ticket closed successfully ',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể đóng ticket do ticket không ở trạng thái Open ',
                ], 500);
            }

            //     $send_ticket_complete_notification = Http::post(config('services.api_service.sw_ticket_complete_url'), [
            //         'ticket_id' => $ticket->id,
            //         'ticket_owner_name'   => $ticket->user_owner->fullname,
            //         'ticket_owner_email' => $ticket->user_owner->email,
            //         'receipt' => $ticket->ticket_receipt,
            //         'description' => $ticket->description,
            //         'completed by' => $ticket->completed_by->fullname,
            //         'ticket_comment' => $ticket_info_input['ticket_comment'],
            //         'status' => $status,
            //     ]);
            //     if ($send_ticket_complete_notification->successful()) {
                    
            //         return response()->json([
            //             'success' => true,
            //             'message' => 'Ticket closed and notification sent successfully',
            //         ]);
            //     } else {
            //         // Xử lý lỗi nếu phản hồi không thành công
            //         return response()->json([
            //             'success' => false,
            //             'message' => 'Ticket closed but cant send notification due to: ' . $send_ticket_complete_notification->body(),
            //         ], 500);
            //     } 
            // } else {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Chỉ có ticket ở trạng thái "Open" hoặc "In Progress" mới có thể đóng !',
            //     ], 400);
            // }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to close ticket due to ' .$e->getMessage(),
            ], 500);
        } 
        
    }

    public function Power_Automate_Good_Part_Booking (Request $request) {
        
        if ($request->header('api_key') !== config('services.api_service.power_automate_api_key')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access. Invalid API key.',
            ], 401);
        } else {
                $tickets_good_part_pending = TTEX_Tickets_Model::with('user_owner')->where([
                    ['part_status', '1'],
                    ['booking_date', today()]
                ])->get();
                $email_list = $tickets_good_part_pending->pluck('user_owner.email')->toArray();//
                $email_list = array_unique($email_list);
                $email_list = implode(';', $email_list);
            if (count($tickets_good_part_pending) > 0){
                return response()->json([
                    'success' => true,
                    'tickets_good_part_pending' => $tickets_good_part_pending,
                    'email_list' => $email_list,
                ]);
            }
        }
        
    }

    public function Booking_Def_Part(Request $request) {
        try {
            
            if ($request->has('booking_def') && is_array($request->input('booking_def'))) {
                $def_part_tickets = TTEX_Tickets_Model::whereIn('id', $request->input('booking_def'))->get();
                TTEX_Tickets_Model::whereIn('id', $request->input('booking_def'))->update([
                    'booking_date' => today(),
                    'status' => '2',
                ]);
                tracking_info_service::add(
                    $def_part_tickets->first()->id,
                    auth()->id(),
                    2, //1 là mã cho software ticket
                    'completed booking ticket at',
                );
                return response()->json([
                    'success' => true,
                    'message' => 'Selected tickets booked successfully.',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No tickets selected for booking.',
                ], 400);
            }

            
                
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to book tickets due to: ' . $e->getMessage(),
            ], 500);
        }
    }

    
}
