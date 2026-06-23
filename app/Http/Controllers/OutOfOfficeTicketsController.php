<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Out_Of_Office_Tickets_Model;
use App\Models\Attachments_Model;
use App\Models\Comments_Model;
use App\Services\tracking_info_service;

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

        try {
            $ticket->save();
            return response()->json([
                'success' => true,
                'message' => 'Ticket created successfully',
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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ticket due to: ',
                'error' => $e->getMessage(), // Có thể bỏ ở môi trường production
            ], 500);
        }

    }
}
