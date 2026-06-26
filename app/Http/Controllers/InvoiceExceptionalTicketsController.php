<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice_Exceptional_Tickets_Model;
use App\Models\Attachments_Model;
use App\Models\Comments_Model;
use App\Services\tracking_info_service;

class InvoiceExceptionalTicketsController extends Controller
{
    //
    public function Show_Pending_Tickets(){ 
        if (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_INVOICE_EXCEPTIONAL_L1_APPROVER')) {
            $tickets = Invoice_Exceptional_Tickets_Model::whereIn('status', ['1', '2', '3'])->get();
            $tickets_waiting_approval = Invoice_Exceptional_Tickets_Model::where('status', 3)->get();
            return view('invoice-exceptional-menu', compact('tickets', 'tickets_waiting_approval'));
        } 
        else {
            $tickets = Invoice_Exceptional_Tickets_Model::where('user_id', auth()->id()) //lọc ra ticket của user đó
                ->whereIn('status', ['1', '2', '3']) // lọc ra ticket đang pending
                ->get();

            

            $tickets_waiting_approval = Invoice_Exceptional_Tickets_Model::whereIn('status', ['1', '2', '3'])
                ->whereHas('user_owner', function ($query) { //Lọc ra những ticket có user_owner có leader_id là id của user đang đăng nhập, tức là lọc ra những ticket của những user mà user đang đăng nhập là leader của họ, rồi mới lấy ra những ticket đó để trả về view
                    $query->where('leader_id', auth()->id());
                })
                ->get();
            
            return view('invoice-exceptional-menu', compact('tickets', 'tickets_waiting_approval'));

            
        }
        
    }

    public function Create_Invoice_Exceptional_Tickets(Request $request){
        try {
            $validate_data = $request->validate([
                'ticket_receipt' => 'required',
                'invoice_number' => 'required',
                'serial_number' => 'required',
                'product_number' => 'required',
                'product_model' => 'required',
                'invoice_date' => 'required',
                'expired_date' => 'required',
                'retail_name' => 'required',
                'company_customer_name' => 'required',
                'support_type' => 'required',
                'description' => 'required',
                'attachments.*' => 'file|max:20480|mimes:jpg,png,pdf,jpeg,xlsx,docx'
            ]);

            $validate_data['user_id'] = auth()->id();
            $validate_data['status'] = '2';
            $validate_data['ticket_receipt'] = strip_tags($validate_data['ticket_receipt']);
            $validate_data['invoice_number'] = strip_tags($validate_data['invoice_number']);
            $validate_data['serial_number'] = strip_tags($validate_data['serial_number']);
            $validate_data['product_number'] = strip_tags($validate_data['product_number']);
            $validate_data['product_model'] = strip_tags($validate_data['product_model']);
            $validate_data['invoice_date'] = strip_tags($validate_data['invoice_date']);
            $validate_data['expired_date'] = strip_tags($validate_data['expired_date']);
            $validate_data['retail_name'] = strip_tags($validate_data['retail_name']);
            $validate_data['company_customer_name'] = strip_tags($validate_data['company_customer_name']);
            $validate_data['support_type'] = strip_tags($validate_data['support_type']);
            $validate_data['description'] = strip_tags($validate_data['description']);

            $ticket = Invoice_Exceptional_Tickets_Model::create($validate_data);
            if ($request->hasFile('attachments')) { //Kiểm tra xem có file nào được upload lên không

                foreach ($request->file('attachments') as $file) { //Duyệt qua từng file được upload lên
                    $originalName = $file->getClientOriginalName();
                    $folderPath = '7/'.$ticket->id;
                    $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục '/'
                    
                    Attachments_Model::create([
                        'type_of_ticket' => 7,
                        'ticket_id' => $ticket->id,
                        'file_path' => $filePath,   
                        'name' => $originalName,// Lưu tên gốc của file vào cơ sở dữ liệu
                    ]);
                }
                
            }

            tracking_info_service::add(
                $ticket->id, 
                auth()->id(), 
                7,
                'created ticket at'
            );

            
            $ticket->save();
            return response()->json([
                'success' => true,
                'message' => 'Ticket created successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ticket due to ' .$e->getMessage(),
            ], 500);
        }

    }

    public function Show_Invoice_Exceptional_Ticket_Details($id) {
        $ticket = Invoice_Exceptional_Tickets_Model::with(['user_owner', 'active_attachments','ticket_tracking_info','ticket_comments.attachments', 'ticket_comments.user'])->findOrFail($id);
        return view('invoice-exceptional-menu-details', compact('ticket'));
    }

