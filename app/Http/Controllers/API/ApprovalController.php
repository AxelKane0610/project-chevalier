<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EEG_Software_Ticket;
use App\Models\Comments_Model;
use App\Services\tracking_info_service;


class ApprovalController extends Controller
{
    //
    public function handleCallback(Request $request)
    {
        // 1. Log dữ liệu để kiểm tra (tùy chọn)
        Log::info('Power Automate Callback:', $request->all());

        // 2. Lấy dữ liệu từ Power Automate gửi sang
        $approval_response['ticket_id'] = $request->input('ticket_id');
        $approval_response['type_of_ticket'] = $request->input('type_of_ticket');
        $approval_response['outcome'] = $request->input('outcome'); // Approve hoặc Reject
        $approval_response['approver_comment'] = $request->input('approver_comment');

        

        // 3. Tìm ticket và comment vào
        $ticket = Comments_Model::create([
            'ticket_id' => $approval_response['ticket_id'],
            'type_of_ticket' => $approval_response['type_of_ticket'],
            'user_id' => 10,
            'comment'=> $approval_response['approver_comment']

        ]);

        if ($approval_response['outcome'] === 'Approve' and $approval_response['type_of_ticket'] === '1') 
        {
            $ticket = EEG_Software_Ticket::find($approval_response['ticket_id']);
            if ($ticket->status == 3) {
                tracking_info_service::add(
                    $ticket->id,
                    10,
                    1,
                    'received approved response from Power Automate',
                );
                $ticket->status = 2; 
                $ticket->save();
            } 
        }

        if ($approval_response['outcome'] === 'Reject' and $approval_response['type_of_ticket'] === '1') 
        {
            $ticket = EEG_Software_Ticket::find($approval_response['ticket_id']);
            if ($ticket->status == 3) {
                tracking_info_service::add(
                    $ticket->id,
                    10,
                    1,
                    'received rejected response from Power Automate',
                );
                $ticket->status = 2; 
                $ticket->save();
            }
        } 

        if (!$ticket) {
            return response()->json(['message' => 'Ticket không tồn tại'], 404);
        }

        


        // 4. Trả về response cho Power Automate biết đã nhận tin thành công
        return response()->json([
            'status' => 'success',
            'message' => 'Trạng thái ticket đã được cập nhật'
        ], 200);
    }

}
