<?php

use App\Http\Controllers\ClockController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [ClockController::class, 'index']);
