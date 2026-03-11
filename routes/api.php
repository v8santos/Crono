<?php

use App\Http\Controllers\Api\ClockController;
use Illuminate\Support\Facades\Route;

Route::get('/clock', [ClockController::class, 'index']);
Route::get('/clock/all', [ClockController::class, 'getAll']);
Route::post('/clock/check', [ClockController::class, 'check']);
Route::post('/clock/check/adjustment', [ClockController::class, 'adjustment']);
Route::get('/clock/metrics', [ClockController::class, 'metrics']);

// TODO: Adicionar notificação para verificar se ainda estou trabalhando
// Talvez colocar um campo para horário previsto para fim de sessão

// TODO: Notificação perguntando como está o tempo lá fora
// O intuito aqui é tirarmos os olhos da tela por alguns minutos

// TODO: Adicionar regra de intervalo