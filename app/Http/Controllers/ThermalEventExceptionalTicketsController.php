<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Thermal_Event_Exceptional_Tickets_Model;
use App\Models\Attachments_Model;
use App\Models\Comments_Model;
use App\Models\User;
use App\Services\tracking_info_service;

class ThermalEventExceptionalTicketsController extends Controller
{
    //
    public function Show_Pending_Tickets(){ 
        if (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_THERMAL_EVENT_ADMIN')) {
            $tickets = Thermal_Event_Exceptional_Tickets_Model::whereIn('status', ['1', '2', '3'])->get();
            $tickets_waiting_approval = Thermal_Event_Exceptional_Tickets_Model::where('status', 3)->get();
            return view('thermal-event-tickets-menu', compact('tickets', 'tickets_waiting_approval'));
        } 
        else {
            $tickets = Thermal_Event_Exceptional_Tickets_Model::where('user_id', auth()->id()) //lọc ra ticket của user đó
                ->whereIn('status', ['1', '2', '3']) // lọc ra ticket đang pending
                ->get();

            

            $tickets_waiting_approval = Thermal_Event_Exceptional_Tickets_Model::where('status', 3)
                ->whereHas('user_owner', function ($query) { //Lọc ra những ticket có user_owner có leader_id là id của user đang đăng nhập, tức là lọc ra những ticket của những user mà user đang đăng nhập là leader của họ, rồi mới lấy ra những ticket đó để trả về view
                    $query->where('leader_id', auth()->id());
                })
                ->get();
            
            return view('thermal-event-tickets-menu', compact('tickets', 'tickets_waiting_approval'));

            
        }
        
    }

    public function Create_Thermal_Event_Ticket(Request $request){
        $validate_data = $request->validate([
            'ticket_receipt' => 'required',
            'serial_number' => 'required',
            'product_number'=> 'required',
            'product_model' => 'required',
            'description' => 'required',
            'cdax_id' => 'required',
            'customer_type' => 'required',
            'company_customer_name' => 'required',
            'part_mo_number' => 'required',
            'part_number' => 'required',
            'part_description' => 'required',
            'part_ct_number' => 'required',
            'user_observations' => 'required',
            'attachments.*' => 'file|max:20480|mimes:jpg,png,pdf,jpeg,xlsx'
        ]);

        $validate_data['user_id'] = auth()->id();
        $validate_data['ticket_receipt'] = strip_tags($validate_data['ticket_receipt']);
        $validate_data['status'] = 2;
        $validate_data['serial_number'] = strip_tags($validate_data['serial_number']);
        $validate_data['product_number'] = strip_tags($validate_data['product_number']);
        $validate_data['description'] = strip_tags($validate_data['description']);
        $validate_data['cdax_id'] = strip_tags($validate_data['cdax_id']);
        $validate_data['customer_type'] = strip_tags($validate_data['customer_type']);
        $validate_data['company_customer_name'] = strip_tags($validate_data['company_customer_name']);
        $validate_data['part_mo_number'] = strip_tags($validate_data['part_mo_number']);
        $validate_data['part_number'] = strip_tags($validate_data['part_number']);
        $validate_data['part_description'] = strip_tags($validate_data['part_description']);
        $validate_data['part_ct_number'] = strip_tags($validate_data['part_ct_number']);
        $validate_data['user_observations'] = strip_tags($validate_data['user_observations']);

        $ticket = Thermal_Event_Exceptional_Tickets_Model::create($validate_data);

        if ($request->hasFile('attachments')) { //Kiểm tra xem có file nào được upload lên không

            foreach ($request->file('attachments') as $file) { //Duyệt qua từng file được upload lên
                $originalName = $file->getClientOriginalName();
                $folderPath = '10/'.$ticket->id;
                $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục '/'
                
                Attachments_Model::create([
                    'type_of_ticket' => 10, // Giả sử 1 là mã cho software ticket
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

        $ticket->save();
        return response()->json([
            'success' => true,
            'message' => 'Ticket created successfully',
        ]);
    }
}
