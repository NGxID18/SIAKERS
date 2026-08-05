<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AlkesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogPemeliharaanController;
use App\Http\Controllers\MutasiAlkesController;
use App\Http\Controllers\RuanganController;
use App\Http\Middleware\EnsureSessionRole;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (SIAKERS - RSJKO Engku Haji Daud)
|--------------------------------------------------------------------------
*/

// Auth Routes (Login & Logout)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Web Pages Protected by Session Role (Default: Must Login First)
Route::middleware(['throttle:60,1', EnsureSessionRole::class])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('alkes', AlkesController::class)->except(['store']);

    Route::get('mutasi', [MutasiAlkesController::class, 'index'])->name('mutasi.index');
    Route::get('mutasi/buat', [MutasiAlkesController::class, 'create'])->name('mutasi.create');

    Route::get('pemeliharaan', [LogPemeliharaanController::class, 'index'])->name('pemeliharaan.index');
    Route::get('pemeliharaan/buat', [LogPemeliharaanController::class, 'create'])->name('pemeliharaan.create');

    Route::get('ruangan', [RuanganController::class, 'index'])->name('ruangan.index');
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
});

// Form Submissions Protected by Session Role
Route::middleware(['throttle:20,1', EnsureSessionRole::class])->group(function () {
    Route::post('alkes', [AlkesController::class, 'store'])->name('alkes.store');
    Route::post('mutasi', [MutasiAlkesController::class, 'store'])->name('mutasi.store');
    Route::post('pemeliharaan', [LogPemeliharaanController::class, 'store'])->name('pemeliharaan.store');
    
    // Otoritas Elektromedis: Selesaikan Perbaikan & Kembalikan Alat
    Route::post('pemeliharaan/{id}/selesai', [LogPemeliharaanController::class, 'resolve'])->name('pemeliharaan.resolve');
    Route::post('notifications/read-all', [LogPemeliharaanController::class, 'markNotificationsRead'])->name('notifications.read-all');
});
