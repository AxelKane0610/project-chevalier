<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BBNT_Partner_Onsite_Model;
use Carbon\Carbon;
use App\Models\Attachments_Model;
use App\Models\Comments_Model;
use App\Services\tracking_info_service;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;



class BBNTPartnerOnsiteController extends Controller
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
                'email_address' => 'required',
                'notes' => 'nullable',
                'attachments.*' => 'file|max:20480|mimes:jpg,png,pdf,jpeg,xlsx'
            ]);

            $new_ticket['user_id'] = auth()->id();
            $new_ticket['status'] = '1';
            $new_ticket['partner_address'] = strip_tags($new_ticket['partner_address']);
            $new_ticket['partner_city'] = strip_tags($new_ticket['partner_city']);
            $new_ticket['onsite_type'] = strip_tags($new_ticket['onsite_type']);
            $new_ticket['total_case'] = strip_tags($new_ticket['total_case']);
            $new_ticket['email_address'] = strip_tags($new_ticket['email_address']);
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

    public function Filter_Pending_BBNT_Tickets(Request $request) {
        if (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_BBNT_PARTNER_ONSITE_ADMIN')) {
            $query = BBNT_Partner_Onsite_Model::whereIn('status', ['1', '2']);
        } else {
            $query = BBNT_Partner_Onsite_Model::where('user_id', auth()->id())->whereIn('status', ['1', '2']);
        }
        

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('document_name', 'like', "%{$search}%")
                    ->orWhereHas('user_owner', function ($user) use ($search) {
                        $user->where('fullname', 'like', "%{$search}%");
                    });
                    
            });
        }

        if ($request->filled('onsite_type')) {
            $query->where('onsite_type', $request->onsite_type);
        }

        

        // Phân trang kết quả
        $pending_tickets = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('tables.pending-bbnt-tickets-table', compact('pending_tickets'))->render();
        }

    }

    public function Filter_All_BBNT_Tickets(Request $request) {
        if (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_BBNT_PARTNER_ONSITE_ADMIN')) {
            $query = BBNT_Partner_Onsite_Model::query();
        } else {
            $query = BBNT_Partner_Onsite_Model::where('user_id', auth()->id())->orderBy('created_at', 'desc');
        }
        

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('document_name', 'like', "%{$search}%")
                    ->orWhereHas('user_owner', function ($user) use ($search) {
                        $user->where('fullname', 'like', "%{$search}%");
                    });
                    
            });
        }

        if ($request->filled('onsite_type')) {
            $query->where('onsite_type', $request->onsite_type);
        }

        

        // Phân trang kết quả
        $all_tickets = $query->orderBy('created_at', 'desc')
                        ->paginate(10)
                        ->withQueryString();

        if ($request->ajax()) {
            return view('tables.all-bbnt-tickets-table', compact('all_tickets'))->render();
        }

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

    public function Edit_BBNT_Ticket_Details(Request $request, $id) {
        try {
            $ticket = BBNT_Partner_Onsite_Model::findOrFail($id);

            if ($ticket->status != '1' && auth()->user()->hasRole('ROLE_BBNT_PARTNER_ONSITE_USER')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể edit ticket do ticket đã chuyển mục',
                ], 403);
            } else {
                $validatedData = $request->validate([
                    'email_address' => 'required',
                    'document_name' => 'required',
                    'partner_address' => 'required',
                    'partner_city' => 'required',
                    'onsite_type' => 'required',
                    'total_case' => 'required',
                    'total_amount' => 'required',
                    'notes' => 'nullable',
                    'attachments.*' => 'file|max:20480|mimes:jpg,png,pdf,jpeg,xlsx'
                ]);

                $ticket->update([
                    'email_address' => strip_tags($validatedData['email_address']),
                    'document_name' => strip_tags($validatedData['document_name']),
                    'partner_address' => strip_tags($validatedData['partner_address']),
                    'partner_city' => strip_tags($validatedData['partner_city']),
                    'onsite_type' => strip_tags($validatedData['onsite_type']),
                    'total_case' => strip_tags($validatedData['total_case']),
                    'total_amount' => strip_tags(str_replace('.', '', $validatedData['total_amount'])),
                    'notes' => strip_tags($validatedData['notes']),
                ]);

                tracking_info_service::add(
                    $ticket->id, 
                    auth()->id(), 
                    13,
                    'edited ticket at'
                );

                if ($request->hasFile('attachments')) { //Kiểm tra xem có file nào được upload lên không

                    foreach ($request->file('attachments') as $file) { //Duyệt qua từng file được upload lên
                        $originalName = $file->getClientOriginalName();
                        $folderPath = '13/'.$id;
                        $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục 'attachments' đã được cấu hình trong config/filesystems.php, với đường dẫn là 'attachments/1/{ticket_id}/{original_file_name}'
                        
                        Attachments_Model::create([
                            'type_of_ticket' => 13, 
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

                return response()->json([
                    'success' => true,
                    'message' => 'Ticket chỉnh sửa thành công ',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update ticket details due to: '.$e->getMessage(),
            ], 500);
        }
    }

    public function Close_BBNT_Ticket(Request $request, $id) {
        
        try {
            
            $ticket = BBNT_Partner_Onsite_Model::with('user_owner', 'active_attachments')->findOrFail($id);
            $ticket->status = '3';
            $ticket->save();
            
            tracking_info_service::add(
                $ticket->id, 
                auth()->id(), 
                13,
                'completed ticket at'
            );

            $attachments = $ticket->active_attachments->map(function ($file) {
                return [
                    'fileName' => basename($file->file_path),
                    'fileContent' => base64_encode(
                        Storage::disk('attachments')->get(
                            $file->file_path
                        )
                    ),
                ];
            });


            
            
            $send_email = Http::post(config('services.api_service.send_bbnt_ticket_complete_notification_url'), [
                'partner_name' => $ticket->user_owner->fullname ?? 'N/A',
                'email_address' => $ticket->email_address ?? 'N/A',
                'document_name' => $ticket->document_name ?? 'N/A',
                'total_case' => $ticket->total_case ?? 'N/A',
                'total_amount' => number_format($ticket->total_amount ?? 0, 0, ',', '.'),
                'comment' => $request->input('comment') ?? 'N/A',
                'attachments' => $attachments,
            ]);
            

            

            if ($send_email->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send email notification due to: '.$send_email->body(),
                ], 500);
            }

            

            return response()->json([
                'success' => true,
                'message' => 'Ticket closed successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to close ticket due to: '.$e->getMessage(),
            ], 500);
        }
    }
}
