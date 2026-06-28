<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Spectre_Crown_Warehouse_Model;

class SpectreCrownWarehouseController extends Controller
{
    //
    public function index()
    {
        $items = Spectre_Crown_Warehouse_Model::all();
        return view('spectre-crown-warehouse-menu', compact('items'));
    }
}
