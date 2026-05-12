<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EEG_Software_Ticket;
use App\Models\Comments_Model;

class ApprovalController extends Controller
{
    //
    public function handleCallback(Request $request)
    {
        // 1. Log dữ liệu để kiểm tra (tùy chọn)
        \Log::info('Power Automate Callback:', $request->all());

        // 2. Lấy dữ liệu từ Power Automate gửi sang
        $approval_response['ticket_id'] = $request->input('ticket_id');
        $approval_response['type_of_ticket'] = $request->input('type_of_ticket');
        $approval_response['outcome'] = $request->input('outcome'); // Approve hoặc Reject
        $approval_response['approver_comment'] = $request->input('approver_comment');

        

        // 3. Tìm ticket và comment vào
        $ticket = Comments_Model::create([
            'ticket_id' => $approval_response['ticket_id'],
            'type_of_ticket' => $approval_response['type_of_ticket'],
            'user_id' => 0,
            'comment'=> $approval_response['approver_comment']

        ]);

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
