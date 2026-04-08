<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\EEGTicketsController;
use App\Models\EEG_Software_Ticket;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules\Can;

Route::get('/', function () {
    // Auth::logout();
    // session()->flush();
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

     // Chỉ cho phép ROLE_SUPER_ADMIN truy cập vào route này, nếu không sẽ bị trả về lỗi 403 Forbidden
    // Route::get('/software-tickets-menu', function () {
    //     $tickets = EEG_Software_Ticket::where('user_id', auth()->id())->get();
    //     return view('software-tickets-menu', compact('tickets'));
    // })->middleware('role:ROLE_SUPER_ADMIN,ROLE_SW_TICKET_USER,ROLE_TICKET_SW_ADMIN'); // Sử dụng Gate 'check_role' để kiểm tra nếu user có role 'ROLE_SUPER_ADMIN' thì mới cho phép truy cập vào route này

    // Route::get('/software-tickets-menu-details/{id}', [EEGTicketsController::class, 'Show_Software_Ticket_Details'])->middleware('role:ROLE_SUPER_ADMIN,ROLE_SW_TICKET_USER,ROLE_TICKET_SW_ADMIN');

    Route::middleware(['role:ROLE_SUPER_ADMIN,ROLE_SW_TICKET_USER,ROLE_TICKET_SW_ADMIN'])->group(function () {
    
        Route::get('/software-tickets-menu', function () {
            $tickets = EEG_Software_Ticket::where('user_id', auth()->id())->get();
            return view('software-tickets-menu', compact('tickets'));
        });

        Route::get('/software-tickets-menu-details/{id}', [EEGTicketsController::class, 'Show_Software_Ticket_Details']);
        
        // Thêm các route khác vào đây...
        Route::post('/create-software-ticket', [EEGTicketsController::class, 'Create_Software_Ticket']);
        Route::patch('/change-ticket-status/{id}/status', [EEGTicketsController::class, 'Change_Software_Ticket_Status']);

    });

    
    

});