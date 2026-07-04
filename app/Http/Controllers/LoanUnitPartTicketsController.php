<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Loan_Unit_Part_Tickets_Model;
use App\Models\Loan_Unit_Ticket_Parts_Details_Model;
use App\Models\Attachments_Model;
use App\Models\Comments_Model;
use App\Services\tracking_info_service;

class LoanUnitPartTicketsController extends Controller
{
    //
    public function Show_Pending_Tickets(){ 
        if (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_LOAN_UNIT_ADMIN')) {
            $tickets = Loan_Unit_Part_Tickets_Model::whereIn('status', ['1'])->get();
            $tickets_waiting_return = Loan_Unit_Part_Tickets_Model::where('status', '2')->get();
            return view('loan-unit-part-menu', compact('tickets', 'tickets_waiting_return'));
        } 
        else {
            $tickets = Loan_Unit_Part_Tickets_Model::where('user_id', auth()->id()) //lọc ra ticket của user đó
                ->whereIn('status', ['1', '2']) // lọc ra ticket đang pending
                ->get();
            
            return view('loan-unit-part-menu', compact('tickets'));
            
        }
        
    }

    public function Create_Loan_Unit_Part_Ticket(Request $request){
        try {
            $validate_data = $request->validate([
                'ticket_receipt' => 'required',
                'customer_unit_info' => 'required',
                'part_request' => 'required',
                'attachments.*' => 'file|max:20480|mimes:jpg,png,pdf,jpeg'

            ]);

            $validate_data['user_id'] = auth()->id();
            $validate_data['status'] = '1';
            $validate_data['ticket_receipt'] = strip_tags($validate_data['ticket_receipt']);
            $validate_data['customer_unit_info'] = strip_tags($validate_data['customer_unit_info']);
            $validate_data['part_request'] = strip_tags($validate_data['part_request']);

            if ($request->hasFile('attachments')) { //Kiểm tra xem có file nào được upload lên không

                foreach ($request->file('attachments') as $file) { //Duyệt qua từng file được upload lên
                    $originalName = $file->getClientOriginalName();
                    $folderPath = '4/'.$ticket->id;
                    $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục '/'
                    
                    Attachments_Model::create([
                        'type_of_ticket' => '4',
                        'ticket_id' => $ticket->id,
                        'file_path' => $filePath,   
                        'name' => $originalName,// Lưu tên gốc của file vào cơ sở dữ liệu
                    ]);
                }
                
            }
            
            $ticket = Loan_Unit_Part_Tickets_Model::create($validate_data);
            $validate_data['ticket_id'] = $ticket->id;
            $part_details = Loan_Unit_Ticket_Parts_Details_Model::create($validate_data);
            tracking_info_service::add(
                $ticket->id, 
                auth()->id(), 
                '4',
                'created ticket at'
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Ticket created successfully',
                'ticket_id' => $ticket->id,
                'ticket_receipt' => $ticket->ticket_receipt,
                'status' => $ticket->status,
                'customer_unit_info' => $ticket->customer_unit_info,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ticket due to ' .$e->getMessage(),
            ], 500);
        }
    }

    public function Show_Loan_Unit_Part_Ticket_Details($id){
        $ticket = Loan_Unit_Part_Tickets_Model::with(['user_owner', 'active_attachments','ticket_tracking_info','ticket_comments.attachments', 'ticket_comments.user', 'parts_details'])->findOrFail($id);
        return view('loan-unit-part-ticket-details', compact('ticket'));
    }

    public function Add_Comment_Loan_Unit_Part_Ticket(Request $request, $id){
        $comment_info_input = $request->validate([
            'comment' => 'required_without_all:attachments|string|nullable',
            'attachments' => 'required_without_all:comment|array|nullable',
            'attachments.*' => 'file|max:20480|mimes:jpg,jpeg,png,pdf,xlsx,docx',
        ]);

        $comment_info_input['comment'] = strip_tags($comment_info_input['comment']);
        $comment_info_input['ticket_id'] = $id;
        $comment_info_input['type_of_ticket'] = 4;
        $comment_info_input['user_id'] = auth()->id();
        
        
        $comment = Comments_Model::create($comment_info_input);
        

        if($request->hasFile('attachments'))
        {
            foreach($request->file('attachments') as $file)
            {
                $originalName = $file->getClientOriginalName();
                $folderPath = '4/'.$id;
                $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục 'attachments' đã được cấu hình trong config/filesystems.php, với đường dẫn là 'attachments/1/{ticket_id}/{original_file_name}'
                
                Attachments_Model::create([
                    'type_of_ticket' => 4,
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

    public function Edit_Loan_Unit_Part_Ticket(Request $request, $id){
        $ticket = Loan_Unit_Part_Tickets_Model::with('user_owner')->findOrFail($id);
        
        try {
            if ($ticket->status == '1') {
                $validate_data = $request->validate([
                    'ticket_receipt' => 'required',
                    'customer_unit_info' => 'required',
                    'attachments.*' => 'file|max:20480|mimes:jpg,png,pdf,jpeg,xlsx'
            ]);

                $validate_data['ticket_receipt'] = strip_tags($validate_data['ticket_receipt']);
                $validate_data['customer_unit_info'] = strip_tags($validate_data['customer_unit_info']);
                
                $ticket->ticket_receipt = $validate_data['ticket_receipt'];
                $ticket->customer_unit_info = $validate_data['customer_unit_info'];


                tracking_info_service::add(
                    $ticket->id, 
                    auth()->id(), 
                    4,
                    'edited ticket at'
                );

                Loan_Unit_Ticket_Parts_Details_Model::where('ticket_id', $id)
                ->update([
                    'ticket_receipt' => $validate_data['ticket_receipt']
                ]);
                $ticket->save();

                if ($request->hasFile('attachments')) { //Kiểm tra xem có file nào được upload lên không

                    foreach ($request->file('attachments') as $file) { //Duyệt qua từng file được upload lên
                        $originalName = $file->getClientOriginalName();
                        $folderPath = '4/'.$id;
                        $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục 'attachments' đã được cấu hình trong config/filesystems.php, với đường dẫn là 'attachments/1/{ticket_id}/{original_file_name}'
                        
                        Attachments_Model::create([
                            'type_of_ticket' => 4, // Giả sử 1 là mã cho software ticket
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
                    'message' => 'Ticket edited successfully',
                ]);
            } else return response()->json([
                'success' => false,
                'message' => 'Chỉ có ticket đang ở trạng thái "Open" mới được phép edit !',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to edit ticket due to ' .$e->getMessage(),
            ], 500);
        }
    }

    public function Edit_Loan_Unit_Part_Details (Request $request, $id){
        try {
            $part = Loan_Unit_Ticket_Parts_Details_Model::findOrFail($id);
            $ticket = Loan_Unit_Part_Tickets_Model::with('user_owner')->findOrFail($part->ticket_id);

            if ($part->status != '1') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có part đang ở trạng thái "Requested" mới được phép edit part details !',
                ], 400);
            } else {
                $validate_data = $request->validate([
                    'part_request' => 'nullable',
                    'loan_unit_asset_tag' => 'nullable',
                    'loan_unit_serial_number' => 'nullable',
                    'ct_loaned' => 'nullable',
                    'new_ct_return' => 'nullable',
                    'original' => 'nullable',
                    'start_date' => 'nullable',
                    'end_date' => 'nullable',
                ]);

                $part->part_request = strip_tags($validate_data['part_request']);
                $part->loan_unit_asset_tag = strip_tags($validate_data['loan_unit_asset_tag']);
                $part->loan_unit_serial_number = strip_tags($validate_data['loan_unit_serial_number']);
                $part->ct_loaned = strip_tags($validate_data['ct_loaned']);
                $part->new_ct_return = strip_tags($validate_data['new_ct_return']);
                $part->original = strip_tags($validate_data['original']);
                $part->start_date = strip_tags($validate_data['start_date']);
                $part->end_date = strip_tags($validate_data['end_date']);

                $part->save();

                tracking_info_service::add(
                    $ticket->id,
                    auth()->id(),
                    4,
                    'edited part details at'
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Part details edited successfully',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to edit part detail due to ' .$e->getMessage(),
            ], 500);
        }


    }

    public function Issue_Loan_Unit_Part (Request $request, $id ){
        try {
            $part = Loan_Unit_Ticket_Parts_Details_Model::findOrFail($id);
            $ticket = Loan_Unit_Part_Tickets_Model::with('user_owner')->findOrFail($part->ticket_id);

            if ($part->status != '1') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có part đang ở trạng thái "Requested" mới được phép issue !',
                ], 400);
            } else {
                $validate_data = $request->validate([
                    'loan_unit_asset_tag' => 'required_without_all:loan_unit_serial_number|string|nullable',
                    'loan_unit_serial_number' => 'required_without_all:loan_unit_asset_tag|string|nullable',
                    'ct_loaned' => 'nullable',
                    'original' => 'nullable',
                    'start_date' => 'nullable',
                ]);

                $part->status = '2'; //Cập nhật trạng thái part từ "Requested" sang "Borrowed, not return yet"
                $part->loan_unit_asset_tag = strip_tags($validate_data['loan_unit_asset_tag']);
                $part->loan_unit_serial_number = strip_tags($validate_data['loan_unit_serial_number']);
                $part->ct_loaned = strip_tags($validate_data['ct_loaned']);
                $part->original = strip_tags($validate_data['original']);
                $part->start_date = strip_tags($validate_data['start_date']);

                $part->save();

                tracking_info_service::add(
                    $ticket->id,
                    auth()->id(),
                    4,
                    'issued part at'
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Part issued successfully',
                ]);


            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to issue part due to ' .$e->getMessage(),
            ], 500);
        }

    }


}
