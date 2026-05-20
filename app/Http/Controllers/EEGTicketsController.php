<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UserController;
use App\Models\EEG_Software_Ticket;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AttachmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Attachments_Model;
use App\Models\Comments_Model;
use App\Services\tracking_info_service;
use App\Models\User;

class EEGTicketsController extends Controller
{
    //
    public function Create_Software_Ticket(Request $request){
        $ticket_info_input = $request->validate([
            'ticket_reciept' => 'required',
            'support_type' => 'required',
            'priority' => 'required',
            'description' => 'required',
            'attachments.*' => 'file|max:5120|mimes:jpg,png,pdf,jpeg,xlsx'
        ]);

        $ticket_info_input['ticket_reciept'] = strip_tags($ticket_info_input['ticket_reciept']);//remove code xấu do người dùng input
        $ticket_info_input['support_type'] = strip_tags($ticket_info_input['support_type']);
        $ticket_info_input['priority'] = strip_tags($ticket_info_input['priority']);
        $ticket_info_input['description'] = strip_tags($ticket_info_input['description']);
        $ticket_info_input['user_id'] = auth()->id();

        $ticket = EEG_Software_Ticket::create($ticket_info_input); //Phải tạo model EEG_Software_Ticket để có thể sử dụng hàm create() này, và phải khai báo fillable trong model đó nữa
        
        if ($request->hasFile('attachments')) { //Kiểm tra xem có file nào được upload lên không

            foreach ($request->file('attachments') as $file) { //Duyệt qua từng file được upload lên
                $originalName = $file->getClientOriginalName();
                $folderPath = '1/'.$ticket->id;
                $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục '/'
                
                Attachments_Model::create([
                    'type_of_ticket' => 1, // Giả sử 1 là mã cho software ticket
                    'ticket_id' => $ticket->id,
                    'file_path' => $filePath,   
                    'name' => $originalName,// Lưu tên gốc của file vào cơ sở dữ liệu
                ]);
            }
            
        }

        tracking_info_service::add(
            $ticket->id, 
            auth()->id(), 
            1,
            'created ticket at'
        );


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

    public function Show_Pending_Tickets(){ 
        if (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_TICKET_SW_ADMIN')) {
            $tickets = EEG_Software_Ticket::whereIn('status', ['1', '2', '3'])->get();
            $tickets_waiting_approval = EEG_Software_Ticket::where('status', 3)->get();
            return view('software-tickets-menu', compact('tickets', 'tickets_waiting_approval'));
        } 
        else {
            $tickets = EEG_Software_Ticket::where('user_id', auth()->id()) //lọc ra ticket của user đó
                ->whereIn('status', ['1', '2', '3']) // lọc ra ticket đang pending
                ->get();

            

            $tickets_waiting_approval = EEG_Software_Ticket::where('status', 3)
                ->whereHas('user_owner', function ($query) { //Lọc ra những ticket có user_owner có leader_id là id của user đang đăng nhập, tức là lọc ra những ticket của những user mà user đang đăng nhập là leader của họ, rồi mới lấy ra những ticket đó để trả về view
                    $query->where('leader_id', auth()->id());
                })
                ->get();
            
            return view('software-tickets-menu', compact('tickets', 'tickets_waiting_approval'));

            
        }
        
    }

    public function Show_Software_Ticket_Details($id, $type_of_ticket = 1){
        $ticket = EEG_Software_Ticket::with('user_owner', 'active_attachments', 'ticket_comments.attachments', 'ticket_comments.user')->findOrFail($id);
        
        // $ticket->ticket_comments = DB::table('comments_table') 
        //     ->join('users', 'comments_table.user_id', '=', 'users.id') 
        //     ->where('comments_table.ticket_id', $id) 
        //     ->where('comments_table.type_of_ticket', $type_of_ticket) 
        //     ->select('comments_table.*', 'users.fullname') 
        //     ->get();
        // dd($ticket->ticket_comments);

        $ticket->tracking_info = DB::table('tracking_info') //gọi trực tiếp tới bảng tracking_info để lấy thông tin tracking của ticket này, vì trong model tracking_info_model có điều kiện where(['type_of_ticket' => 1]) rồi nên khi gọi tới function "tracking_info" trong model EEG_Software_Ticket để lấy thông tin tracking của ticket này thì nó sẽ chỉ lấy những tracking có type_of_ticket là 1 (software ticket) thôi, còn nếu muốn lấy thêm thông tin user của tracking đó nữa thì phải join thêm với bảng users nữa
            ->join('users', 'tracking_info.user_id', '=', 'users.id') //join với bảng users để lấy thông tin user của tracking đó
            ->where('tracking_info.ticket_id', $id) //lọc ra tracking của ticket này dựa vào ticket_id
            ->where('tracking_info.type_of_ticket', $type_of_ticket) //lọc ra tracking của software ticket dựa vào type_of_ticket
            ->select('tracking_info.*', 'users.fullname') //chọn tất cả cột của tracking_info và cột fullname của users để trả về
            ->get();//Lấy tất cả tracking của ticket này rồi trả về dưới dạng collection
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

    public function Send_Approval_Request($id, Request $request){
        $ticket = EEG_Software_Ticket::with('user_owner', 'active_attachments')->findOrFail($id);
        $approval_type = $request->input('approval_type');

        $attachments = $ticket->active_attachments->map(function ($file) { //Duyệt qua từng attachment của ticket này, rồi lấy đường dẫn file để đọc nội dung file đó, rồi mã hóa nội dung file đó thành base64 để gửi qua API
            $filePath = ('attachments/' . $file->file_path);

            return [
                'fileName'    => basename($filePath),
                'fileContent' => base64_encode(file_get_contents($filePath)),
            ];
        });

        $leader_email = User::where('id', $ticket->user_owner->leader_id)->value('email'); //Lấy email của leader của user owner của ticket này để gửi vào API, nếu không có leader thì trả về null
        // dd($leader_email);
        try 
        {
            $send_approval = Http::post('https://defaultca7981a2785a463db82a3db87dfc3c.e6.environment.api.powerplatform.com:443/powerautomate/automations/direct/workflows/02e7dce1f8724f49a897de0ee8a58568/triggers/manual/paths/invoke?api-version=1&sp=%2Ftriggers%2Fmanual%2Frun&sv=1.0&sig=zC58zM_5pldekMYNMUI_yLYF-31LYLG5A2rE0tOqy6o', [
                'type_of_ticket' => 1,
                'ticket_id' => $ticket->id,
                'ticket_owner'   => $ticket->user_owner->fullname,
                'reciept' => $ticket->ticket_reciept,
                'description' => $ticket->description,
                'attachments' => $attachments,
                'approval_type' => $approval_type,
                'leader_email' => $leader_email,
            ]);
            if ($send_approval->successful()) {
                // Xử lý phản hồi thành công nếu cần
                $ticket->status = 3;
                $ticket->save();
                return response()->json([
                    'success' => true,
                    'message' => 'Approval request sent successfully',
                ]);
            } else {
                // Xử lý lỗi nếu phản hồi không thành công
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send approval request. API responded with status: ' . $send_approval->body(),
                ], 500);
            } 
        } catch (\Exception $e) {
            // Xử lý lỗi nếu có
            return response()->json([
                'success' => false,
                'message' => 'Failed to send approval request: ' . $e->getMessage(),
            ], 500);
        }

    }

