<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BBNT_Partner_Onsite_Model;

class BBNTpartnerOnsiteController extends Controller
{
    //
    public function index(){ 
        if (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_BBNT_PARTNER_ONSITE_USER') || auth()->user()->hasRole('ROLE_BBNT_PARTNER_ONSITE_ADMIN')) {
            $pending_tickets = BBNT_Partner_Onsite_Model::whereIn('status', ['1', '2'])->paginate(10);
            $all_tickets = BBNT_Partner_Onsite_Model::query()->orderByDesc('created_at')->paginate(10);
            return view('bbnt-partner-onsite-menu', compact('pending_tickets', 'all_tickets'));
        } 
        else {
            $pending_tickets = BBNT_Partner_Onsite_Model::where('user_id', auth()->id()) //lọc ra ticket của user đó
                ->whereIn('status', ['1', '2']) // lọc ra ticket đang pending
                ->paginate(10);

            
            $all_tickets = BBNT_Partner_Onsite_Model::where('user_id', auth()->id())->orderByDesc('created_at')->paginate(10);
            return view('bbnt-partner-onsite-menu', compact('pending_tickets', 'all_tickets'));

            
        }
        
    }
}
