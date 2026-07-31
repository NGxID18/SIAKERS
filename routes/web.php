<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AlkesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogPemeliharaanController;
use App\Http\Controllers\MutasiAlkesController;
use App\Http\Controllers\RuanganController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (Akses Terbuka Tanpa Login)
|--------------------------------------------------------------------------
*/

// Redirect login ke Dashboard Utama (Akses Terbuka)
Route::get('/login', function () {
    return redirect()->route('dashboard');
})->name('login');

Route::post('/login', function () {
    return redirect()->route('dashboard');
});

Route::post('/logout', function () {
    return redirect()->route('dashboard');
})->name('logout');

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

// Form Submissions (Rate Limit: 10 req/min)
Route::middleware(['throttle:10,1'])->group(function () {
    Route::post('alkes', [AlkesController::class, 'store'])->name('alkes.store');
    Route::post('mutasi', [MutasiAlkesController::class, 'store'])->name('mutasi.store');
    Route::post('pemeliharaan', [LogPemeliharaanController::class, 'store'])->name('pemeliharaan.store');
});
