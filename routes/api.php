<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\IuranController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\Api\ChatGroupController;

// Authentication Routes
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

// Protected Routes
Route::middleware('auth:api')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard']);
    Route::post('/logout', [UserController::class, 'logout']);
    
    // Chat Group Routes
    Route::prefix('chat')->group(function () {
        // Get connection info for gRPC
        Route::get('/connection-info', [ChatGroupController::class, 'connectionInfo']);
        
        // Group management
        Route::get('/groups', [ChatGroupController::class, 'index']);
        Route::post('/groups', [ChatGroupController::class, 'store']);
        Route::get('/groups/{groupId}', [ChatGroupController::class, 'show']);
        Route::post('/groups/{groupId}/join', [ChatGroupController::class, 'join']);
        Route::post('/groups/{groupId}/leave', [ChatGroupController::class, 'leave']);
        
        // Message history
        Route::get('/groups/{groupId}/messages', [ChatGroupController::class, 'messages']);
    });
});

// Midtrans Payment Callback (public)
Route::post('/midtrans/callback', [MidtransController::class, 'callback']);
