<?php

namespace App\Http\Controllers;
use App\Models\EEG_Software_Ticket;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EEGTicketsController extends Controller
{
    //
    public function Create_Software_Ticket(Request $request){
        $ticket_info_input = $request->validate([
            'ticket_reciept' => 'required',
            'support_type' => 'required',
            'priority' => 'required',
            'description' => 'required',
        ]);

        $ticket_info_input['ticket_reciept'] = strip_tags($ticket_info_input['ticket_reciept']);//remove code xấu do người dùng input
        $ticket_info_input['support_type'] = strip_tags($ticket_info_input['support_type']);
        $ticket_info_input['priority'] = strip_tags($ticket_info_input['priority']);
        $ticket_info_input['description'] = strip_tags($ticket_info_input['description']);
        $ticket_info_input['user_id'] = auth()->id();

        $ticket = EEG_Software_Ticket::create($ticket_info_input); //Phải tạo model EEG_Software_Ticket để có thể sử dụng hàm create() này, và phải khai báo fillable trong model đó nữa
        // return redirect('/software-tickets-menu')->with('success', 'Tạo ticket thành công!');
        return response()->json([
            'success' => true,
            'ticket_id' => $ticket->id,
            'ticket_reciept' => $ticket->ticket_reciept,
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
    }

    public function Show_Software_Ticket_Details($id){
        $ticket = EEG_Software_Ticket::with('user_owner')->findOrFail($id); //gọi tới function "user" trong model EEG_Software_Ticket để lấy thông tin user của ticket đó, rồi mới trả về view
        return view('software-tickets-menu-details', compact('ticket'));
    }

    public function Change_Software_Ticket_Status($id){
        $ticket = EEG_Software_Ticket::with('user_owner')->findOrFail($id);
        $ticket->status = 4; //đổi status thành "Đã hoàn thành"
        $ticket->save();
        return response()->json([
            'success' => true,
            'message' => 'Ticket status updated successfully',
        ]);
    }

    

    
}