    public function Add_Comment_Invoice_Exceptional_Ticket(Request $request, $id){
        $comment_info_input = $request->validate([
            'comment' => 'required',
        ]);

        $comment_info_input['comment'] = strip_tags($comment_info_input['comment']);
        $comment_info_input['ticket_id'] = $id;
        $comment_info_input['type_of_ticket'] = 7;
        $comment_info_input['user_id'] = auth()->id();
        
        
        $comment = Comments_Model::create($comment_info_input);
        

        if($request->hasFile('attachments'))
        {
            foreach($request->file('attachments') as $file)
            {
                $originalName = $file->getClientOriginalName();
                $folderPath = '7/'.$id;
                $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục 'attachments' đã được cấu hình trong config/filesystems.php, với đường dẫn là 'attachments/1/{ticket_id}/{original_file_name}'
                
                Attachments_Model::create([
                    'type_of_ticket' => 7,
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

    public function Edit_Invoice_Exceptional_Ticket(Request $request, $id){
        $ticket = Invoice_Exceptional_Tickets_Model::with('user_owner')->findOrFail($id);
        try {
            if ($ticket->status == '1') {
                $validate_data = $request->validate([
                    'ticket_receipt' => 'required',
                    'invoice_number' => 'required',
                    'serial_number' => 'required',
                    'product_number'=> 'required',
                    'expired_date' => 'required',
                    'invoice_date' => 'required',
                    'product_model' => 'required',
                    'description' => 'required',
                    'retail_name' => 'required',
                    'company_customer_name' => 'required',
                    'support_type' => 'required',
                    'attachments.*' => 'file|max:20480|mimes:jpg,png,pdf,jpeg,xlsx'
            ]);

                $validate_data['ticket_receipt'] = strip_tags($validate_data['ticket_receipt']);
                $validate_data['invoice_number'] = strip_tags($validate_data['invoice_number']);
                $validate_data['serial_number'] = strip_tags($validate_data['serial_number']);
                $validate_data['product_number'] = strip_tags($validate_data['product_number']);
                $validate_data['expired_date'] = strip_tags($validate_data['expired_date']);
                $validate_data['invoice_date'] = strip_tags($validate_data['invoice_date']);
                $validate_data['product_model'] = strip_tags($validate_data['product_model']);
                $validate_data['description'] = strip_tags($validate_data['description']);
                $validate_data['retail_name'] = strip_tags($validate_data['retail_name']);
                $validate_data['company_customer_name'] = strip_tags($validate_data['company_customer_name']);
                $validate_data['support_type'] = strip_tags($validate_data['support_type']);
                
                $ticket->ticket_receipt = $validate_data['ticket_receipt'];
                $ticket->invoice_number = $validate_data['invoice_number'];
                $ticket->serial_number = $validate_data['serial_number'];
                $ticket->product_number = $validate_data['product_number'];
                $ticket->expired_date = $validate_data['expired_date'];
                $ticket->invoice_date = $validate_data['invoice_date'];
                $ticket->product_model = $validate_data['product_model'];
                $ticket->description = $validate_data['description'];
                $ticket->retail_name = $validate_data['retail_name'];
                $ticket->company_customer_name = $validate_data['company_customer_name'];
                $ticket->support_type = $validate_data['support_type'];


                tracking_info_service::add(
                    $ticket->id, 
                    auth()->id(), 
                    7,
                    'edited ticket at'
                );

                $ticket->save();

                if ($request->hasFile('attachments')) { //Kiểm tra xem có file nào được upload lên không

                    foreach ($request->file('attachments') as $file) { //Duyệt qua từng file được upload lên
                        $originalName = $file->getClientOriginalName();
                        $folderPath = '7/'.$id;
                        $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục 'attachments' đã được cấu hình trong config/filesystems.php, với đường dẫn là 'attachments/1/{ticket_id}/{original_file_name}'
                        
                        Attachments_Model::create([
                            'type_of_ticket' => 7, // Giả sử 1 là mã cho software ticket
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
                'message' => 'Chỉ có ticket đang ở trạng thái "Open" mới được phép edit !',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ticket due to ' .$e->getMessage(),
            ], 500);
        }
    }

    public function Send_Approve_Invoice_Exceptional($id) {
        $ticket = Invoice_Exceptional_Tickets_Model::with('user_owner', 'active_attachments')->findOrFail($id);
        try {
            if ($ticket->status == '1' && $ticket->highest_approved_step == '1') {
                
                $ticket->status = '2';
                $ticket->save();
                tracking_info_service::add(
                    $ticket->id, 
                    auth()->id(), 
                    7,
                    'sent approval request at',
                );
                return response()->json([
                    'success' => true,
                    'message' => 'Approval request sent successfully',
                ]);
                
            } else if ($ticket->status == '1' && $ticket->highest_approved_step == '2') {
                $ticket->status = '3';
                $ticket->save();
                tracking_info_service::add(
                    $ticket->id, 
                    auth()->id(), 
                    7,
                    'sent approval request at',
                );
                return response()->json([
                    'success' => true,
                    'message' => 'Approval request sent successfully',
                ]);

            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có ticket đang ở trạng thái "Open" mới có thể request approve !',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ticket due to ' .$e->getMessage(),
                'error' => $e->getMessage(), // Có thể bỏ ở môi trường production
            ], 500);
        }

    }

    public function Invoice_Exceptional_Approve_Lv1($id){
        $ticket = Invoice_Exceptional_Tickets_Model::with('user_owner')->findOrFail($id);
        try {
            if ($ticket->status != '2') {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể approve ticket. Ticket không ở trạng thái đang chờ phê duyệt.',
                ], 400);
            }
            else if ($ticket->status == '2' && ($ticket->support_type == '1' || $ticket->support_type == '2')){
                $ticket->status = '4';
                $ticket->save();

                tracking_info_service::add(
                    $ticket->id,
                    auth()->id(),
                    7, //1 là mã cho software ticket
                    'approved invoice at',
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Invoice approved !',
                ], 200);
            } else if ($ticket->status == '2' && ($ticket->support_type == '3' || $ticket->support_type == '4'))  {
                $ticket->status = '3';
                $ticket->highest_approved_step = '2';
                $ticket->save();

                tracking_info_service::add(
                    $ticket->id,
                    auth()->id(),
                    7, //1 là mã cho software ticket
                    'approved invoice at',
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Invoice approved !',
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve due to ',
                'error' => $e->getMessage(), // Có thể bỏ ở môi trường production
            ], 500);
        }
    }

    public function Invoice_Exceptional_Approve_Lv2($id){
        $ticket = Invoice_Exceptional_Tickets_Model::with('user_owner')->findOrFail($id);
        try {
            if ($ticket->status != '3') {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể approve ticket. Ticket không ở trạng thái đang chờ active warranty.',
                ], 400);
            }
            else if ($ticket->status == '3' && ($ticket->support_type == '3' || $ticket->support_type == '4')){
                $ticket->status = '4';
                $ticket->save();

                tracking_info_service::add(
                    $ticket->id,
                    auth()->id(),
                    7, //1 là mã cho software ticket
                    'approved ticket at',
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Re-active warranty completed !',
                ], 200);
            } 
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve due to ',
                'error' => $e->getMessage(), // Có thể bỏ ở môi trường production
            ], 500);
        }
    }

    public function Invoice_Exceptional_Reject($id){
        $ticket = Invoice_Exceptional_Tickets_Model::with('user_owner')->findOrFail($id);
        try {
            if ($ticket->status == 1 || $ticket->status == 4 || $ticket->status == 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể reject ticket. Ticket không ở trạng thái đang chờ phê duyệt.',
                ], 400);
            }
            else {
                $ticket->status = '5';
                $ticket->save();

                tracking_info_service::add(
                    $ticket->id,
                    auth()->id(),
                    7, //1 là mã cho software ticket
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
                'message' => 'Failed to reject ticket due to: ',
                'error' => $e->getMessage(), // Có thể bỏ ở môi trường production
            ], 500);
        }
        
    }

    public function Re_Open_Invoice_Exceptional_Ticket($id) {
        $ticket = Invoice_Exceptional_Tickets_Model::with('user_owner')->findOrFail($id);
        try {
            if ($ticket->status == '4' || $ticket->status == '5') {
                $ticket->status = '1'; //đổi status thành "Open"
                tracking_info_service::add(
                    $ticket->id,
                    auth()->id(),
                    7, //1 là mã cho software ticket
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
                'message' => 'Failed to re-open ticket due to: ',
                'error' => $e->getMessage(), // Có thể bỏ ở môi trường production
            ], 500);
        }
    }



    
}
