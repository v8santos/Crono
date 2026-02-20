<?php

use App\Http\Controllers\ClockController;
use Illuminate\Support\Facades\Route;

Route::get('/clock', [ClockController::class, 'index']);
Route::post('/clock/check', [ClockController::class, 'check']);
Route::get('/clock/metrics', [ClockController::class, 'metrics']);