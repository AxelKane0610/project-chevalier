<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HPS_Warehouse_Model;
use App\Models\HPS_Warehouse_Export_Details_Model;
use Illuminate\Support\Facades\DB;
use App\Models\Comments_Model;
use App\Models\Attachments_Model;
use App\Services\tracking_info_service;
use Illuminate\Support\Facades\Http;

use function PHPUnit\Framework\matches;

class HPSWarehouseController extends Controller
{
    //
    public function index(Request $request)
    {
        if (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_HPS_WAREHOUSE_ADMIN')) {
            $query = HPS_Warehouse_Model::query();
        } else {
            $query = HPS_Warehouse_Model::where('current_site', auth()->user()->site_id);
        }

        $items = $query->orderBy('created_at', 'desc')->paginate(10);
        $pending_items = $query->where('current_status', '3')->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('hps-warehouse-menu', compact('items', 'pending_items'));
    }

    public function Filter_All_HPS_Items(Request $request)
    {
        if (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_HPS_WAREHOUSE_ADMIN')) {
            $query = HPS_Warehouse_Model::query();
        } else {
            $query = HPS_Warehouse_Model::where('current_site', auth()->user()->site_id);
        }
        

        // Search
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('current_serial_number', 'like', "%{$search}%")
                ->orWhere('current_box_serial_number', 'like', "%{$search}%")
                ->orWhere('current_product_number', 'like', "%{$search}%")
                ->orWhere('serial_number', 'like', "%{$search}%")
                ->orWhere('box_serial_number', 'like', "%{$search}%")
                ->orWhere('po_number', 'like', "%{$search}%")
                ->orWhere('invoice', 'like', "%{$search}%")
                ->orWhere('model', 'like', "%{$search}%")
                ->orWhere('asset_tag', 'like', "%{$search}%")
                ;
            });
        }


        if ($request->filled('current_status')) {
            $query->where('current_status', $request->current_status);
        }


        if ($request->filled('current_site')) {
            $query->where('current_site', $request->current_site);
        }

        
        if ($request->filled('unit_re_import_status')) {
            $query->where('unit_re_import_status', $request->unit_re_import_status);
        }


        $items = $query->orderBy('created_at', 'desc')->paginate(10);

        if ($request->ajax()) {
            return view('tables.hps-warehouse-items-table', compact('items'))->render();
        }

    }


    public function Filter_Pending_HPS_Items(Request $request)
    {
        if (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_HPS_WAREHOUSE_ADMIN')) {
            $query = HPS_Warehouse_Model::where('current_status', "3");
        } else {
            $query = HPS_Warehouse_Model::where([
                ['current_site', auth()->user()->site_id],
                ['current_status', "3"],

            ]);
        }
        

        // Search
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('current_serial_number', 'like', "%{$search}%")
                ->orWhere('current_hps_receipt', 'like', "%{$search}%")
                ->orWhere('current_box_serial_number', 'like', "%{$search}%")
                ->orWhere('current_product_number', 'like', "%{$search}%")
                ->orWhere('serial_number', 'like', "%{$search}%")
                ->orWhere('box_serial_number', 'like', "%{$search}%")
                ->orWhere('po_number', 'like', "%{$search}%")
                ->orWhere('invoice', 'like', "%{$search}%")
                ->orWhere('model', 'like', "%{$search}%")
                ->orWhere('asset_tag', 'like', "%{$search}%")
                ;
            });
        }


        if ($request->filled('current_status')) {
            $query->where('current_status', $request->current_status);
        }


        if ($request->filled('current_site')) {
            $query->where('current_site', $request->current_site);
        }

        
        if ($request->filled('unit_re_import_status')) {
            $query->where('unit_re_import_status', $request->unit_re_import_status);
        }


        $pending_items = $query->orderBy('created_at', 'desc')->paginate(10);

        if ($request->ajax()) {
            return view('tables.hps-warehouse-pending-items-table', compact('pending_items'))->render();
        }

    }


    public function Item_Details($id){
        $item_details = HPS_Warehouse_Model::with(['active_attachments','ticket_tracking_info','ticket_comments.attachments', 'ticket_comments.user', 'export_details', 'user_owner', 'export_details.ceOwner', 'export_details.user_owner_export', 'export_details.user_owner_re_import'])->findOrFail($id);
        
        return view('hps-warehouse-item-details', compact('item_details'));
    }

    public function Get_Model_Name(Request $request){
        // Lấy tham số product_number từ request
        $productNumber = trim($request->query('product_number'));

        if (empty($productNumber)) {
            return response()->json([
                'success' => false,
                'message' => 'Product Number không được để trống'
            ], 400);
        }


        $product = DB::table('hps_product_lookup') 
            ->where('product_number', $productNumber)
            ->select('model') // Chọn cột chứa tên model
            ->first();

        if ($product) {
            return response()->json([
                'success' => true,
                'model'   => $product->model 
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => ''
            ], 404);
        }
    }

    public function Import_Asset(Request $request) {
        try {
        $validated = $request->validate([
            'serial_number'   => 'required|array|min:1',
            'serial_number.*' => 'required|string',
            'product_number'  => 'required|array|min:1',
            'model'           => 'required|array|min:1',
            'po_number'       => 'nullable|array',
            'price'           => 'nullable|array',
            'note'            => 'nullable|array',
            'import_source'   => 'required',
            'po_type'         => 'required',
            'import_status'   => 'required',
            'unit_re_import_status' => 'required',
            'site'            => 'required',
            'location'        => 'nullable',
            'invoice'         => 'nullable',
            'attachments.*'   => 'file|max:20480|mimes:jpg,png,pdf,jpeg,xlsx'
        ]);

        $sanitize = function ($value) use (&$sanitize) {
            if (is_array($value)) {
                return array_map($sanitize, $value);
            }
            return is_string($value) ? strip_tags(trim($value)) : $value;
        };

        // Áp dụng làm sạch cho toàn bộ $validated
        $validated = array_map($sanitize, $validated);


        // Thêm `return` trước DB::transaction để trả kết quả response ra Controller
        return DB::transaction(function () use ($request) {
            
            // Tách phần số sau 'FB-' (từ ký tự thứ 4) và ép kiểu về UNSIGNED để tìm số lớn nhất chuẩn xác
            $latestAsset = HPS_Warehouse_Model::where('asset_tag', 'LIKE', 'FB-%')
                ->orderByRaw("CAST(SUBSTRING(asset_tag, 4) AS UNSIGNED) DESC")
                ->lockForUpdate()
                ->first();

            // Tách lấy số thứ tự hiện tại
            $currentNumber = 1000; // Giá trị mặc định nếu DB chưa có bản ghi FB- nào
            if ($latestAsset && $latestAsset->asset_tag) {
                $numberPart = preg_replace('/[^0-9]/', '', $latestAsset->asset_tag);
                if (!empty($numberPart)) {
                    $currentNumber = (int) $numberPart;
                }
            }

            $serialNumbers = $request->input('serial_number');

            foreach ($serialNumbers as $index => $serial) {
                $currentNumber++; // Tăng dần số thứ tự (ví dụ: 1001, 1002...)
                $newAssetTag = 'FB-' . $currentNumber;

                $new_asset = HPS_Warehouse_Model::create([
                    'asset_tag'                 => $newAssetTag,
                    'user_id'                   => auth()->id(),
                    'serial_number'             => $request->serial_number[$index],
                    'box_serial_number'         => $request->serial_number[$index],
                    'product_number'            => $request->product_number[$index],
                    'model'                     => $request->model[$index],
                    'po_number'                 => $request->po_number[$index] ?? null,
                    'price'                     => $request->price[$index] ?? null,
                    'note'                      => $request->note[$index] ?? null,
                    'import_source'             => $request->import_source,
                    'po_type'                   => $request->po_type,
                    'import_status'             => $request->import_status,
                    'unit_re_import_status'     => $request->unit_re_import_status,
                    'site'                      => $request->site,
                    'location'                  => $request->location,
                    'invoice'                   => $request->invoice,
                    'import_date'               => now(),

                    'curent_status'             => '1',
                    'current_hps_receipt'       => null,
                    'current_serial_number'     => $serial,
                    'current_box_serial_number' => $serial ?? null,
                    'current_product_number'    => $request->product_number[$index] ?? null,
                    'current_site'              => $request->site,
                    'current_location'          => $request->location,
                    'current_export_ticket_id'  => null,
                ]);

                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        $originalName = $file->getClientOriginalName();
                        $folderPath = '12/' . $new_asset->id;
                        $filePath = $file->storeAs($folderPath, $originalName, 'attachments');

                        Attachments_Model::create([
                            'type_of_ticket' => 12,
                            'ticket_id'      => $new_asset->id,
                            'comment_id'     => null,
                            'file_path'      => $filePath,
                            'name'           => $originalName,
                            'status'         => 1
                        ]);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Nhập kho thành công ' . count($serialNumbers) . ' máy.',
            ]);
        });

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Nhập kho thất bại do ' . $e->getMessage(),
            ], 500);
        }
    }

    public function Add_Comment_HPS_Warehouse(Request $request, $id){
        $comment_info_input = $request->validate([
            'comment' => 'required_without_all:attachments|string|nullable',
            'attachments' => 'required_without_all:comment|array|nullable',
            'attachments.*' => 'file|max:20480|mimes:jpg,jpeg,png,pdf,xlsx',
        ]);

        $comment_info_input['comment'] = strip_tags($comment_info_input['comment']);
        $comment_info_input['ticket_id'] = $id;
        $comment_info_input['type_of_ticket'] = 12; //1 là mã cho software ticket
        $comment_info_input['user_id'] = auth()->id();
        
        
        $comment = Comments_Model::create($comment_info_input);
        

        if($request->hasFile('attachments'))
        {
            foreach($request->file('attachments') as $file)
            {
                $originalName = $file->getClientOriginalName();
                $folderPath = '12/'.$id;
                $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục 'attachments' đã được cấu hình trong config/filesystems.php, với đường dẫn là 'attachments/1/{ticket_id}/{original_file_name}'
                
                Attachments_Model::create([
                    'type_of_ticket' => 12,
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


    public function Edit_HPS_Asset_Details(Request $request, $id){
        try{
            $validatedData = $request->validate([
                'serial_number' => 'required|string',
                'box_serial_number' => 'nullable|string',
                'product_number' => 'required|string',
                'model' => 'required|string',
                'import_date' => 'required|date',
                'import_source' => 'required',
                'po_number' => 'nullable|string',
                'po_type' => 'required',
                'invoice' => 'nullable|string',
                'price' => 'nullable|numeric',
                'import_status' => 'required|in:1,2,3',
                'site' => 'required|string',
                'location' => 'nullable|string',
                'current_status' => 'required|in:1,2,3,4,5',
                'current_hps_receipt' => 'nullable|string', 
                'current_serial_number' => 'nullable|string',
                'current_box_serial_number' => 'nullable|string',
                'current_product_number' => 'nullable|string',
                'current_site' => 'nullable',
                'current_location' => 'nullable|string',
                'unit_re_import_status' => 'required',
                'note' => 'nullable|string',
                'attachments.*'   => 'file|max:20480|mimes:jpg,png,pdf,jpeg,xlsx'
            ]);

            $asset = HPS_Warehouse_Model::findOrFail($id);
            $sanitize = function ($value) use (&$sanitize) {
                if (is_array($value)) {
                    return array_map($sanitize, $value);
                }
                return is_string($value) ? strip_tags(trim($value)) : $value;
            };

            // Áp dụng làm sạch cho toàn bộ $validated
            $validatedData = array_map($sanitize, $validatedData);
            $asset->update($validatedData);
            if ($request->hasFile('attachments')) { //Kiểm tra xem có file nào được upload lên không

                foreach ($request->file('attachments') as $file) { //Duyệt qua từng file được upload lên
                    $originalName = $file->getClientOriginalName();
                    $folderPath = '12/'.$asset->id;
                    $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục '/'
                    
                    Attachments_Model::create([ 
                        'type_of_ticket' => 12, // Giả sử 12 là mã cho asset details ticket
                        'ticket_id' => $asset->id,
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
                'message' => 'Asset details edited successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to edit asset details due to ' .$e->getMessage(),
            ], 500);
        }
    }

    // public function Export_HPS_Asset(Request $request, $id){
    //     try{
    //         $validatedData = $request->validate([
    //             'hps_receipt' => 'required|string',
    //             'ce_owner' => 'nullable|exists:users,id',
    //             'program_support' => 'required',
    //             'export_date' => 'required|date',
    //             'export_site' => 'required|string',
    //             'export_location' => 'nullable|string',
    //             'export_product_number' => 'nullable|string',
    //             'export_model' => 'nullable|string',
    //             'export_serial_number' => 'nullable|string',
    //             'export_box_serial_number' => 'nullable|string',
    //             'note' => 'nullable|string',
    //         ]);

    //         $asset = HPS_Warehouse_Model::findOrFail($id);

    //         // dd(
    //         //     $validatedData, auth()->user()->fullname, auth()->user()->email
    //         // );
            
    //         if (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_HPS_WAREHOUSE_ADMIN')) {
    //             $validatedData['asset_tag'] = $asset->asset_tag;
    //             $validatedData['user_export'] = auth()->id(); // Gán user hiện tại là người export
    //             $validatedData['current_status'] = "2";

    //             $new_export_detail = HPS_Warehouse_Export_Details_Model::create($validatedData);
    //             $asset->update([
    //                 'current_status' => "3", // Cập nhật trạng thái hiện tại của asset
    //                 'current_hps_receipt' => $validatedData['hps_receipt'],
    //                 'current_export_ticket_id' => $new_export_detail->id, // Lưu ID của bản ghi export mới tạo vào asset

    //             ]);
                
    //             tracking_info_service::add(
    //                 $asset->id, 
    //                 auth()->id(), 
    //                 12,
    //                 'export ' & $asset->current_serial_number & ' for receipt ' & $validatedData['hps_receipt'] & ' at'
    //             );
                

    //             return response()->json([
    //                 'success' => true,
    //                 'message' => 'Asset exported successfully',
    //             ]);
    //         } else {
    //             $validatedData['asset_tag'] = $asset->asset_tag;
    //             $validatedData['user_export'] = auth()->id(); // Gán user hiện tại là người export
    //             $validatedData['current_status'] = "1";

    //             $new_export_detail = HPS_Warehouse_Export_Details_Model::create($validatedData);

    //             tracking_info_service::add(
    //                 $asset->id, 
    //                 auth()->id(), 
    //                 12,
    //                 'requested export ' & $asset->current_serial_number & ' for receipt ' & $validatedData['hps_receipt'] & ' at'
    //             );

    //             $send_approval = Http::post(config('services.api_service.hps_export_request_url'), [
    //                 'ticket_owner' => auth()->user()->fullname,
    //                 'ticket_owner_email' => auth()->user()->email,
    //                 'receipt' => $validatedData['hps_receipt'],
    //                 'program' => match($validatedData) {
    //                     '1' => 'FB30',
    //                     '2' => 'Handover',
    //                     '3' => 'Luân chuyển nội bộ',
    //                     '4' => 'Sàn số serial',
    //                     '5' => 'WUE',
    //                     '6' => 'Xuất mượn',
    //                     default => 'Unknown',

    //                 },
    //                 'serial_number' => $validatedData['export_serial_number'],
    //                 'product_number' => $validatedData['export_product_number'],
    //                 'product_model' => $validatedData['export_model'],

    //             ]);

    //             if ($send_approval->successful()) {
    //                 return response()->json([
    //                     'success' => true,
    //                     'message' => 'Request xuất máy thành công, vui lòng đợi duyệt',
    //                 ]);
    //             } else {
    //                 // Xử lý lỗi nếu phản hồi không thành công
    //                 return response()->json([
    //                     'success' => false,
    //                     'message' => 'Request xuất máy thành công, nhưng API trả lỗi: ' . $send_approval->body(),
    //                 ], 500);
    //             } 
    //         }
            

            
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to export asset due to '.$e->getMessage(),
    //         ], 500);
    //     }
    // }


    public function Export_HPS_Asset(Request $request, $id){
    try {
            $validatedData = $request->validate([
                'hps_receipt' => 'required|string',
                'ce_owner' => 'nullable|exists:users,id',
                'program_support' => 'required',
                'export_date' => 'required|date',
                'export_site' => 'required|string',
                'export_location' => 'nullable|string',
                'export_product_number' => 'nullable|string',
                'export_model' => 'nullable|string',
                'export_serial_number' => 'nullable|string',
                'export_box_serial_number' => 'nullable|string',
                'note' => 'nullable|string',
            ]);

            $asset = HPS_Warehouse_Model::findOrFail($id);
            
            if (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_HPS_WAREHOUSE_ADMIN')) {
                $validatedData['asset_tag'] = $asset->asset_tag;
                $validatedData['user_export'] = auth()->id();
                $validatedData['current_status'] = "2";

                $new_export_detail = HPS_Warehouse_Export_Details_Model::create($validatedData);
                $asset->update([
                    'current_status' => "3",
                    'current_hps_receipt' => $validatedData['hps_receipt'],
                    'current_export_ticket_id' => $new_export_detail->id,
                ]);
                
                // Đã sửa nối chuỗi dùng dấu . thay vì dấu &
                tracking_info_service::add(
                    $asset->id, 
                    auth()->id(), 
                    12,
                    'export ' . $asset->current_serial_number . ' for receipt ' . $validatedData['hps_receipt'] . ' at'
                );
                
                return response()->json([
                    'success' => true,
                    'message' => 'Asset exported successfully',
                ]);
            } else {
                $validatedData['asset_tag'] = $asset->asset_tag;
                $validatedData['user_export'] = auth()->id();
                $validatedData['current_status'] = "1";

                $new_export_detail = HPS_Warehouse_Export_Details_Model::create($validatedData);

                // Đã sửa nối chuỗi dùng dấu . thay vì dấu &
                tracking_info_service::add(
                    $asset->id, 
                    auth()->id(), 
                    12,
                    'requested export ' . $asset->current_serial_number . ' for receipt ' . $validatedData['hps_receipt'] . ' at'
                );

                $send_approval = Http::post(config('services.api_service.hps_export_request_url'), [
                    'ticket_id' => $new_export_detail->id,
                    'ticket_owner' => auth()->user()->fullname,
                    'ticket_owner_email' => auth()->user()->email,
                    'receipt' => $validatedData['hps_receipt'],
                    // Đã sửa match truyền đúng trường program_support
                    'program' => match((string)$validatedData['program_support']) {
                        '1' => 'FB30',
                        '2' => 'Handover',
                        '3' => 'Luân chuyển nội bộ',
                        '4' => 'Sàn số serial',
                        '5' => 'WUE',
                        '6' => 'Xuất mượn',
                        default => 'Unknown',
                    },
                    'serial_number' => $validatedData['export_serial_number'],
                    'product_number' => $validatedData['export_product_number'], // Đã sửa từ product_number -> export_product_number
                    'product_model' => $validatedData['export_model'],
                    'asset_status' => match((string)$asset->unit_re_import_status){
                        '1' => 'New',
                        '2' => 'Good',
                        '3' => 'DOA',
                        '4' => 'Not good',
                        '5' => 'Scrap',
                        default => 'Unknown',
                    }
                ]);

                if ($send_approval->successful()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Request xuất máy thành công, vui lòng đợi duyệt',
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Request xuất máy thành công, nhưng API trả lỗi: ' . $send_approval->body(),
                    ], 500);
                } 
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export asset due to ' . $e->getMessage(),
            ], 500);
        }
    }

    public function Re_Import_HPS_Asset(Request $request, $id) {
        
        try {
            $validatedData = $request->validate([
                're_import_date' => 'required',
                're_import_location' => 'nullable',
                're_import_product_number' => 'required',
                're_import_model' => 'required',
                're_import_serial_number' => 'required',
                're_import_box_serial_number' => 'required',
                're_import_site' => 'required',
                're_import_status' => 'required',
                'note' => 'nullable',
            ]);

            $sanitize = function ($value) use (&$sanitize) {
                if (is_array($value)) {
                    return array_map($sanitize, $value);
                }
                return is_string($value) ? strip_tags(trim($value)) : $value;
            };

            // Áp dụng làm sạch cho toàn bộ $validated
            $validatedData = array_map($sanitize, $validatedData);
            
            $validatedData['user_re_import'] = auth()->id();

            $asset = HPS_Warehouse_Model::findOrFail($id);
            $asset['current_serial_number'] = $validatedData['re_import_serial_number'];
            $asset['current_box_serial_number'] = $validatedData['re_import_box_serial_number'];
            $asset['current_product_number'] = $validatedData['re_import_product_number'];
            $asset['model'] = $validatedData['re_import_model'];
            $asset['current_site'] = $validatedData['re_import_site'];
            $asset['current_location'] = $validatedData['re_import_location'];
            $asset['unit_re_import_status'] = $validatedData['re_import_status'];

            if (in_array($validatedData['re_import_status'], ["1", "2", "3"])) {
                $asset['current_status'] = "1";
            } else {
                $asset['current_status'] = "4";
            }
            $asset->save();
            $re_import_data = HPS_Warehouse_Export_Details_Model::findOrFail($asset['current_export_ticket_id']);
            
            $re_import_data->update($validatedData);

            tracking_info_service::add(
                $asset->id, 
                auth()->id(), 
                12,
                're-imported ' . $validatedData['re_import_serial_number'] . ' of receipt ' . $re_import_data['hps_receipt'] . ' at'
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Nhập lại thành công',
            ]);


        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to re-import asset due to '.$e->getMessage(),
            ], 500);
        }
    }

}
