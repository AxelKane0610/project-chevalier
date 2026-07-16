<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Thermal_Event_Exceptional_Tickets_Model;
use App\Models\Attachments_Model;
use App\Models\Comments_Model;
use App\Models\Thermal_Event_Parts_Details_Model;
use App\Models\User;
use App\Services\tracking_info_service;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;



class ThermalEventExceptionalTicketsController extends Controller
{
    //
    public function Show_Pending_Tickets(){ 
        if (auth()->user()->hasRole('ROLE_SUPER_ADMIN')) {
            $tickets = Thermal_Event_Exceptional_Tickets_Model::whereIn('status', ['1', '2', '3'])->get();
            $tickets_waiting_approval = Thermal_Event_Exceptional_Tickets_Model::whereIn('status', ['2', '3'])->get();
            $all_tickets = Thermal_Event_Exceptional_Tickets_Model::all();
            return view('thermal-event-tickets-menu', compact('tickets', 'tickets_waiting_approval', 'all_tickets'));
        } 
        else {
            $tickets = Thermal_Event_Exceptional_Tickets_Model::where('user_id', auth()->id()) //lọc ra ticket của user đó
                ->whereIn('status', ['1', '2', '3']) // lọc ra ticket đang pending
                ->get();

            

            $tickets_waiting_approval = Thermal_Event_Exceptional_Tickets_Model::whereIn('status', ['2', '3'])
                ->where('user_id', auth()->id())
                ->get();

            $all_tickets = Thermal_Event_Exceptional_Tickets_Model::where('user_id', auth()->id())->get();
            
            return view('thermal-event-tickets-menu', compact('tickets', 'tickets_waiting_approval', 'all_tickets'));

            
        }
        
    }

