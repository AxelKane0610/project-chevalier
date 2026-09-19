<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BBNT_Partner_Onsite_Model;
use Carbon\Carbon;
use App\Models\Attachments_Model;
use App\Models\Comments_Model;
use App\Services\tracking_info_service;

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

    public function Create_BBNT_Ticket(Request $request) {
        try {
            $new_ticket = $request->validate([
                'partner_address' => 'required',
                'partner_city' => 'required',
                'onsite_type' => 'required',
                'total_case' => 'required',
                'total_amount' => 'required',
                'notes' => 'nullable',
                'attachments.*' => 'file|max:20480|mimes:jpg,png,pdf,jpeg,xlsx'
            ]);

            $new_ticket['user_id'] = auth()->id();
            $new_ticket['status'] = '1';
            $new_ticket['partner_address'] = strip_tags($new_ticket['partner_address']);
            $new_ticket['partner_city'] = strip_tags($new_ticket['partner_city']);
            $new_ticket['onsite_type'] = strip_tags($new_ticket['onsite_type']);
            $new_ticket['total_case'] = strip_tags($new_ticket['total_case']);
            $new_ticket['total_amount'] = strip_tags(str_replace('.', '', $new_ticket['total_amount']));
            $new_ticket['notes'] = strip_tags($new_ticket['notes']);

            $prevMonth = Carbon::now()->subMonth();

            // Sinh chuỗi theo định dạng
            $document_name = sprintf(
                'BBNT-%s-THÁNG %d NĂM %d',
                match ($request->input('onsite_type')) {
                    '1' => 'MÁY TÍNH',
                    '2' => 'MÁY IN',
                    default => 'Unknown',
                },
                $prevMonth->month,
                $prevMonth->year
            );

            $new_ticket['document_name'] = $document_name;

            $new_ticket = BBNT_Partner_Onsite_Model::create($new_ticket);

            if ($request->hasFile('attachments')) { 

                foreach ($request->file('attachments') as $file) { 
                    $originalName = $file->getClientOriginalName();
                    $folderPath = '13/'.$new_ticket->id;
                    $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); 
                    
                    Attachments_Model::create([
                        'type_of_ticket' => 13, 
                        'ticket_id' => $new_ticket->id,
                        'file_path' => $filePath,   
                        'name' => $originalName,
                    ]);
                }
                
            }


            tracking_info_service::add(
                    $new_ticket->id, 
                    auth()->id(), 
                    13,
                    'created ticket at'
                );

            return response()->json([
                'success' => true,
                'message' => 'Ticket ' . $new_ticket->document_name . ' được tạo thành công !',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tạo ticket lỗi do ' .$e->getMessage(),
            ], 500);
        }



    }

    public function Ticket_Details($id) {
        $ticket = BBNT_Partner_Onsite_Model::with(['user_owner', 'active_attachments','ticket_tracking_info','ticket_comments.attachments', 'ticket_comments.user'])->findOrFail($id);
        return view('bbnt-ticket-details', compact('ticket'));
    }

    public function Add_Comment_BBNT_Ticket(Request $request, $id) {
        $ticket = BBNT_Partner_Onsite_Model::findOrFail($id);

        $validatedData = $request->validate([
            'comment' => 'required_without_all:attachments|string|nullable',
            'attachments' => 'required_without_all:comment|array|nullable',
            'attachments.*' => 'file|max:20480|mimes:jpg,jpeg,png,pdf,xlsx,docx',
        ]);

        $comment = Comments_Model::create([
            'type_of_ticket' => 13,
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'comment' => strip_tags($validatedData['comment']),
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $originalName = $file->getClientOriginalName();
                $folderPath = '13/'.$id;
                $filePath = $file->storeAs($folderPath, $originalName, 'attachments');

                Attachments_Model::create([
                    'type_of_ticket' => 13,
                    'ticket_id' => $id,
                    'comment_id' => $comment->id,
                    'file_path' => $filePath,
                    'name' => $originalName,
                ]);
            }
        }

        

        return back()->with('success');
    }

    public function Change_BBNT_Ticket_Status_To_In_Progress($id) {
        try {
            $ticket = BBNT_Partner_Onsite_Model::findOrFail($id);
            $ticket->status = '2';
            $ticket->save();

            return response()->json([
                'success' => true,
                'message' => 'Ticket changed to "In Progress" successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to change ticket status due to ' .$e->getMessage(),
            ], 500);
        }
    }
}
