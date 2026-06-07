<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice_Exceptional_Tickets_Model;
use App\Models\Attachments_Model;
use App\Models\Comments_Model;
use App\Services\tracking_info_service;

class InvoiceExceptionalTicketsController extends Controller
{
    //
    public function Show_Pending_Tickets(){ 
        if (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_INVOICE_EXCEPTIONAL_L1_APPROVER')) {
            $tickets = Invoice_Exceptional_Tickets_Model::whereIn('status', ['1', '2', '3'])->get();
            $tickets_waiting_approval = Invoice_Exceptional_Tickets_Model::where('status', 3)->get();
            return view('invoice-exceptional-menu', compact('tickets', 'tickets_waiting_approval'));
        } 
        else {
            $tickets = Invoice_Exceptional_Tickets_Model::where('user_id', auth()->id()) //lọc ra ticket của user đó
                ->whereIn('status', ['1', '2', '3']) // lọc ra ticket đang pending
                ->get();

            

            $tickets_waiting_approval = Invoice_Exceptional_Tickets_Model::whereIn('status', ['1', '2', '3'])
                ->whereHas('user_owner', function ($query) { //Lọc ra những ticket có user_owner có leader_id là id của user đang đăng nhập, tức là lọc ra những ticket của những user mà user đang đăng nhập là leader của họ, rồi mới lấy ra những ticket đó để trả về view
                    $query->where('leader_id', auth()->id());
                })
                ->get();
            
            return view('invoice-exceptional-menu', compact('tickets', 'tickets_waiting_approval'));

            
        }
        
    }

    public function Create_Invoice_Exceptional_Tickets(Request $request){
        $validate_data = $request->validate([
            'ticket_receipt' => 'required',
            'invoice_number' => 'required',
            'serial_number' => 'required',
            'product_number' => 'required',
            'product_model' => 'required',
            'invoice_date' => 'required',
            'expired_date' => 'required',
            'retail_name' => 'required',
            'company_customer_name' => 'required',
            'support_type' => 'required',
            'description' => 'required',
            'attachments.*' => 'file|max:20480|mimes:jpg,png,pdf,jpeg,xlsx,docx'
        ]);

        $validate_data['user_id'] = auth()->id();
        $validate_data['status'] = 2;
        $validate_data['ticket_receipt'] = strip_tags($validate_data['ticket_receipt']);
        $validate_data['invoice_number'] = strip_tags($validate_data['invoice_number']);
        $validate_data['serial_number'] = strip_tags($validate_data['serial_number']);
        $validate_data['product_number'] = strip_tags($validate_data['product_number']);
        $validate_data['product_model'] = strip_tags($validate_data['product_model']);
        $validate_data['invoice_date'] = strip_tags($validate_data['invoice_date']);
        $validate_data['expired_date'] = strip_tags($validate_data['expired_date']);
        $validate_data['retail_name'] = strip_tags($validate_data['retail_name']);
        $validate_data['company_customer_name'] = strip_tags($validate_data['company_customer_name']);
        $validate_data['support_type'] = strip_tags($validate_data['support_type']);
        $validate_data['description'] = strip_tags($validate_data['description']);

        $$ticket = Invoice_Exceptional_Tickets_Model::create($validate_data);
        if ($request->hasFile('attachments')) { //Kiểm tra xem có file nào được upload lên không

            foreach ($request->file('attachments') as $file) { //Duyệt qua từng file được upload lên
                $originalName = $file->getClientOriginalName();
                $folderPath = '7/'.$ticket->id;
                $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục '/'
                
                Attachments_Model::create([
                    'type_of_ticket' => 7,
                    'ticket_id' => $ticket->id,
                    'file_path' => $filePath,   
                    'name' => $originalName,// Lưu tên gốc của file vào cơ sở dữ liệu
                ]);
            }
            
        }

        tracking_info_service::add(
            $ticket->id, 
            auth()->id(), 
            7,
            'created ticket at'
        );

        try {
            $ticket->save();
            return response()->json([
                'success' => true,
                'message' => 'Ticket created successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ticket',
                'error' => $e->getMessage(), // Có thể bỏ ở môi trường production
            ], 500);
        }

    }
}
