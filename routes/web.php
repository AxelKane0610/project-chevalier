<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\EEGTicketsController;
use App\Models\EEG_Software_Ticket;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('/login');
});

// 1. Trang hiển thị Form Login
Route::get('/login', [UserController::class, 'login'])->name('login');

// 2. Xử lý logic Login (Nơi thực hiện validate)
Route::post('/login', [UserController::class, 'authenticate']);



Route::middleware(['auth'])->group(function () {
    
    Route::get('/main-menu', function () {
        return view('main-menu');
    });

    Route::get('/software-tickets-menu', function () {
        $tickets = EEG_Software_Ticket::where('user_id', auth()->id())->get(); // Lấy tất cả ticket của user đang đăng nhập
        return view('software-tickets-menu', compact('tickets'));
    });

    Route::post('/create-software-ticket', [EEGTicketsController::class, 'Create_Software_Ticket']);

    Route::get('/software-tickets-menu-details/{id}', [EEGTicketsController::class, 'Show_Software_Ticket_Details']);

});