<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ApprovalController;
use App\Http\Controllers\TTEXTicketsController;

Route::post('/v1/power-automate/callback', [ApprovalController::class, 'handleCallback']);
Route::get('/v1/power-automate/good-part-booking', [TTEXTicketsController::class, 'Power_Automate_Good_Part_Booking']);