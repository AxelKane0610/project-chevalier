<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Spectre_Crown_Warehouse_Model;
use App\Models\Loan_Unit_Part_Tickets_Model;
use App\Models\Loan_Unit_Ticket_Parts_Details_Model;

use App\Models\Attachments_Model;
use App\Models\Comments_Model;
use App\Services\tracking_info_service;

class SpectreCrownWarehouseController extends Controller
{
    
    public function Import_Asset(Request $request) {
        try {
            $validate_data = $request->validate([
                'asset_tag' => 'nullable',
                'model' => 'required',
                'serial_number' => 'required',
                'box_serial_number' => 'required',
                'product_number' => 'required',
                'category' => 'required',
                'asset_type' => 'required',
                'warehouse' => 'required',
                'available_status' => 'required',
                'condition' => 'required',
                'note' => 'nullable',
                'quantity' => 'required',
                'attachments.*' => 'file|max:20480|mimes:jpg,png,pdf,jpeg,xlsx'

            ]);

            $validate_data['asset_tag'] = strip_tags($validate_data['asset_tag']);
            $validate_data['model'] = strip_tags($validate_data['model']);
            $validate_data['serial_number'] = strip_tags($validate_data['serial_number']);
            $validate_data['box_serial_number'] = strip_tags($validate_data['box_serial_number']);
            $validate_data['product_number'] = strip_tags($validate_data['product_number']);
            $validate_data['category'] = strip_tags($validate_data['category']);
            $validate_data['asset_type'] = strip_tags($validate_data['asset_type']);
            $validate_data['warehouse'] = strip_tags($validate_data['warehouse']);
            $validate_data['available_status'] = strip_tags($validate_data['available_status']);
            $validate_data['condition'] = strip_tags($validate_data['condition']);
            $validate_data['note'] = strip_tags($validate_data['note']);
            $validate_data['quantity'] = strip_tags($validate_data['quantity']);

            if (empty($request->asset_tag)) {

                $prefixes = [
                    1 => 'BUF',
                    2 => 'CRT',
                    3 => 'DASS',
                    4 => 'DEMO',
                    5 => 'DOA',
                    6 => 'BU',
                ];

                $prefix = $prefixes[$request->asset_type];

                $lastItem = Spectre_Crown_Warehouse_Model::where('asset_tag', 'like', $prefix.'-%')
                    ->orderByRaw("
                        CAST(SUBSTRING_INDEX(asset_tag, '-', -1) AS UNSIGNED) DESC
                    ")
                    ->first();

                if ($lastItem) {
                    $lastNumber = (int) explode('-', $lastItem->asset_tag)[1];
                    $nextNumber = $lastNumber + 1;
                } else {
                    $nextNumber = 1;
                }

                $assetTag = $prefix . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
                $validate_data['asset_tag'] = $assetTag;

            } else {

                $assetTag = strtoupper(trim($request->asset_tag));
                $validate_data['asset_tag'] = $assetTag;

            }

            $new_asset = Spectre_Crown_Warehouse_Model::create($validate_data);
            return response()->json([
                'success' => true,
                'message' => 'Asset Import thành công với tag: ' .$validate_data['asset_tag'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Asset Import failed due to ' .$e->getMessage(),
            ], 500);
        }

    }

    public function index(Request $request)
    {
        $query = Spectre_Crown_Warehouse_Model::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('serial_number', 'like', "%{$search}%")
                ->orWhere('model', 'like', "%{$search}%")
                ->orWhere('asset_tag', 'like', "%{$search}%");
            });
        }

        // Category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Warehouse
        if ($request->filled('warehouse')) {
            $query->where('warehouse', $request->warehouse);
        }

        // Availability
        if ($request->filled('availability')) {
            $query->where('available_status', $request->availability);
        }

        // Condition
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        $items = $query->paginate(10);

        if ($request->ajax()) {
            return view('tables.spectre-crown-warehouse-items-table', compact('items'))->render();
        }

