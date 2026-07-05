<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\EEGTicketsController;
use App\Models\EEG_Software_Ticket;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules\Can;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\InvoiceExceptionalTicketsController;
use App\Models\Laser_Engraving_Tickets_Model;
use App\Http\Controllers\LaserEngravingTicketsController;
use App\Http\Controllers\OutOfOfficeTicketsController;
use App\Models\Out_Of_Office_Tickets_Model;
use App\Http\Controllers\ThermalEventExceptionalTicketsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SpectreCrownWarehouseController;
use App\Http\Controllers\LoanUnitPartTicketsController;

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
    Route::get('/search-receipt', [SearchController::class, 'search'])->name('search.receipt');
    Route::get('/main-menu', function () {
        return view('main-menu');
    });

    Route::get('/user-profile', [UserController::class,'User_Profile'])->name('user-profile');

    Route::get('/attachments/{folder}/{id}/{filename}', [AttachmentController::class, 'show']);

    
    //1. EEG Ticket
    Route::middleware(['role:ROLE_SUPER_ADMIN,ROLE_SW_TICKET_USER,ROLE_TICKET_SW_ADMIN'])->group(function () {
    
        Route::get('/software-tickets-menu', [EEGTicketsController::class, 'Show_Pending_Tickets']);
        
        Route::get('/software-tickets-menu-details/{id}', [EEGTicketsController::class, 'Show_Software_Ticket_Details']);
        
        // Thêm các route khác vào đây...
        Route::post('/create-software-ticket', [EEGTicketsController::class, 'Create_Software_Ticket']);
        Route::post('/send-approval-request/{id}', [EEGTicketsController::class, 'Send_Approval_Request']) ->name('send-approval-request');
        Route::post('/add-comment-software-ticket/{id}', [EEGTicketsController::class, 'Add_Comment_Software_Ticket']) ->name('add-comment-software-ticket');
        Route::patch('/re-open-ticket/{id}', [EEGTicketsController::class, 'Re_Open_Ticket']) ->name('re-open-ticket');
        Route::patch('/close-software-ticket/{id}', [EEGTicketsController::class, 'Close_Software_Ticket']) ->name('close-software-ticket');
        Route::patch('/edit-software-ticket/{id}',[EEGTicketsController::class, 'Edit_Software_Ticket'])->name('edit-software-ticket');
        Route::patch('/change-ticket-software-status-to-in-progress/{id}', [EEGTicketsController::class, 'Change_Ticket_Software_Status_To_In_Progress'])->name('change-ticket-software-status-to-in-progress');
        Route::post('/approve-ticket/{id}', [EEGTicketsController::class, 'Approve_Ticket'])->name('approve-ticket');
        Route::post('/reject-ticket/{id}', [EEGTicketsController::class, 'Reject_Ticket'])->name('reject-ticket');
    });

    //3. Khắc base
    Route::middleware(['role:ROLE_LASER_ENGRAVING_USER,ROLE_LASER_ENGRAVING_ADMIN,ROLE_SUPER_ADMIN'])->group(function () {
        Route::get('/laser-engraving-menu', [LaserEngravingTicketsController::class, 'Show_Pending_Tickets']);
        Route::get('/laser-engraving-menu-details/{id}', [LaserEngravingTicketsController::class, 'Show_Laser_Engraving_Ticket_Details']);
        Route::post('/create-laser-engraving-ticket', [LaserEngravingTicketsController::class, 'Create_Laser_Engraving_Ticket']);
        Route::patch('/edit-laser-engraving-ticket/{id}', [LaserEngravingTicketsController::class, 'Edit_Laser_Engraving_Ticket'])->name('edit-laser-engraving-ticket');
        Route::patch('/re-open-laser-engraving-ticket/{id}', [LaserEngravingTicketsController::class,'Re_Open_Laser_Engraving_Ticket'])->name('re-open-laser-engraving-ticket');
        Route::post('/add-comment-laser-engraving-ticket/{id}', [LaserEngravingTicketsController::class, 'Add_Comment_Laser_Engraving_Ticket']) ->name('add-comment-laser-engraving-ticket');
        Route::patch('/change-laser-engraving-status-to-in-progress/{id}', [LaserEngravingTicketsController::class, 'Change_Laser_Engraving_Status_To_In_Progress'])->name('change-laser-engraving-status-to-in-progress');
        Route::patch('/close-laser-engraving-ticket/{id}', [LaserEngravingTicketsController::class, 'Close_Laser_Engraving_Ticket'])->name('close-laser-engraving-ticket');
    });

    //10. Thermal Event
    Route::middleware(['role:ROLE_THERMAL_EVENT_USER,ROLE_THERMAL_EVENT_LV1_APPROVER,ROLE_THERMAL_EVENT_LV2_APPROVER,ROLE_SUPER_ADMIN'])->group(function () {
        Route::get('/thermal-event-tickets-menu', [ThermalEventExceptionalTicketsController::class, 'Show_Pending_Tickets']);
        Route::get('/thermal-event-tickets-menu-details/{id}', [ThermalEventExceptionalTicketsController::class, 'Show_Thermal_Event_Ticket_Details']);
        Route::post('/create-thermal-event-ticket', [ThermalEventExceptionalTicketsController::class, 'Create_Thermal_Event_Ticket']);
        Route::patch('/edit-thermal-event-ticket/{id}', [ThermalEventExceptionalTicketsController::class, 'Edit_Thermal_Event_Ticket'])->name('edit-thermal-event-ticket');
        
        Route::post('/add-comment-thermal-event-ticket/{id}', [ThermalEventExceptionalTicketsController::class, 'Add_Comment_Thermal_Event_Ticket']) ->name('add-comment-thermal-event-ticket');
        Route::patch('/close-thermal-event-ticket/{id}', [ThermalEventExceptionalTicketsController::class, 'Close_Thermal_Event_Ticket'])->name('close-thermal-event-ticket');
        Route::post('/thermal-event-approve-lv1/{id}', [ThermalEventExceptionalTicketsController::class,'Thermal_Event_Approve_Lv1'])->name('thermal-event-approve-lv1');
        Route::post('/thermal-event-approve-lv2/{id}', [ThermalEventExceptionalTicketsController::class,'Thermal_Event_Approve_Lv2'])->name('thermal-event-approve-lv2');
        Route::post('/thermal-event-reject/{id}', [ThermalEventExceptionalTicketsController::class,'Thermal_Event_Reject'])->name('thermal-event-reject');
        Route::patch('/re-open-thermal-event-ticket/{id}', [ThermalEventExceptionalTicketsController::class,'Thermal_Event_Re_Open'])->name('re-open-thermal-event-ticket');
        Route::post('/add-thermal-event-part/{id}',[ThermalEventExceptionalTicketsController::class,'Add_Thermal_Event_Part'])->name('add-thermal-event-part');

        Route::patch('/edit-thermal-event-part-details/{id}', [ThermalEventExceptionalTicketsController::class, 'Edit_Thermal_Event_Part_Details'])->name('edit-thermal-event-part-details');
        Route::patch('/delete-thermal-event-part-details/{id}', [ThermalEventExceptionalTicketsController::class, 'Delete_Thermal_Event_Part_Details'])->name('delete-thermal-event-part-details');
    });

    //7. Invoice Exceptional
    Route::middleware(['role:ROLE_INVOICE_EXCEPTIONAL_USER,ROLE_SUPER_ADMIN,ROLE_INVOICE_EXCEPTIONAL_L1_APPROVER,ROLE_INVOICE_EXCEPTIONAL_L2_APPROVER'])->group(function () {
        Route::get('/invoice-exceptional-menu', [InvoiceExceptionalTicketsController::class, 'Show_Pending_Tickets']);
        Route::get('/invoice-exceptional-menu-details/{id}', [InvoiceExceptionalTicketsController::class, 'Show_Invoice_Exceptional_Ticket_Details']);
        Route::post('/create-invoice-exceptional-ticket', [InvoiceExceptionalTicketsController::class, 'Create_Invoice_Exceptional_Tickets']);
        Route::post('/add-comment-invoice-exceptional-ticket/{id}', [InvoiceExceptionalTicketsController::class, 'Add_Comment_Invoice_Exceptional_Ticket']) ->name('add-comment-invoice-exceptional-ticket');
        Route::patch('/edit-invoice-exceptional-ticket/{id}', [InvoiceExceptionalTicketsController::class, 'Edit_Invoice_Exceptional_Ticket'])->name('edit-invoice-exceptional-ticket');
        Route::post('/send-approve-invoice-exceptional/{id}', [InvoiceExceptionalTicketsController::class, 'Send_Approve_Invoice_Exceptional']) ->name('send-approve-invoice-exceptional');
        Route::post('/invoice-exceptional-approve-lv1/{id}', [InvoiceExceptionalTicketsController::class,'Invoice_Exceptional_Approve_Lv1'])->name('invoice-exceptional-approve-lv1');
        Route::post('/invoice-exceptional-approve-lv2/{id}', [InvoiceExceptionalTicketsController::class,'Invoice_Exceptional_Approve_Lv2'])->name('invoice-exceptional-approve-lv2');
        Route::post('/invoice-exceptional-reject/{id}', [InvoiceExceptionalTicketsController::class,'Invoice_Exceptional_Reject'])->name('invoice-exceptional-reject');
        Route::patch('/re-open-invoice-exceptional-ticket/{id}', [InvoiceExceptionalTicketsController::class,'Re_Open_Invoice_Exceptional_Ticket'])->name('re-open-invoice-exceptional-ticket');

    });

    //6. Quản lý SubK
    Route::middleware(['role:ROLE_SUPER_ADMIN'])->group(function () {
        // Các route chỉ dành cho ROLE_SUPER_ADMIN
        Route::get('/subk-management', [UserController::class, 'index']);
        
        Route::post('/create-new-user', [UserController::class,'Create_New_User']) ->name('create-new-user');
        Route::patch('/edit-user-info/{id}', [UserController::class,'Edit_User_Info'])->name('edit-user-info');
    });

    //9. Nghỉ phép
    Route::middleware(['role:ROLE_SUPER_ADMIN,ROLE_OUT_OF_OFFICE_USER,ROLE_OUT_OF_OFFICE_ADMIN'])->group(function () {
        Route::get('/out-of-office-tickets-menu', [OutOfOfficeTicketsController::class, 'Show_Pending_Tickets']);
        Route::post('/create-out-of-office-ticket', [OutOfOfficeTicketsController::class, 'Create_Out_Of_Office_Ticket']);
        Route::get('/out-of-office-tickets-menu-details/{id}', [OutOfOfficeTicketsController::class, 'Show_Out_Of_Office_Ticket_Details']);
        Route::patch('/edit-out-of-office-ticket/{id}', [OutOfOfficeTicketsController::class, 'Edit_Out_Of_Office_Ticket'])->name('edit-out-of-office-ticket');
        Route::post('/add-comment-out-of-office-ticket/{id}', [OutOfOfficeTicketsController::class, 'Add_Comment_Out_Of_Office_Ticket']) ->name('add-comment-out-of-office-ticket');
    });

    //11. Quản lý kho Crown - Spectre
    Route::middleware(['role:ROLE_SUPER_ADMIN'])->group(function () {
        Route::get('/spectre-crown-warehouse-menu', [SpectreCrownWarehouseController::class, 'index']);
        Route::get('/spectre-crown-warehouse-item-details/{id}', [SpectreCrownWarehouseController::class, 'Item_Details']);

    });

    //4. Ticket mượn máy & mượn part
    Route::middleware(['role:ROLE_SUPER_ADMIN,ROLE_LOAN_UNIT_ADMIN,ROLE_LOAN_UNIT_USER'])->group(function () {
        Route::get('/loan-unit-part-menu',[LoanUnitPartTicketsController::class, 'Show_Pending_Tickets']);
        Route::get('/loan-unit-part-ticket-details/{id}', [LoanUnitPartTicketsController::class, 'Show_Loan_Unit_Part_Ticket_Details']) ->name('loan-unit-part-ticket-details');
        Route::post('/create-loan-unit-part-ticket', [LoanUnitPartTicketsController::class, 'Create_Loan_Unit_Part_Ticket']);
        Route::post('/add-comment-loan-unit-part-ticket/{id}', [LoanUnitPartTicketsController::class, 'Add_Comment_Loan_Unit_Part_Ticket']) ->name('add-comment-loan-unit-part-ticket');
        Route::patch('/edit-loan-unit-part-ticket/{id}', [LoanUnitPartTicketsController::class, 'Edit_Loan_Unit_Part_Ticket'])->name('edit-loan-unit-part-ticket');
        Route::patch('/edit-loan-unit-part-details/{id}', [LoanUnitPartTicketsController::class, 'Edit_Loan_Unit_Part_Details'])->name('edit-loan-unit-part-details');
        Route::patch('/issue-loan-unit-part/{id}', [LoanUnitPartTicketsController::class, 'Issue_Loan_Unit_Part'])->name('issue-loan-unit-part');
        Route::post('/add-loan-unit-part/{id}', [LoanUnitPartTicketsController::class, 'Add_Loan_Unit_Part'])->name('add-loan-unit-part');
        Route::patch('/return-loan-unit-part/{id}', [LoanUnitPartTicketsController::class, 'Return_Loan_Unit_Part'])->name('return-loan-unit-part');
        Route::patch('/close-loan-unit-part-ticket/{id}', [LoanUnitPartTicketsController::class, 'Close_Loan_Unit_Part_Ticket'])->name('close-loan-unit-part-ticket');
        


    });

    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
    

});