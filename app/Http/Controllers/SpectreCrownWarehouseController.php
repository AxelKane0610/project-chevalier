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
    public function index() {
        $items = Spectre_Crown_Warehouse_Model::paginate(10);
        return view('spectre-crown-warehouse-menu', compact('items'));
    }

    public function Item_Details($id){
        $item_details = Spectre_Crown_Warehouse_Model::with(['active_attachments','ticket_tracking_info','ticket_comments.attachments', 'ticket_comments.user', 'loan_unit_part_tickets'])->findOrFail($id);
        return view('spectre-crown-warehouse-item-details', compact('item_details'));
    }
}
