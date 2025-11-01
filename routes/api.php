<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\IuranController;
use App\Http\Controllers\MidtransController;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::middleware('auth:api')->get('/dashboard', [UserController::class, 'dashboard']);
Route::middleware('auth:api')->post('/logout', [UserController::class, 'logout']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::middleware('auth:api')->get('/dashboard', [UserController::class, 'dashboard']);
Route::middleware('auth:api')->post('/logout', [UserController::class, 'logout']);

Route::post('/midtrans/callback', [MidtransController::class, 'callback']);
Route::post('/midtrans/callback', [MidtransController::class, 'callback']);

