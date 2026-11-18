<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestFetchXml extends Controller
{
    //
    public function index(){
        if (auth()->user()->hasRole('ROLE_SUPER_ADMIN')) {
            return view('test-fetch-xml');
        }
    }

    public function fetch(Request $request){
        
    }
}
