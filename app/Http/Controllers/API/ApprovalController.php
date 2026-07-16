<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EEG_Software_Ticket;
use App\Models\Out_Of_Office_Tickets_Model;
use App\Models\Invoice_Exceptional_Tickets_Model;

use App\Models\Comments_Model;
use App\Services\tracking_info_service;


class ApprovalController extends Controller
{
    //
    public function handleCallback(Request $request)
    {
        // 1. Log dữ liệu để kiểm tra (tùy chọn)
        

        // 2. Lấy dữ liệu từ Power Automate gửi sang
        $approval_response['ticket_id'] = $request->input('ticket_id');
        $approval_response['type_of_ticket'] = $request->input('type_of_ticket');
        $approval_response['outcome'] = $request->input('outcome'); // Approve hoặc Reject
        $approval_response['approver_comment'] = $request->input('approver_comment');

        

        
        
        if ($approval_response['outcome'] === 'Approve' && $approval_response['type_of_ticket'] === '1') 
        {
            $ticket = EEG_Software_Ticket::find($approval_response['ticket_id']);
            if ($ticket->status == '3') {
                tracking_info_service::add(
                    $ticket->id,
                    10,
                    1,
                    'received approved response from Power Automate',
                );
                Comments_Model::create([
                    'ticket_id' => $approval_response['ticket_id'],
                    'type_of_ticket' => $approval_response['type_of_ticket'],
                    'user_id' => 10,
                    'comment'=> $approval_response['approver_comment']

                ]);
                $ticket->status = '2'; 
                $ticket->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Trạng thái ticket đã được cập nhật'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ticket không ở trạng thái chờ phê duyệt'
                ], 200);
            }
        }

        if ($approval_response['outcome'] === 'Reject' && $approval_response['type_of_ticket'] === '1') 
        {
            $ticket = EEG_Software_Ticket::find($approval_response['ticket_id']);
            if ($ticket->status == '3') {
                tracking_info_service::add(
                    $ticket->id,
                    10,
                    1,
                    'received rejected response from Power Automate',
                );
                Comments_Model::create([
                    'ticket_id' => $approval_response['ticket_id'],
                    'type_of_ticket' => $approval_response['type_of_ticket'],
                    'user_id' => 10,
                    'comment'=> $approval_response['approver_comment']

                ]);
                $ticket->status = '2'; 
                $ticket->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Trạng thái ticket đã được cập nhật'
                ], 200);

            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ticket không ở trạng thái chờ phê duyệt'
                ], 200);
            }
        } 

        if ($approval_response['outcome'] === 'Approve' && $approval_response['type_of_ticket'] === '7') 
        {
            $ticket = Invoice_Exceptional_Tickets_Model::find($approval_response['ticket_id']);
            if ($ticket->status == '2' && ($ticket->support_type == '1' || $ticket->support_type == '2')) {
                tracking_info_service::add(
                    $ticket->id,
                    10,
                    7,
                    'received approved response from Power Automate',
                );
                Comments_Model::create([
                    'ticket_id' => $approval_response['ticket_id'],
                    'type_of_ticket' => $approval_response['type_of_ticket'],
                    'user_id' => 10,
                    'comment'=> $approval_response['approver_comment']

                ]);
                $ticket->status = '4'; 
                $ticket->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Trạng thái ticket đã được cập nhật'
                ], 200);
            } else if ($ticket->status == '2' && ($ticket->support_type == '3' || $ticket->support_type == '4')){
                tracking_info_service::add(
                    $ticket->id,
                    10,
                    7,
                    'received approved response from Power Automate',
                );
                Comments_Model::create([
                    'ticket_id' => $approval_response['ticket_id'],
                    'type_of_ticket' => $approval_response['type_of_ticket'],
                    'user_id' => 10,
                    'comment'=> $approval_response['approver_comment']

                ]);
                $ticket->highest_approved_step = '2';
                $ticket->status = '3'; 
                $ticket->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Trạng thái ticket đã được cập nhật'
                ], 200);
            } else if ($ticket->status == '3' && ($ticket->support_type == '3' || $ticket->support_type == '4')){
                tracking_info_service::add(
                    $ticket->id,
                    10,
                    7,
                    'received approved response from Power Automate',
                );
                Comments_Model::create([
                    'ticket_id' => $approval_response['ticket_id'],
                    'type_of_ticket' => $approval_response['type_of_ticket'],
                    'user_id' => 10,
                    'comment'=> $approval_response['approver_comment']

                ]);
                $ticket->status = '4'; 
                $ticket->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Trạng thái ticket đã được cập nhật'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ticket không ở trạng thái chờ phê duyệt'
                ], 200);
            }
        }

        if ($approval_response['outcome'] === 'Reject' && $approval_response['type_of_ticket'] === '7') 
        {
            $ticket = Invoice_Exceptional_Tickets_Model::find($approval_response['ticket_id']);
            if ($ticket->status == '2' && ($ticket->support_type == '1' || $ticket->support_type == '2')) {
                tracking_info_service::add(
                    $ticket->id,
                    10,
                    7,
                    'received rejected response from Power Automate',
                );
                Comments_Model::create([
                    'ticket_id' => $approval_response['ticket_id'],
                    'type_of_ticket' => $approval_response['type_of_ticket'],
                    'user_id' => 10,
                    'comment'=> $approval_response['approver_comment']

                ]);
                $ticket->status = '5'; 
                $ticket->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Trạng thái ticket đã được cập nhật'
                ], 200);
            } else if ($ticket->status == '2' && ($ticket->support_type == '3' || $ticket->support_type == '4')){
                tracking_info_service::add(
                    $ticket->id,
                    10,
                    7,
                    'received rejected response from Power Automate',
                );
                Comments_Model::create([
                    'ticket_id' => $approval_response['ticket_id'],
                    'type_of_ticket' => $approval_response['type_of_ticket'],
                    'user_id' => 10,
                    'comment'=> $approval_response['approver_comment']

                ]);
                $ticket->status = '5'; 
                $ticket->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Trạng thái ticket đã được cập nhật'
                ], 200);
            } else if ($ticket->status == '3' && ($ticket->support_type == '3' || $ticket->support_type == '4')){
                tracking_info_service::add(
                    $ticket->id,
                    10,
                    7,
                    'received rejected response from Power Automate',
                );
                Comments_Model::create([
                    'ticket_id' => $approval_response['ticket_id'],
                    'type_of_ticket' => $approval_response['type_of_ticket'],
                    'user_id' => 10,
                    'comment'=> $approval_response['approver_comment']

                ]);
                $ticket->status = '5'; 
                $ticket->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Trạng thái ticket đã được cập nhật'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ticket không ở trạng thái chờ phê duyệt'
                ], 200);
            }
        }

        

        if ($approval_response['outcome'] === 'Approve' && $approval_response['type_of_ticket'] === '9') 
        {
            $ticket = Out_Of_Office_Tickets_Model::find($approval_response['ticket_id']);
            if ($ticket->status == '2') {
                tracking_info_service::add(
                    $ticket->id,
                    10,
                    9,
                    'received approved response from Power Automate',
                );
                Comments_Model::create([
                    'ticket_id' => $approval_response['ticket_id'],
                    'type_of_ticket' => $approval_response['type_of_ticket'],
                    'user_id' => 10,
                    'comment'=> $approval_response['approver_comment']

                ]);
                $ticket->status = '3'; 
                $ticket->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Trạng thái ticket đã được cập nhật'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ticket không ở trạng thái chờ phê duyệt'
                ], 200);
            }
        }

        if ($approval_response['outcome'] === 'Reject' && $approval_response['type_of_ticket'] === '9') 
        {
            $ticket = Out_Of_Office_Tickets_Model::find($approval_response['ticket_id']);
            if ($ticket->status == '2') {
                tracking_info_service::add(
                    $ticket->id,
                    10,
                    9,
                    'received rejected response from Power Automate',
                );
                Comments_Model::create([
                    'ticket_id' => $approval_response['ticket_id'],
                    'type_of_ticket' => $approval_response['type_of_ticket'],
                    'user_id' => 10,
                    'comment'=> $approval_response['approver_comment']

                ]);
                $ticket->status = '4'; 
                $ticket->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Trạng thái ticket đã được cập nhật'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ticket không ở trạng thái chờ phê duyệt'
                ], 200);
            }
        }


        


        
        
    }

}
