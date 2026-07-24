<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Spectre_Crown_Warehouse_Model;
use App\Models\Loan_Unit_Part_Tickets_Model;
use App\Models\Attachments_Model;
use App\Models\Comments_Model;
use App\Services\tracking_info_service;

class SpectreCrownWarehouseController extends Controller
{
    //
    // public function index(Request $request) {
    //     $query = Spectre_Crown_Warehouse_Model::query();

    // // 3. Nếu người dùng nhập ô tìm kiếm (ví dụ tìm theo serial_number hoặc model)
    //     if ($request->filled('search')) {
    //         $search = $request->search;
    //         $query->where(function($q) use ($search) {
    //             $q->where('serial_number', 'like', "%{$search}%")
    //             ->orWhere('model', 'like', "%{$search}%")
    //             ->orWhere('asset_tag', 'like', "%{$search}%");
    //         });

    //     }

    //     // 4. Phân trang
    //     $items = $query->paginate(10);

    //     // 5. Nếu gửi từ Javascript (AJAX) -> Chỉ trả về partial view chứa bảng dữ liệu
    //     if ($request->ajax()) {
    //         return view('tables.spectre-crown-warehouse-items-table', compact('items'))->render();
    //     }

    //     // 6. Nếu truy cập bình thường -> Trả về full trang giao diện
    //     return view('spectre-crown-warehouse-menu', compact('items'));
    // }

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


}