        return view('spectre-crown-warehouse-menu', compact('items'));
    }

    public function Item_Details($id){
        $item_details = Spectre_Crown_Warehouse_Model::with(['active_attachments','ticket_tracking_info','ticket_comments.attachments', 'ticket_comments.user', 'loan_unit_part_tickets'])->findOrFail($id);
        return view('spectre-crown-warehouse-item-details', compact('item_details'));
    }

    public function Add_Comment_Spectre_Crown_Warehouse(Request $request, $id){
        $comment_info_input = $request->validate([
            'comment' => 'required_without_all:attachments|string|nullable',
            'attachments' => 'required_without_all:comment|array|nullable',
            'attachments.*' => 'file|max:20480|mimes:jpg,jpeg,png,pdf,xlsx,docx',
        ]);

        $comment_info_input['comment'] = strip_tags($comment_info_input['comment']);
        $comment_info_input['ticket_id'] = $id;
        $comment_info_input['type_of_ticket'] = 11; //1 là mã cho software ticket
        $comment_info_input['user_id'] = auth()->id();
        
        
        $comment = Comments_Model::create($comment_info_input);
        

        if($request->hasFile('attachments'))
        {
            foreach($request->file('attachments') as $file)
            {
                $originalName = $file->getClientOriginalName();
                $folderPath = '11/'.$id;
                $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục 'attachments' đã được cấu hình trong config/filesystems.php, với đường dẫn là 'attachments/1/{ticket_id}/{original_file_name}'
                
                Attachments_Model::create([
                    'type_of_ticket' => 11,
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

    public function Edit_Asset_Details(Request $request, $id){
        $asset_details = Spectre_Crown_Warehouse_Model::findOrFail($id);
        try {
            
                $info_input = $request->validate([
                    'asset_tag' => 'required',
                    'model' => 'required',
                    'serial_number' => 'required',
                    'box_serial_number' => 'required',
                    'product_number' => 'required',
                    'category' => 'required',
                    'asset_type' => 'required',
                    'warehouse' => 'required',
                    'available_status' => 'required',
                    'condition' => 'required',
                    'quantity' => 'required',
                    'note' => 'nullable',
                    'attachments.*' => 'file|max:5120|mimes:jpg,png,pdf,jpeg,xlsx'
                ]);


                $info_input['asset_tag'] = trim(strip_tags($info_input['asset_tag']));
                $info_input['model'] = trim(strip_tags($info_input['model']));
                $info_input['box_serial_number'] = trim(strip_tags($info_input['box_serial_number']));
                $info_input['product_number'] = trim(strip_tags($info_input['product_number']));
                $info_input['category'] = trim(strip_tags($info_input['category']));
                $info_input['asset_type'] = trim(strip_tags($info_input['asset_type']));
                $info_input['warehouse'] = trim(strip_tags($info_input['warehouse']));
                $info_input['available_status'] = trim(strip_tags($info_input['available_status']));
                $info_input['condition'] = trim(strip_tags($info_input['condition']));
                $info_input['quantity'] = trim(strip_tags($info_input['quantity']));
                $info_input['note'] = trim(strip_tags($info_input['note']));


                if ($request->hasFile('attachments')) { //Kiểm tra xem có file nào được upload lên không

                    foreach ($request->file('attachments') as $file) { //Duyệt qua từng file được upload lên
                        $originalName = $file->getClientOriginalName();
                        $folderPath = '11/'.$asset_details->id;
                        $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục '/'
                        
                        Attachments_Model::create([
                            'type_of_ticket' => 11, // Giả sử 11 là mã cho asset details ticket
                            'ticket_id' => $asset_details->id,
                            'file_path' => $filePath,   
                            'name' => $originalName,// Lưu tên gốc của file vào cơ sở dữ liệu
                        ]);
                    }
                    
                }
                
                $asset_details->update($info_input);
                tracking_info_service::add(
                    $asset_details->id, 
                    auth()->id(), 
                    11,
                    'edited asset details at'
                );
                

                

                if ($request->has('delete_files')) {
                // Cập nhật tất cả các ID được tích chọn thành status = 0 trong 1 câu lệnh duy nhất
                    Attachments_Model::whereIn('id', $request->input('delete_files'))->update(['status' => '0']);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Asset details edited successfully',
                ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to edit asset details due to ' .$e->getMessage(),
            ], 500);
        }
        

    }

    public function Asset_Export (Request $request, $id) {
        $item_details = Spectre_Crown_Warehouse_Model::findOrFail($id);
        try {
            $ticket_info_input = $request->validate([
                'loan_unit_asset_tag' => 'required',
                'user_id' => 'nullable',
                'ticket_receipt' => 'required',
                'part_request' => 'required',
                'ct_loaned' => 'required',
                'status' => 'required',
                'original' => 'required',
                'start_date' => 'required',
                'note' => 'nullable'
                
            ]);

            $ticket_info_input['loan_unit_asset_tag'] = strip_tags($ticket_info_input['loan_unit_asset_tag']);
            $ticket_info_input['user_id'] = $ticket_info_input['user_id'] ?: null;
            $ticket_info_input['ticket_receipt'] = strip_tags($ticket_info_input['ticket_receipt']);
            $ticket_info_input['part_request'] = strip_tags($ticket_info_input['part_request']);
            $ticket_info_input['ct_loaned'] = strip_tags($ticket_info_input['ct_loaned']);
            $ticket_info_input['status'] = strip_tags($ticket_info_input['status']);
            $ticket_info_input['original'] = strip_tags($ticket_info_input['original']);
            $ticket_info_input['start_date'] = strip_tags($ticket_info_input['start_date']);
            $ticket_info_input['note'] = strip_tags($ticket_info_input['note']);

            $part_details = Loan_Unit_Ticket_Parts_Details_Model::create($ticket_info_input);

            tracking_info_service::add(
                $item_details->id, 
                auth()->id(), 
                '11',
                'exported asset at'
            );

            return response()->json([
                'success' => true,
                'message' => 'Asset exported successfully',
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export asset due to ' .$e->getMessage(),
            ], 500);
        }
    }

    public function Edit_Asset_Export (Request $request, $id ){
        try {
            $validate_data = $request->validate([
                'user_id' => 'nullable',
                'ticket_receipt' => 'nullable',
                'part_request' => 'nullable',
                'ct_loaned' => 'nullable',
                'new_ct_return' => 'nullable',
                'status' => 'nullable',
                'original' => 'nullable',
                'start_date' => 'nullable',
                'end_date' => 'nullable',
                'note' => 'nullable',
            ]);

            $validate_data['user_id'] = strip_tags($validate_data['user_id']);
            $validate_data['ticket_receipt'] = strip_tags($validate_data['ticket_receipt']);
            $validate_data['part_request'] = strip_tags($validate_data['part_request']);
            $validate_data['ct_loaned'] = strip_tags($validate_data['ct_loaned']);
            $validate_data['new_ct_return'] = strip_tags($validate_data['new_ct_return']);
            $validate_data['status'] = strip_tags($validate_data['status']);
            $validate_data['original'] = strip_tags($validate_data['original']);
            $validate_data['start_date'] = $validate_data['start_date'] ?: null;
            $validate_data['end_date'] = $validate_data['end_date'] ?: null;
            $validate_data['note'] = strip_tags($validate_data['note']);

            $ticket = Loan_Unit_Ticket_Parts_Details_Model::with('user_owner')->findOrFail($id);

            $ticket->update($validate_data);

            return response()->json([
                'success' => true,
                'message' => 'Edited successfully',
            ]);


        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fail to edit due to ' .$e->getMessage(),
            ], 500);
        }

    }


}
