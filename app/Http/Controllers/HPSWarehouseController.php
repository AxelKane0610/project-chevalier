<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HPS_Warehouse_Model;


class HPSWarehouseController extends Controller
{
    //
    public function index(Request $request)
    {
        if (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_HPS_WAREHOUSE_ADMIN')) {
            $query = HPS_Warehouse_Model::query();
        } else {
            $query = HPS_Warehouse_Model::where('current_site', auth()->user()->site_id());
        }
        

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


        $items = $query->paginate(10);

        if ($request->ajax()) {
            return view('tables.hps-warehouse-items-table', compact('items'))->render();
        }

        return view('hps-warehouse-menu', compact('items'));
    }
}
