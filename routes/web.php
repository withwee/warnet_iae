<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\IuranController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ForumController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard routes - redirect based on role
Route::middleware(['auth'])->group(function () {
    // User dashboard
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    
    // Admin dashboard
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
        ->middleware('admin')
        ->name('admin.dashboardAdmin');
    
    // Admin menu routes
    Route::get('/admin/pengumuman', [PengumumanController::class, 'index'])
        ->middleware('admin')
        ->name('admin.pengumuman');
    Route::get('/admin/forum', [UserController::class, 'forum'])
        ->middleware('admin')
        ->name('admin.forum');
    Route::get('/admin/kalender', [UserController::class, 'kalender'])
        ->middleware('admin')
        ->name('admin.kalender');
    Route::get('/admin/bayar-iuran', [UserController::class, 'bayarIuran'])
        ->middleware('admin')
        ->name('admin.bayar-iuran');
    
    // Pengumuman routes
    Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman');
    Route::post('/pengumuman', [PengumumanController::class, 'store'])->name('pengumuman.store');
    Route::put('/pengumuman/{id}', [PengumumanController::class, 'update'])->name('pengumuman.update');
    Route::delete('/pengumuman/{id}', [PengumumanController::class, 'destroy'])->name('pengumuman.destroy');
    Route::post('/pengumuman/{id}/toggle-khusus', [PengumumanController::class, 'toggleKhusus'])->name('pengumuman.toggleKhusus');
    
    // Kalender/Kegiatan routes
    Route::get('/kalender', [UserController::class, 'kalender'])->name('kalender');
    Route::post('/kegiatan', [KegiatanController::class, 'store'])->name('kegiatan.store');
    Route::put('/kegiatan/{id}', [KegiatanController::class, 'update'])->name('kegiatan.update');
    Route::delete('/kegiatan/{id}', [KegiatanController::class, 'destroy'])->name('kegiatan.destroy');
    
    // Iuran/Pembayaran routes
    Route::get('/bayar-iuran', [UserController::class, 'bayarIuran'])->name('bayar-iuran');
    Route::get('/iuran/cari', [IuranController::class, 'cari'])->name('iuran.cari');
    Route::post('/bayar-iuran', [IuranController::class, 'store'])->name('iuran.store');
    Route::get('/pay/snap-token/{id}', [IuranController::class, 'getSnapToken']);
    Route::get('/bayar-iuran/success/{order_id?}', function () {
    return view('succespay');})->name('iuran.success');
    Route::post('/bayar-iuran/update-status', [IuranController::class, 'updateStatus']);


    
    // Notifikasi route
    Route::get('/notifikasi', [UserController::class, 'notifikasi'])->name('notifikasi');
    
    // Forum routes
    Route::get('/forum', [UserController::class, 'forum'])->name('forum');
    Route::post('/forum', [\App\Http\Controllers\ForumController::class, 'store'])->name('forum.store');
    Route::post('/forum/{id}/reply', [\App\Http\Controllers\ForumController::class, 'reply'])->name('forum.reply');
    Route::delete('/forum/post/{id}', [\App\Http\Controllers\ForumController::class, 'deletePost'])->name('forum.deletePost');
    Route::delete('/forum/comment/{id}', [\App\Http\Controllers\ForumController::class, 'deleteComment'])->name('forum.deleteComment');
    
    // Admin Forum routes
    Route::post('/admin/forum', [\App\Http\Controllers\AdminForumController::class, 'store'])
        ->middleware('admin')
        ->name('admin.forum.store');
    Route::post('/admin/forum/{id}/reply', [\App\Http\Controllers\AdminForumController::class, 'reply'])
        ->middleware('admin')
        ->name('admin.forum.reply');
    Route::delete('/admin/forum/post/{id}', [\App\Http\Controllers\AdminForumController::class, 'deletePost'])
        ->middleware('admin')
        ->name('admin.forum.deletePost');
    Route::delete('/admin/forum/comment/{id}', [\App\Http\Controllers\AdminForumController::class, 'deleteComment'])
        ->middleware('admin')
        ->name('admin.forum.deleteComment');
    
    // Admin pengeluaran
    Route::post('/admin/pengeluaran', [AdminController::class, 'storePengeluaran'])
        ->middleware('admin')
        ->name('admin.pengeluaran.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