    public function Close_Software_Ticket(Request $request, $id){
        
        $ticket_info_input = $request->validate([
            'ticket_status' => 'required',
            'issue_owner' => 'required',
            'ticket_comment' => 'nullable'
        ]);

        $ticket_info_input['ticket_status'] = strip_tags($ticket_info_input['ticket_status']);
        $ticket_info_input['issue_owner'] = strip_tags($ticket_info_input['issue_owner']);
        $ticket_info_input['ticket_comment'] = strip_tags($ticket_info_input['ticket_comment']);
        
        $ticket = EEG_Software_Ticket::with('user_owner')->findOrFail($id);
        $ticket->status = $ticket_info_input['ticket_status'];
        $ticket->issue_owner = $ticket_info_input['issue_owner'];
        $ticket->save();

        switch ($ticket_info_input['ticket_status']) {
            case '4':
                $action = 'completed ticket at';
                break;
            case '5':
                $action = 'rejected ticket at';
                break;
            case '6':
                $action = 'canceled ticket at';
                break;
            default:
                $action = 'updated ticket status to ' . $ticket_info_input['ticket_status'] . ' at';
        }
        tracking_info_service::add(
            $ticket->id,
            auth()->id(),
            1, //1 là mã cho software ticket
            $action,
        );
        return response()->json([
            'success' => true,
            'message' => 'Ticket completed !',
        ]);
    }

