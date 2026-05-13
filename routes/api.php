<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ApprovalController;

Route::post('/v1/power-automate/callback', [ApprovalController::class, 'handleCallback']);