<?php

namespace App\Http\Controllers;
use App\Models\EEG_Software_Ticket;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AttachmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Attachments_Model;

class EEGTicketsController extends Controller
{
    //
    public function Create_Software_Ticket(Request $request){
        $ticket_info_input = $request->validate([
            'ticket_reciept' => 'required',
            'support_type' => 'required',
            'priority' => 'required',
            'description' => 'required',
            'attachments.*' => 'file|max:5120|mimes:jpg,png,pdf,jpeg'
        ]);

        $ticket_info_input['ticket_reciept'] = strip_tags($ticket_info_input['ticket_reciept']);//remove code xấu do người dùng input
        $ticket_info_input['support_type'] = strip_tags($ticket_info_input['support_type']);
        $ticket_info_input['priority'] = strip_tags($ticket_info_input['priority']);
        $ticket_info_input['description'] = strip_tags($ticket_info_input['description']);
        $ticket_info_input['user_id'] = auth()->id();

        $ticket = EEG_Software_Ticket::create($ticket_info_input); //Phải tạo model EEG_Software_Ticket để có thể sử dụng hàm create() này, và phải khai báo fillable trong model đó nữa
        
        if ($request->hasFile('attachments')) {

            foreach ($request->file('attachments') as $file) {
                $filePath = $file->store('attachments', 'public'); // Lưu file vào thư mục 'storage/app/public/attachments'
                
                Attachments_Model::create([
                    'type_of_ticket' => 1, // Giả sử 1 là mã cho software ticket
                    'ticket_id' => $ticket->id,
                    'file_path' => $filePath,
                    'name' => $file->getClientOriginalName(),
                ]);
            }
            
        }





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
        $ticket = EEG_Software_Ticket::with('user_owner', 'attachments')->findOrFail($id); //gọi tới function "user" trong model EEG_Software_Ticket để lấy thông tin user của ticket đó, rồi mới trả về view
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

    public function Send_Approval_Request($id){
        $ticket = EEG_Software_Ticket::with('user_owner')->findOrFail($id);
        $ticket->status = 3;
        $ticket->save();
        $response = Http::post('https://defaultca7981a2785a463db82a3db87dfc3c.e6.environment.api.powerplatform.com:443/powerautomate/automations/direct/workflows/02e7dce1f8724f49a897de0ee8a58568/triggers/manual/paths/invoke?api-version=1&sp=%2Ftriggers%2Fmanual%2Frun&sv=1.0&sig=zC58zM_5pldekMYNMUI_yLYF-31LYLG5A2rE0tOqy6o', [
            'ticket_id'   => $ticket->id,
            'reciept' => $ticket->ticket_reciept,
        ]);

        return back()->with('success', 'Approval sent!');
    }

    

    

    
}