    public function Edit_Software_Ticket(Request $request, $id){

        $ticket_info_input = $request->validate([
            'ticket_reciept' => 'required',
            'support_type' => 'required',
            'priority' => 'required',
            'description' => 'required',
            'attachments.*' => 'file|max:5120|mimes:jpg,png,pdf,jpeg,xlsx'
        ]);

        $ticket_info_input['ticket_reciept'] = trim(strip_tags($ticket_info_input['ticket_reciept']));
        $ticket_info_input['support_type'] = trim(strip_tags($ticket_info_input['support_type']));
        $ticket_info_input['priority'] = trim(strip_tags($ticket_info_input['priority']));
        $ticket_info_input['description'] = trim(strip_tags($ticket_info_input['description']));

        $ticket = EEG_Software_Ticket::with('user_owner')->findOrFail($id);
        $ticket->ticket_reciept = $ticket_info_input['ticket_reciept'];
        $ticket->support_type = $ticket_info_input['support_type'];
        $ticket->priority = $ticket_info_input['priority'];
        $ticket->description = $ticket_info_input['description'];

        $ticket->save();

        if ($request->hasFile('attachments')) { //Kiểm tra xem có file nào được upload lên không

            foreach ($request->file('attachments') as $file) { //Duyệt qua từng file được upload lên
                $originalName = $file->getClientOriginalName();
                $folderPath = '1/'.$id;
                $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục 'attachments' đã được cấu hình trong config/filesystems.php, với đường dẫn là 'attachments/1/{ticket_id}/{original_file_name}'
                
                Attachments_Model::create([
                    'type_of_ticket' => 1, // Giả sử 1 là mã cho software ticket
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

        return back()->with('success');


    }

    public function Add_Comment_Software_Ticket(Request $request, $id){
        $comment_info_input = $request->validate([
            'comment' => 'required',
        ]);

        $comment_info_input['comment'] = strip_tags($comment_info_input['comment']);
        $comment_info_input['ticket_id'] = $id;
        $comment_info_input['type_of_ticket'] = 1; //1 là mã cho software ticket
        $comment_info_input['user_id'] = auth()->id();
        
        
        $comment = Comments_Model::create($comment_info_input);
        // if ($request->hasFile('attachments')) { //Kiểm tra xem có file nào được upload lên không

        //     foreach ($request->file('attachments') as $file) { //Duyệt qua từng file được upload lên
        //         $originalName = $file->getClientOriginalName();
        //         $folderPath = '1/'.$ticket->id;
        //         $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục '/'
                
        //         Attachments_Model::create([
        //             'type_of_ticket' => 1, // Giả sử 1 là mã cho software ticket
        //             'ticket_id' => $ticket->id,
        //             'file_path' => $filePath,   
        //             'name' => $originalName,// Lưu tên gốc của file vào cơ sở dữ liệu
        //         ]);
        //     }
            
        // }

        if($request->hasFile('attachments'))
        {
            foreach($request->file('attachments') as $file)
            {
                $originalName = $file->getClientOriginalName();
                $folderPath = '1/'.$id;
                $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục 'attachments' đã được cấu hình trong config/filesystems.php, với đường dẫn là 'attachments/1/{ticket_id}/{original_file_name}'
                
                Attachments_Model::create([
                    'type_of_ticket' => 1,
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

    public function Approve_Ticket(Request $request, $id){
        $ticket = EEG_Software_Ticket::with('user_owner')->findOrFail($id);
        if ($ticket->status != 3) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot approve ticket. Ticket is not in pending approval status.',
            ], 400);
        }
        else {
            $ticket->status = 2;
            $ticket->save();

            tracking_info_service::add(
                $ticket->id,
                auth()->id(),
                1, //1 là mã cho software ticket
                'approved ticket at',
            );

            return response()->json([
                'success' => true,
                'message' => 'Ticket approved !',
            ]);
        }
        
    }

    
    

    

    

    
}
