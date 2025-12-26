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

    // Pengumuman Routes
    Route::get('/pengumuman', [App\Http\Controllers\PengumumanController::class, 'indexApi']);
    Route::post('/pengumuman', [App\Http\Controllers\PengumumanController::class, 'storeApi']);
    Route::put('/pengumuman/{id}', [App\Http\Controllers\PengumumanController::class, 'updateApi']);
    Route::delete('/pengumuman/{id}', [App\Http\Controllers\PengumumanController::class, 'destroyApi']);

    // Forum Routes
    Route::get('/forum', [App\Http\Controllers\ForumController::class, 'indexApi']);
    Route::post('/forum', [App\Http\Controllers\ForumController::class, 'storeApi']);
    Route::post('/forum/{id}/reply', [App\Http\Controllers\ForumController::class, 'replyApi']);
    Route::delete('/forum/post/{id}', [App\Http\Controllers\ForumController::class, 'deletePostApi']);
    Route::delete('/forum/comment/{id}', [App\Http\Controllers\ForumController::class, 'deleteCommentApi']);

    // Kalender/Kegiatan Routes
    Route::get('/kegiatan', [App\Http\Controllers\KegiatanController::class, 'indexApi']);
    Route::post('/kegiatan', [App\Http\Controllers\KegiatanController::class, 'storeApi']);
    Route::put('/kegiatan/{id}', [App\Http\Controllers\KegiatanController::class, 'updateApi']);
    Route::delete('/kegiatan/{id}', [App\Http\Controllers\KegiatanController::class, 'destroyApi']);

    // Iuran/Pembayaran Routes
    Route::get('/iuran', [App\Http\Controllers\IuranController::class, 'indexApi']);
    Route::post('/iuran', [App\Http\Controllers\IuranController::class, 'storeApi']);
    Route::get('/iuran/{id}', [App\Http\Controllers\IuranController::class, 'showApi']);

    // Notifikasi Route
    Route::get('/notifikasi', [App\Http\Controllers\UserController::class, 'notifikasiApi']);

    // Profile Routes
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'showApi']);
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'updateApi']);
    Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroyApi']);
});

// Midtrans Payment Callback (public)
Route::post('/midtrans/callback', [MidtransController::class, 'callback']);
