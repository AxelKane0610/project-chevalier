<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Scrap_Request_Model;
use App\Models\Attachments_Model;
use App\Models\Comments_Model;
use App\Models\User;
use Carbon\Carbon;
use App\Services\tracking_info_service;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ScrapRequestController extends Controller
{
    //
    public function index(){ 
        if (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_SCRAP_LV1_APPROVER') || auth()->user()->hasRole('ROLE_SCRAP_LV2_APPROVER')) {
            $pending_tickets = Scrap_Request_Model::whereIn('status', ['1', '2', '3'])->paginate(10);
            $all_tickets = Scrap_Request_Model::query()->orderByDesc('created_at')->paginate(10);
            return view('scrap-request-menu', compact('pending_tickets', 'all_tickets'));
        } 
        else {
            $pending_tickets = Scrap_Request_Model::where('user_id', auth()->id()) //lọc ra ticket của user đó
                ->whereIn('status', ['1', '2', '3']) // lọc ra ticket đang pending
                ->paginate(10);

            
            $all_tickets = Scrap_Request_Model::where('user_id', auth()->id())->orderByDesc('created_at')->paginate(10);
            return view('scrap-request-menu', compact('pending_tickets', 'all_tickets'));

            
        }
        
    }

    public function Create_Scrap_Ticket(Request $request) {
        try {
            $new_ticket = $request->validate([
                'scrap_type' => 'required',
                'scrap_date' => 'required',
                'scrap_description' => 'required',
                'attachments.*' => 'file|max:20480|mimes:jpg,png,pdf,jpeg,xlsx'

            ]);

            $new_ticket['user_id'] = auth()->id();
            $new_ticket['status'] = '2';
            $new_ticket['scrap_type'] = strip_tags($new_ticket['scrap_type']);
            $new_ticket['scrap_date'] = strip_tags($new_ticket['scrap_date']);
            $new_ticket['scrap_description'] = strip_tags($new_ticket['scrap_description']);

            $new_ticket = Scrap_Request_Model::create($new_ticket);
            if ($request->hasFile('attachments')) { 

                foreach ($request->file('attachments') as $file) { 
                    $originalName = $file->getClientOriginalName();
                    $folderPath = '14/'.$new_ticket->id;
                    $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); 
                    
                    Attachments_Model::create([
                        'type_of_ticket' => 14, 
                        'ticket_id' => $new_ticket->id,
                        'file_path' => $filePath,   
                        'name' => $originalName,
                    ]);
                }
                
            }


            tracking_info_service::add(
                $new_ticket->id, 
                auth()->id(), 
                14,
                'created ticket at'
            );

            $new_ticket = Scrap_Request_Model::with('user_owner', 'active_attachments')->findOrFail($new_ticket->id);
            $attachments = $new_ticket->active_attachments->map(function ($file) {
                return [
                    'fileName' => basename($file->file_path),
                    'fileContent' => base64_encode(
                        Storage::disk('attachments')->get(
                            $file->file_path
                        )
                    ),
                ];
            });
            $date = Carbon::parse($new_ticket->scrap_date);

            try {
                $send_approve = Http::post(config('services.api_service.send_approve_scrap_ticket_url'), [
                    'user_name' => $new_ticket->user_owner->fullname ?? 'N/A',
                    'email_address' => $new_ticket->user_owner->email ?? 'N/A',
                    'leader_email_address' => User::where('id', $new_ticket->user_owner->leader_id)->value('email'),
                    'scrap_date' => 'tháng ' . $date->format('n') . ' năm ' . $date->format('Y'),
                    'scrap_type' => $new_ticket->scrap_type,
                    'scrap_description' => $new_ticket->scrap_description,
                    'attachments' => $attachments,
                ]);
                if ($send_approve->successful()) {
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Tạo ticket thành công',
                    ]);
                } else {
                    // Xử lý lỗi nếu phản hồi không thành công
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to send approval request. API responded with status: ' . $send_approve->body(),
                    ], 500);
                } 
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tạo ticket lỗi do ' .$e->getMessage(),
                ], 500);
            }
            

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tạo ticket lỗi do ' .$e->getMessage(),
            ], 500);
        }
    }

    public function Ticket_Details($id) {
        $ticket = Scrap_Request_Model::with(['user_owner', 'active_attachments','ticket_tracking_info','ticket_comments.attachments', 'ticket_comments.user'])->findOrFail($id);
        return view('scrap-ticket-details', compact('ticket'));
    }
}
