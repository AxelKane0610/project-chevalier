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

            
        }
        
    }
}
