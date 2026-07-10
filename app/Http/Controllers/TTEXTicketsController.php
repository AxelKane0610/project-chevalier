<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TTEX_Tickets_Model;
use Illuminate\Http\Request;

class TTEXTicketsController extends Controller
{
    //
    public function Show_Pending_Tickets(){ 
        if (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_TTEX_TICKET_ADMIN')) {
            $tickets = TTEX_Tickets_Model::all();
            $tickets_good_part_pending = TTEX_Tickets_Model::where([
                ['status', '1'],
                ['part_status', '1'],
            ])
            ->get();

            $tickets_def_part_pending = TTEX_Tickets_Model::where('status', '1')
            ->whereIn('part_status', ['2', '3'])
            ->get();
            return view('ttex-tickets-menu', compact('tickets', 'tickets_good_part_pending', 'tickets_def_part_pending'));
        } 
        else {
            $tickets = TTEX_Tickets_Model::where('user_id', auth()->id())->get();
            $tickets_good_part_pending = TTEX_Tickets_Model::where([
                ['status', '1'],
                ['part_status', '1'],
                ['user_id', auth()->id()]
            ])
            ->get();

            $tickets_def_part_pending = TTEX_Tickets_Model::where([
                ['status', '1'],
                ['user_id', auth()->id()]
            ])
            ->whereIn('part_status', ['2', '3'])
            ->get();

            return view('ttex-tickets-menu', compact('tickets', 'tickets_good_part_pending', 'tickets_def_part_pending'));


            
        }
        
    }

    
}
