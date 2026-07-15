<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Out_Of_Office_Tickets_Model;
use App\Models\Attachments_Model;
use App\Models\Comments_Model;
use App\Services\tracking_info_service;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Storage;



class OutOfOfficeTicketsController extends Controller
{
    //
    public function Show_Pending_Tickets(){ 
        if (auth()->user()->hasRole('ROLE_SUPER_ADMIN')) {
            $tickets = Out_Of_Office_Tickets_Model::whereIn('status', ['1', '2'])->get();
            $tickets_waiting_approval = Out_Of_Office_Tickets_Model::where('status', '2')->get();
            return view('out-of-office-tickets-menu', compact('tickets', 'tickets_waiting_approval'));
        } 
        else if (auth()->user()->hasRole('ROLE_OUT_OF_OFFICE_ADMIN')){
            $tickets = Out_Of_Office_Tickets_Model::where('user_id', auth()->id()) //lọc ra ticket của user đó
                ->whereIn('status', ['1', '2']) // lọc ra ticket đang pending
                ->get();

            

            $tickets_waiting_approval = Out_Of_Office_Tickets_Model::whereIn('status', ['1', '2'])
                ->whereHas('user_owner', function ($query) { //Lọc ra những ticket có user_owner có leader_id là id của user đang đăng nhập, tức là lọc ra những ticket của những user mà user đang đăng nhập là leader của họ, rồi mới lấy ra những ticket đó để trả về view
                    $query->where('leader_id', auth()->id());
                })
                ->get();
            
            return view('out-of-office-tickets-menu', compact('tickets', 'tickets_waiting_approval'));

            
        } else {
            $tickets = Out_Of_Office_Tickets_Model::where('user_id', auth()->id())
                ->whereIn('status', ['1', '2'])
                ->get();
            return view('out-of-office-tickets-menu', compact('tickets'));
        }
        
    }