    public function Create_Thermal_Event_Ticket(Request $request){
        try {
            $validate_data = $request->validate([
                'ticket_receipt' => 'required',
                'serial_number' => 'required',
                'product_number'=> 'required',
                'product_model' => 'required',
                'description' => 'required',
                'cdax_id' => 'required',
                'customer_type' => 'required',
                'company_customer_name' => 'required',
                'part_mo_number' => 'required_if:multipart_affected_check,1',
                'part_number' => 'required_if:multipart_affected_check,1',
                'part_description' => 'required_if:multipart_affected_check,1',
                'part_ct_number' => 'required_if:multipart_affected_check,1',
                'user_observations' => 'required',
                'attachments.*' => 'file|max:20480|mimes:jpg,png,pdf,jpeg,xlsx'
            ]);

            $validate_data['user_id'] = auth()->id();
            $validate_data['ticket_receipt'] = strip_tags($validate_data['ticket_receipt']);
            $validate_data['serial_number'] = strip_tags($validate_data['serial_number']);
            $validate_data['product_number'] = strip_tags($validate_data['product_number']);
            $validate_data['product_model'] = strip_tags($validate_data['product_model']);
            $validate_data['description'] = strip_tags($validate_data['description']);
            $validate_data['cdax_id'] = strip_tags($validate_data['cdax_id']);
            $validate_data['customer_type'] = strip_tags($validate_data['customer_type']);
            $validate_data['company_customer_name'] = strip_tags($validate_data['company_customer_name']);
            $validate_data['user_observations'] = strip_tags($validate_data['user_observations']);

            
            
            
            if ($request->input('multipart_affected_check') == '1') {
                $validate_data['status'] = '2';
                $ticket = Thermal_Event_Exceptional_Tickets_Model::create($validate_data);

                $validate_data['ticket_id'] = $ticket->id;
                $validate_data['status'] = '1';
                $validate_data['part_mo_number'] = strip_tags($validate_data['part_mo_number']);
                $validate_data['part_number'] = strip_tags($validate_data['part_number']);
                $validate_data['part_description'] = strip_tags($validate_data['part_description']);
                $validate_data['part_ct_number'] = strip_tags($validate_data['part_ct_number']);
                $parts_details = Thermal_Event_Parts_Details_Model::create($validate_data);

                if ($request->hasFile('attachments')) { //Kiểm tra xem có file nào được upload lên không

                    foreach ($request->file('attachments') as $file) { //Duyệt qua từng file được upload lên
                        $originalName = $file->getClientOriginalName();
                        $folderPath = '10/'.$ticket->id;
                        $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục '/'
                        
                        Attachments_Model::create([
                            'type_of_ticket' => 10,
                            'ticket_id' => $ticket->id,
                            'file_path' => $filePath,   
                            'name' => $originalName,// Lưu tên gốc của file vào cơ sở dữ liệu
                        ]);
                    }
                    
                }
                
                tracking_info_service::add(
                    $ticket->id, 
                    auth()->id(), 
                    10,
                    'created ticket at'
                );

                $send_approve_ticket = Thermal_Event_Exceptional_Tickets_Model::with('user_owner', 'active_attachments')->findOrFail($ticket->id);
                
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

                $parts_thermal_details = Thermal_Event_Parts_Details_Model::where([
                    ['ticket_id', $ticket->id],
                    ['status', '1']
                ])->get();

                try {
                    $send_approval = Http::post(config('services.api_service.thermal_event_request_url'), [
                        'ticket_id' => $ticket->id,
                        'ticket_owner' => $send_approve_ticket->user_owner->fullname,
                        'ticket_owner_email' => $send_approve_ticket->user_owner->email,
                        'receipt' => $send_approve_ticket->ticket_receipt,
                        'cdax_id' => $send_approve_ticket->cdax_id,
                        'serial_number' => $send_approve_ticket->serial_number,
                        'product_number' => $send_approve_ticket->product_number,
                        'product_model' => $send_approve_ticket->product_model,
                        'issue_description' => $send_approve_ticket->description,
                        'customer_type' => match($send_approve_ticket->customer_type)
                        {
                            '1' => 'Khách hàng lẻ',
                            '2' => 'Khách hàng công ty/doanh nghiệp',
                            '3' => 'T1/Đại lý bán lẻ',
                            default => 'Unknown',
                        },
                        'company_customer_name' => $send_approve_ticket->company_customer_name,
                        'user_observations' => $send_approve_ticket->user_observations,
                        'leader_email' => User::where('id', $send_approve_ticket->user_owner->leader_id)->value('email'),
                        'parts_details' => $parts_thermal_details,
                        'attachments' => $attachments,
                    ]);


                    if ($send_approval->successful()) {
                        tracking_info_service::add(
                            $ticket->id, 
                            auth()->id(), 
                            10,
                            'sent approval request at'
                        );
                        return response()->json([
                            'success' => true,
                            'message' => 'Ticket created successfully & Approval request sent !',
                        ]);
                    } else {
                        // Xử lý lỗi nếu phản hồi không thành công
                        return response()->json([
                            'success' => false,
                            'message' => 'Ticket tạo thành công, nhưng API trả lỗi: ' . $send_approval->body(),
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
                $validate_data['status'] = '1';
                $ticket = Thermal_Event_Exceptional_Tickets_Model::create($validate_data);
                if ($request->hasFile('attachments')) { //Kiểm tra xem có file nào được upload lên không

                    foreach ($request->file('attachments') as $file) { //Duyệt qua từng file được upload lên
                        $originalName = $file->getClientOriginalName();
                        $folderPath = '10/'.$ticket->id;
                        $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục '/'
                        
                        Attachments_Model::create([
                            'type_of_ticket' => 10,
                            'ticket_id' => $ticket->id,
                            'file_path' => $filePath,   
                            'name' => $originalName,// Lưu tên gốc của file vào cơ sở dữ liệu
                        ]);
                    }
                    
                }
                tracking_info_service::add(
                    $ticket->id, 
                    auth()->id(), 
                    10,
                    'created ticket at'
                );
                return response()->json([
                    'success' => true,
                    'message' => 'Ticket created successfully',
                    'ticket_id' => $ticket->id,
                    'ticket_receipt' => $ticket->ticket_receipt,
                    'status' => match ($ticket->status) {
                        '1' => 'Open',
                        '2' => 'Waiting for verifier',
                        // default => 'Unknown',
                    },
                    'user_owner' => $ticket->user_owner->fullname,
                    'description' => $ticket->description,
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ticket due to ' .$e->getMessage(),
            ], 500);
        }
 
    }

    public function Show_Thermal_Event_Ticket_Details($id){
        $ticket = Thermal_Event_Exceptional_Tickets_Model::with(['user_owner', 'active_attachments','ticket_tracking_info','ticket_comments.attachments', 'ticket_comments.user', 'parts_details'])->findOrFail($id);
        return view('thermal-event-tickets-menu-details', compact('ticket'));
    }

    public function Edit_Thermal_Event_Ticket(Request $request, $id){
        $ticket = Thermal_Event_Exceptional_Tickets_Model::with('user_owner')->findOrFail($id);
        try {
            if ($ticket->status == '1') {
                $validate_data = $request->validate([
                    'ticket_receipt' => 'required',
                    'serial_number' => 'required',
                    'product_number'=> 'required',
                    'product_model' => 'required',
                    'description' => 'required',
                    'cdax_id' => 'required',
                    'customer_type' => 'required',
                    'company_customer_name' => 'required',
                    'user_observations' => 'required',
                    'attachments.*' => 'file|max:20480|mimes:jpg,png,jpeg'
            ]);

                $validate_data['ticket_receipt'] = strip_tags($validate_data['ticket_receipt']);
                $validate_data['serial_number'] = strip_tags($validate_data['serial_number']);
                $validate_data['product_number'] = strip_tags($validate_data['product_number']);
                $validate_data['product_model'] = strip_tags($validate_data['product_model']);
                $validate_data['description'] = strip_tags($validate_data['description']);
                $validate_data['cdax_id'] = strip_tags($validate_data['cdax_id']);
                $validate_data['customer_type'] = strip_tags($validate_data['customer_type']);
                $validate_data['company_customer_name'] = strip_tags($validate_data['company_customer_name']);
                $validate_data['user_observations'] = strip_tags($validate_data['user_observations']);

            
                $ticket->ticket_receipt = $validate_data['ticket_receipt'];
                $ticket->serial_number = $validate_data['serial_number'];
                $ticket->product_number = $validate_data['product_number'];
                $ticket->product_model = $validate_data['product_model'];
                $ticket->description = $validate_data['description'];
                $ticket->cdax_id = $validate_data['cdax_id'];
                $ticket->customer_type = $validate_data['customer_type'];
                $ticket->company_customer_name = $validate_data['company_customer_name'];
                $ticket->user_observations = $validate_data['user_observations'];

                tracking_info_service::add(
                    $ticket->id, 
                    auth()->id(), 
                    10,
                    'edited ticket at'
                );

                $ticket->save();

                if ($request->hasFile('attachments')) { //Kiểm tra xem có file nào được upload lên không

                    foreach ($request->file('attachments') as $file) { //Duyệt qua từng file được upload lên
                        $originalName = $file->getClientOriginalName();
                        $folderPath = '10/'.$id;
                        $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục 'attachments' đã được cấu hình trong config/filesystems.php, với đường dẫn là 'attachments/1/{ticket_id}/{original_file_name}'
                        
                        Attachments_Model::create([
                            'type_of_ticket' => 10, // Giả sử 1 là mã cho software ticket
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
                'message' => 'Failed to edit ticket due to ' .$e->getMessage(),
            ], 500);
        }
        
    }

    public function Add_Comment_Thermal_Event_Ticket(Request $request, $id){
        $comment_info_input = $request->validate([
            'comment' => 'required_without_all:attachments|string|nullable',
            'attachments' => 'required_without_all:comment|array|nullable',
            'attachments.*' => 'file|max:20480|mimes:jpg,jpeg,png,pdf,xlsx,docx',
        ]);

        $comment_info_input['comment'] = strip_tags($comment_info_input['comment']);
        $comment_info_input['ticket_id'] = $id;
        $comment_info_input['type_of_ticket'] = 10; //1 là mã cho software ticket
        $comment_info_input['user_id'] = auth()->id();
        
        
        $comment = Comments_Model::create($comment_info_input);
        

        if($request->hasFile('attachments'))
        {
            foreach($request->file('attachments') as $file)
            {
                $originalName = $file->getClientOriginalName();
                $folderPath = '10/'.$id;
                $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục 'attachments' đã được cấu hình trong config/filesystems.php, với đường dẫn là 'attachments/1/{ticket_id}/{original_file_name}'
                
                Attachments_Model::create([
                    'type_of_ticket' => 10,
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

    public function Thermal_Event_Approve_Lv1($id){
        $ticket = Thermal_Event_Exceptional_Tickets_Model::with('user_owner')->findOrFail($id);
        if ($ticket->status != 2) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể approve ticket. Ticket không ở trạng thái đang chờ phê duyệt level 1.',
            ], 400);
        }
        else if ($ticket->status == 2){
            $ticket->status = 3;
            $ticket->save();

            tracking_info_service::add(
                $ticket->id,
                auth()->id(),
                10, //1 là mã cho software ticket
                'approved ticket at',
            );

            return response()->json([
                'success' => true,
                'message' => 'Ticket approved !',
            ]);
        }
    }

    public function Thermal_Event_Approve_Lv2( $id){
        $ticket = Thermal_Event_Exceptional_Tickets_Model::with('user_owner')->findOrFail($id);
        if ($ticket->status != 3) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể approve ticket. Ticket không ở trạng thái đang chờ phê duyệt level 1.',
            ], 400);
        }
        else if ($ticket->status == 3){
            $ticket->status = 4;
            $ticket->save();

            tracking_info_service::add(
                $ticket->id,
                auth()->id(),
                10, //1 là mã cho software ticket
                'fully approved ticket at',
            );

            return response()->json([
                'success' => true,
                'message' => 'Ticket approved !',
            ]);
        }
    }

    public function Thermal_Event_Reject($id){
        $ticket = Thermal_Event_Exceptional_Tickets_Model::with('user_owner')->findOrFail($id);
        if ($ticket->status == 1 || $ticket->status == 4 || $ticket->status == 5) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể reject ticket. Ticket không ở trạng thái đang chờ phê duyệt.',
            ], 400);
        }
        else {
            $ticket->status = 5;
            $ticket->save();

            tracking_info_service::add(
                $ticket->id,
                auth()->id(),
                10, //1 là mã cho software ticket
                'rejected ticket at',
            );

            return response()->json([
                'success' => true,
                'message' => 'Ticket rejected !',
            ]);
        }
        
    }

    public function Thermal_Event_Re_Open($id){
        $ticket = Thermal_Event_Exceptional_Tickets_Model::with('user_owner')->findOrFail($id);
        if ($ticket->status == 4 || $ticket->status == 5) {
            $ticket->status = 1; //đổi status thành "Open"
            tracking_info_service::add(
                $ticket->id,
                auth()->id(),
                10, //1 là mã cho software ticket
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
    }

    public function Add_Thermal_Event_Part(Request $request, $id){
        $ticket = Thermal_Event_Exceptional_Tickets_Model::with('user_owner')->findOrFail($id);
        if ($ticket->status == 1) {
            $validate_data = $request->validate([
                'part_mo_number' => 'required',
                'part_number' => 'required',
                'part_description' => 'required',
                'part_ct_number' => 'required',
            ]);

            $new_part['ticket_id'] = $id;
            $new_part['part_mo_number'] = strip_tags($validate_data['part_mo_number']);
            $new_part['part_number'] = strip_tags($validate_data['part_number']);
            $new_part['part_description'] = strip_tags($validate_data['part_description']);
            $new_part['part_ct_number'] = strip_tags($validate_data['part_ct_number']);
            $new_part['status'] = '1';
            Thermal_Event_Parts_Details_Model::create($new_part);

            tracking_info_service::add(
                $ticket->id, 
                auth()->id(), 
                10,
                'add part at'
            );

            return response()->json([
                'success' => true,
                'message' => 'Part added successfully',
            ]);

        } else return response()->json([
            'success' => false,
            'message' => 'Chỉ có ticket đang ở trạng thái "Open" mới được phép add thêm part !',
        ], 400);

    
    }

    public function Edit_Thermal_Event_Part_Details(Request $request, $id){
        try {
            $part = Thermal_Event_Parts_Details_Model::findOrFail($id);
            $ticket = Thermal_Event_Exceptional_Tickets_Model::with('user_owner')->findOrFail($part->ticket_id);
            if ($ticket->status == 1) {
                $validate_data = $request->validate([
                    'part_mo_number' => 'required',
                    'part_number' => 'required',
                    'part_description' => 'required',
                    'part_ct_number' => 'required',
                ]);

                $part->part_mo_number = strip_tags($validate_data['part_mo_number']);
                $part->part_number = strip_tags($validate_data['part_number']);
                $part->part_description = strip_tags($validate_data['part_description']);
                $part->part_ct_number = strip_tags($validate_data['part_ct_number']);
                $part->save();

                tracking_info_service::add(
                    $ticket->id, 
                    auth()->id(), 
                    10,
                    'edited part details at'
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Part details edited successfully',
                ]);

            } else return response()->json([
                'success' => false,
                'message' => 'Chỉ có ticket đang ở trạng thái "Open" mới được phép edit part details !',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to edit part detail due to ' .$e->getMessage(),
            ], 500);
        }

    
    }

    public function Delete_Thermal_Event_Part_Details(Request $request, $id){
        $part = Thermal_Event_Parts_Details_Model::findOrFail($id);
        $ticket = Thermal_Event_Exceptional_Tickets_Model::with('user_owner')->findOrFail($part->ticket_id);
        if ($ticket->status == 1) {
            $part->status = '0';
            $part->save();

            tracking_info_service::add(
                $ticket->id, 
                auth()->id(), 
                10,
                'deleted part at'
            );

            return response()->json([
                'success' => true,
                'message' => 'Part deleted successfully',
            ]);

        } else return response()->json([
            'success' => false,
            'message' => 'Chỉ có ticket đang ở trạng thái "Open" mới được phép delete part !',
        ], 400);

    
    }

    public function Send_Approve_Thermal_Event ($id){
        $ticket = Thermal_Event_Exceptional_Tickets_Model::with('user_owner', 'active_attachments')->findOrFail($id);
        $parts_thermal_details = Thermal_Event_Parts_Details_Model::where([
            ['ticket_id', $ticket->id],
            ['status', '1']
        ])->get();

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
            $send_approval = Http::post(config('services.api_service.thermal_event_request_url'), [
                'ticket_id' => $ticket->id,
                'ticket_owner' => $ticket->user_owner->fullname,
                'ticket_owner_email' => $ticket->user_owner->email,
                'receipt' => $ticket->ticket_receipt,
                'cdax_id' => $ticket->cdax_id,
                'serial_number' => $ticket->serial_number,
                'product_number' => $ticket->product_number,
                'product_model' => $ticket->product_model,
                'issue_description' => $ticket->description,
                'customer_type' => match($ticket->customer_type)
                {
                    '1' => 'Khách hàng lẻ',
                    '2' => 'Khách hàng công ty/doanh nghiệp',
                    '3' => 'T1/Đại lý bán lẻ',
                    default => 'Unknown',
                },
                'company_customer_name' => $ticket->company_customer_name,
                'user_observations' => $ticket->user_observations,
                'leader_email' => User::where('id', $ticket->user_owner->leader_id)->value('email'),
                'parts_details' => $parts_thermal_details,
                'attachments' => $attachments,
            ]);


            if ($send_approval->successful()) {
                tracking_info_service::add(
                    $ticket->id, 
                    auth()->id(), 
                    10,
                    'sent approval request at'
                );
                $ticket->status = '2';
                $ticket->save();
                return response()->json([
                    'success' => true,
                    'message' => 'Approval request sent !',
                ]);
            } else {
                // Xử lý lỗi nếu phản hồi không thành công
                return response()->json([
                    'success' => false,
                    'message' => 'Fail to send approve due to: ' . $send_approval->body(),
                ], 500);
            } 
        } catch (\Exception $e) {
            // Xử lý lỗi nếu có
            return response()->json([
                'success' => false,
                'message' => 'Failed to send approve due to ' . $e->getMessage(),
            ], 500);
        }

    }
    
}
