<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AlkesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogPemeliharaanController;
use App\Http\Controllers\MutasiAlkesController;
use App\Http\Controllers\RuanganController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (SIAKER - Sistem Inventaris Alat Kesehatan)
|--------------------------------------------------------------------------
*/

// Auth Routes (Login Tanpa Password & Logout)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Role Switcher untuk Pengujian Cepat Elektromedis vs Petugas Ruangan
Route::get('/switch-role/{role}', function ($role) {
    if ($role === 'elektromedis') {
        session([
            'user_role' => 'elektromedis',
            'user_role_label' => 'Ruangan Elektromedis (Admin SIAKER)',
            'user_ruangan_name' => 'Elektromedis',
        ]);
    } else {
        session([
            'user_role' => 'ruangan',
            'user_role_label' => 'Petugas Ruangan Operasional',
            'user_ruangan_name' => 'Ruangan Operasional',
        ]);
    }
    return redirect()->back()->with('success', 'Peran diubah ke: ' . session('user_role_label'));
})->name('switch-role');

// Web Pages (Rate Limit: 60 req/min)
Route::middleware(['throttle:60,1'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('alkes', AlkesController::class)->except(['store']);

    Route::get('mutasi', [MutasiAlkesController::class, 'index'])->name('mutasi.index');
    Route::get('mutasi/buat', [MutasiAlkesController::class, 'create'])->name('mutasi.create');

    Route::get('pemeliharaan', [LogPemeliharaanController::class, 'index'])->name('pemeliharaan.index');
    Route::get('pemeliharaan/buat', [LogPemeliharaanController::class, 'create'])->name('pemeliharaan.create');

    Route::get('ruangan', [RuanganController::class, 'index'])->name('ruangan.index');
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
});

// Form Submissions (Rate Limit: 20 req/min)
Route::middleware(['throttle:20,1'])->group(function () {
    Route::post('alkes', [AlkesController::class, 'store'])->name('alkes.store');
    Route::post('mutasi', [MutasiAlkesController::class, 'store'])->name('mutasi.store');
    Route::post('pemeliharaan', [LogPemeliharaanController::class, 'store'])->name('pemeliharaan.store');
    
    // Otoritas Elektromedis: Selesaikan Perbaikan & Kembalikan Alat
    Route::post('pemeliharaan/{id}/selesai', [LogPemeliharaanController::class, 'resolve'])->name('pemeliharaan.resolve');
    Route::post('notifications/read-all', [LogPemeliharaanController::class, 'markNotificationsRead'])->name('notifications.read-all');
});