    public function Create_Out_Of_Office_Ticket(Request $request){
        try {
            $validate_data = $request->validate([
                'type_of_leave' => 'required',
                'days_of_leave' => 'required',
                'reasons_for_leave' => 'required|string|max:255',
                'start_date' => 'required',
                'end_date' => 'required',
            ]);

            $validate_data['user_id'] = auth()->id();
            $validate_data['status'] = '2';
            $validate_data['start_date'] = strip_tags($validate_data['start_date']);
            $validate_data['end_date'] = strip_tags($validate_data['end_date']);
            $validate_data['days_of_leave'] = strip_tags($validate_data['days_of_leave']);
            $validate_data['reasons_for_leave'] = strip_tags($validate_data['reasons_for_leave']);
            $validate_data['type_of_leave'] = strip_tags($validate_data['type_of_leave']);

            $ticket = Out_Of_Office_Tickets_Model::create($validate_data);
            if ($request->hasFile('attachments')) { //Kiểm tra xem có file nào được upload lên không

                foreach ($request->file('attachments') as $file) { //Duyệt qua từng file được upload lên
                    $originalName = $file->getClientOriginalName();
                    $folderPath = '9/'.$ticket->id;
                    $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục '/'
                    
                    Attachments_Model::create([
                        'type_of_ticket' => 9,
                        'ticket_id' => $ticket->id,
                        'file_path' => $filePath,   
                        'name' => $originalName,// Lưu tên gốc của file vào cơ sở dữ liệu
                    ]);
                }
                
            }

            tracking_info_service::add(
                $ticket->id, 
                auth()->id(), 
                9,
                'created ticket at'
            );

            $leader_email = User::where('id', $ticket->user_owner->leader_id)->value('email');
            $send_approve_ticket = Out_Of_Office_Tickets_Model::with('user_owner', 'active_attachments')->findOrFail($ticket->id);
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
                $send_approval = Http::post(config('services.api_service.out_of_office_request_url'), [
                    'ticket_id' => $ticket->id,
                    'ticket_owner' => $send_approve_ticket->user_owner->fullname,
                    'ticket_owner_email' => $send_approve_ticket->user_owner->email,
                    'type_of_leave' => match ($send_approve_ticket->type_of_leave) 
                    {
                        '1' => 'Xin nghỉ phép',
                        '2' => 'Xin đi trễ',
                        '3' => 'Xin về sớm',
                        '4' => 'Xin không chấm công vào',
                        '5' => 'Xin không chấm công ra',
                        '6' => 'Quên chấm công vào/ra',
                        default => 'Unknown',
                    },
                    'reasons_for_leave' => $send_approve_ticket->reasons_for_leave,
                    'days_of_leave' => $send_approve_ticket->days_of_leave,
                    'start_date' => $send_approve_ticket->start_date->format('Y-m-d H:i:s'),
                    'end_date' => $send_approve_ticket->end_date->format('Y-m-d H:i:s'),
                    'attachments' => $attachments,
                    'leader_email' => $leader_email,
                ]);

                if ($send_approval->successful()) {
                    tracking_info_service::add(
                        $ticket->id, 
                        auth()->id(), 
                        9,
                        'sent approval request at',
                    );
                    return response()->json([
                        'success' => true,
                        'message' => 'Ticket created successfully & Approval request sent !',
                        'user_owner' => $ticket->user_owner->fullname,
                        'start_date' => $ticket->start_date->format('Y-m-d H:i:s'),
                        'end_date' => $ticket->end_date->format('Y-m-d H:i:s'),
                        'type_of_leave' => match ($ticket->type_of_leave) 
                            {
                                '1' => 'Xin nghỉ phép',
                                '2' => 'Xin đi trễ',
                                '3' => 'Xin về sớm',
                                '4' => 'Xin không chấm công vào',
                                '5' => 'Xin không chấm công ra',
                                '6' => 'Quên chấm công vào/ra',
                                default => 'Unknown',
                            },
                        'reasons_for_leave' => $ticket->reasons_for_leave,
                        'status' => match ($ticket->status) 
                            {
                                '1' => 'Open',
                                '2' => 'Waiting for approval',
                                '3' => 'Completed',
                                '4' => 'Rejected',
                                default => 'Unknown',
                            },
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
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ticket due to: ',
                'error' => $e->getMessage(), // Có thể bỏ ở môi trường production
            ], 500);
        }

    }

    public function Show_Out_Of_Office_Ticket_Details($id){
        $ticket = Out_Of_Office_Tickets_Model::with(['user_owner', 'active_attachments','ticket_tracking_info','ticket_comments.attachments', 'ticket_comments.user'])->findOrFail($id);
        return view('out-of-office-tickets-menu-details', compact('ticket'));
    }

    public function Edit_Out_Of_Office_Ticket(Request $request, $id){
        try {
        $ticket = Out_Of_Office_Tickets_Model::with('user_owner')->findOrFail($id);
        
        
            if ($ticket->status == '1') {
                $validate_data = $request->validate([
                    'type_of_leave' => 'required',
                    'reasons_for_leave' => 'required',
                    'days_of_leave' => 'required',
                    'start_date' => 'required',
                    'end_date' => 'required',
                    'attachments.*' => 'file|max:20480|mimes:jpg,png,pdf,jpeg,xlsx'
            ]);

                $validate_data['type_of_leave'] = strip_tags($validate_data['type_of_leave']);
                $validate_data['reasons_for_leave'] = strip_tags($validate_data['reasons_for_leave']);
                $validate_data['days_of_leave'] = strip_tags($validate_data['days_of_leave']);
                $validate_data['start_date'] = strip_tags($validate_data['start_date']);
                $validate_data['end_date'] = strip_tags($validate_data['end_date']);

                $ticket->update($validate_data);

                tracking_info_service::add(
                    $ticket->id, 
                    auth()->id(), 
                    9,
                    'edited ticket at'
                );


                if ($request->hasFile('attachments')) { //Kiểm tra xem có file nào được upload lên không

                    foreach ($request->file('attachments') as $file) { //Duyệt qua từng file được upload lên
                        $originalName = $file->getClientOriginalName();
                        $folderPath = '9/'.$id;
                        $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục 'attachments' đã được cấu hình trong config/filesystems.php, với đường dẫn là 'attachments/1/{ticket_id}/{original_file_name}'
                        
                        Attachments_Model::create([
                            'type_of_ticket' => 9, // Giả sử 1 là mã cho software ticket
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

    public function Add_Comment_Out_Of_Office_Ticket(Request $request, $id){
        $comment_info_input = $request->validate([
            'comment' => 'required_without_all:attachments|string|nullable',
            'attachments' => 'required_without_all:comment|array|nullable',
            'attachments.*' => 'file|max:20480|mimes:jpg,jpeg,png,pdf,xlsx,docx',
        ]);

        $comment_info_input['comment'] = strip_tags($comment_info_input['comment']);
        $comment_info_input['ticket_id'] = $id;
        $comment_info_input['type_of_ticket'] = 9; 
        $comment_info_input['user_id'] = auth()->id();
        
        
        $comment = Comments_Model::create($comment_info_input);
        

        if($request->hasFile('attachments'))
        {
            foreach($request->file('attachments') as $file)
            {
                $originalName = $file->getClientOriginalName();
                $folderPath = '9/'.$id;
                $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục 'attachments' đã được cấu hình trong config/filesystems.php, với đường dẫn là 'attachments/1/{ticket_id}/{original_file_name}'
                
                Attachments_Model::create([
                    'type_of_ticket' => 9,
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

    public function Send_Approve_Out_Of_Office_Ticket($id){
        try {
            $ticket = Out_Of_Office_Tickets_Model::with('user_owner', 'active_attachments')->findOrFail($id);

            if($ticket->status != '1'){
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể send approve do ticket không ở trạng thái "Open"',
                ], 400);
            } else {
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
                    $send_approval = Http::post(config('services.api_service.out_of_office_request_url'), [
                        'ticket_id' => $ticket->id,
                        'ticket_owner' => $ticket->user_owner->fullname,
                        'ticket_owner_email' => $ticket->user_owner->email,
                        'type_of_leave' => match ($ticket->type_of_leave) 
                        {
                            '1' => 'Xin nghỉ phép',
                            '2' => 'Xin đi trễ',
                            '3' => 'Xin về sớm',
                            '4' => 'Xin không chấm công vào',
                            '5' => 'Xin không chấm công ra',
                            '6' => 'Quên chấm công vào/ra',
                            default => 'Unknown',
                        },
                        'reasons_for_leave' => $ticket->reasons_for_leave,
                        'days_of_leave' => $ticket->days_of_leave,
                        'start_date' => $ticket->start_date->format('Y-m-d H:i:s'),
                        'end_date' => $ticket->end_date->format('Y-m-d H:i:s'),
                        'attachments' => $attachments,
                        'leader_email' => User::where('id', $ticket->user_owner->leader_id)->value('email'),
                    ]);

                    if ($send_approval->successful()) {
                        tracking_info_service::add(
                            $ticket->id, 
                            auth()->id(), 
                            9,
                            'sent approval request at',
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
                            'message' => 'Failed to send approve due to: ' . $send_approval->body(),
                        ], 500);
                    } 


            } catch (\Exception $e) {
                // Xử lý lỗi nếu có
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send approve due to: ' . $e->getMessage(),
                ], 500);
            }
            } 
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send approve due to ' .$e->getMessage(),
            ], 500);
        }
    }

    public function Approve_Out_Of_Office_Ticket($id) {
        try {
            $ticket = Out_Of_Office_Tickets_Model::with('user_owner', 'active_attachments')->findOrFail($id);

            if($ticket->status != '2'){
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể approve do ticket không ở trạng thái "Waiting approval"',
                ], 400);
            } else {
                $ticket->status = '3';
                $ticket->save();


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
                    $send_approval = Http::post(config('services.api_service.out_of_office_approve_reject_from_web'), [
                        'status' => match ($ticket->status)
                        {
                            '1' => 'Open',
                            '2' => 'Waiting for approval',
                            '3' => 'Completed',
                            '4' => 'Rejected',
                            default => 'Unknown',
                        },
                        'leader_name' => User::where('id', $ticket->user_owner->leader_id)->value('fullname'),
                        'ticket_id' => $ticket->id,
                        'ticket_owner' => $ticket->user_owner->fullname,
                        'ticket_owner_email' => $ticket->user_owner->email,
                        'type_of_leave' => match ($ticket->type_of_leave) 
                        {
                            '1' => 'Xin nghỉ phép',
                            '2' => 'Xin đi trễ',
                            '3' => 'Xin về sớm',
                            '4' => 'Xin không chấm công vào',
                            '5' => 'Xin không chấm công ra',
                            '6' => 'Quên chấm công vào/ra',
                            default => 'Unknown',
                        },
                        'reasons_for_leave' => $ticket->reasons_for_leave,
                        'days_of_leave' => $ticket->days_of_leave,
                        'start_date' => $ticket->start_date->format('Y-m-d H:i:s'),
                        'end_date' => $ticket->end_date->format('Y-m-d H:i:s'),
                        'attachments' => $attachments,
                        'leader_email' => User::where('id', $ticket->user_owner->leader_id)->value('email'),
                    ]);

                    if ($send_approval->successful()) {
                        tracking_info_service::add(
                            $ticket->id, 
                            auth()->id(), 
                            9,
                            'approved ticket at'
                        );
                        return response()->json([
                            'success' => true,
                            'message' => 'Ticket approved and notification sent !',
                            
                        ]);
                    } else {
                        // Xử lý lỗi nếu phản hồi không thành công
                        return response()->json([
                            'success' => false,
                            'message' => 'Failed to approve due to: ' . $send_approval->body(),
                        ], 500);
                    }
                } catch (\Exception $e) {
                    // Xử lý lỗi nếu có
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to approve due to: ' . $e->getMessage(),
                    ], 500);
                }
            } 
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed approve ticket due to ' .$e->getMessage(),
            ], 500);
        }
    }

    // public function Reject_Out_Of_Office_Ticket($id) {
    //     try {
    //         $ticket = Out_Of_Office_Tickets_Model::with('user_owner')->findOrFail($id);

    //         if($ticket->status != '2'){
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Không thể reject do ticket không ở trạng thái "Waiting approval"',
    //             ], 400);
    //         } else {
    //             $ticket->status = '4';
    //             $ticket->save();

    //             tracking_info_service::add(
    //                 $ticket->id, 
    //                 auth()->id(), 
    //                 9,
    //                 'rejected ticket at'
    //             );

    //             return response()->json([
    //                 'success' => true,
    //                 'message' => 'Ticket rejected successfully',
    //             ]);
    //         } 
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to reject ticket due to ' .$e->getMessage(),
    //         ], 500);
    //     }
    // }

    public function Reject_Out_Of_Office_Ticket($id) {
        try {
            $ticket = Out_Of_Office_Tickets_Model::with('user_owner', 'active_attachments')->findOrFail($id);

            if($ticket->status != '2'){
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể reject do ticket không ở trạng thái "Waiting approval"',
                ], 400);
            } else {
                $ticket->status = '4';
                $ticket->save();


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
                    $send_approval = Http::post(config('services.api_service.out_of_office_approve_reject_from_web'), [
                        'status' => match ($ticket->status)
                        {
                            '1' => 'Open',
                            '2' => 'Waiting for approval',
                            '3' => 'Completed',
                            '4' => 'Rejected',
                            default => 'Unknown',
                        },
                        'leader_name' => User::where('id', $ticket->user_owner->leader_id)->value('fullname'),
                        'ticket_id' => $ticket->id,
                        'ticket_owner' => $ticket->user_owner->fullname,
                        'ticket_owner_email' => $ticket->user_owner->email,
                        'type_of_leave' => match ($ticket->type_of_leave) 
                        {
                            '1' => 'Xin nghỉ phép',
                            '2' => 'Xin đi trễ',
                            '3' => 'Xin về sớm',
                            '4' => 'Xin không chấm công vào',
                            '5' => 'Xin không chấm công ra',
                            '6' => 'Quên chấm công vào/ra',
                            default => 'Unknown',
                        },
                        'reasons_for_leave' => $ticket->reasons_for_leave,
                        'days_of_leave' => $ticket->days_of_leave,
                        'start_date' => $ticket->start_date->format('Y-m-d H:i:s'),
                        'end_date' => $ticket->end_date->format('Y-m-d H:i:s'),
                        'attachments' => $attachments,
                        'leader_email' => User::where('id', $ticket->user_owner->leader_id)->value('email'),
                    ]);

                    if ($send_approval->successful()) {
                        tracking_info_service::add(
                            $ticket->id, 
                            auth()->id(), 
                            9,
                            'rejected ticket at'
                        );
                        return response()->json([
                            'success' => true,
                            'message' => 'Ticket rejected and notification sent !',
                            
                        ]);
                    } else {
                        // Xử lý lỗi nếu phản hồi không thành công
                        return response()->json([
                            'success' => false,
                            'message' => 'Failed to reject due to: ' . $send_approval->body(),
                        ], 500);
                    }
                } catch (\Exception $e) {
                    // Xử lý lỗi nếu có
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to reject due to: ' . $e->getMessage(),
                    ], 500);
                }
            } 
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed reject ticket due to ' .$e->getMessage(),
            ], 500);
        }
    }

    public function Out_Of_Office_Re_Open($id){
        try {
            $ticket = Out_Of_Office_Tickets_Model::with('user_owner')->findOrFail($id);

            if($ticket->status != '4'){
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể mở lại ticket do ticket không ở trạng thái "Rejected"',
                ], 400);
            } else {
                $ticket->status = '1';
                $ticket->save();

                tracking_info_service::add(
                    $ticket->id, 
                    auth()->id(), 
                    9,
                    're-opened ticket at'
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Ticket re-opened successfully',
                ]);
            } 
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to re-open ticket due to ' .$e->getMessage(),
            ], 500);
        }
    }
}
