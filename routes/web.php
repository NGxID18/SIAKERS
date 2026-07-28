<?php

use App\Http\Controllers\AlkesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogPemeliharaanController;
use App\Http\Controllers\MutasiAlkesController;
use App\Http\Controllers\SeksiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes & Security Layer
|--------------------------------------------------------------------------
*/

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Web Pages (Rate Limit: 60 req/min)
Route::middleware(['throttle:60,1'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('alkes', AlkesController::class)->except(['store']);

    Route::get('mutasi', [MutasiAlkesController::class, 'index'])->name('mutasi.index');
    Route::get('mutasi/buat', [MutasiAlkesController::class, 'create'])->name('mutasi.create');

    Route::get('pemeliharaan', [LogPemeliharaanController::class, 'index'])->name('pemeliharaan.index');
    Route::get('pemeliharaan/buat', [LogPemeliharaanController::class, 'create'])->name('pemeliharaan.create');

    Route::get('seksi', [SeksiController::class, 'index'])->name('seksi.index');
});

// Form Submissions (Rate Limit: 10 req/min)
Route::middleware(['throttle:10,1'])->group(function () {
    Route::post('alkes', [AlkesController::class, 'store'])->name('alkes.store');
    Route::post('mutasi', [MutasiAlkesController::class, 'store'])->name('mutasi.store');
    Route::post('pemeliharaan', [LogPemeliharaanController::class, 'store'])->name('pemeliharaan.store');
    Route::post('seksi', [SeksiController::class, 'store'])->name('seksi.store');
});
