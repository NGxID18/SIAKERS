<?php

use App\Http\Controllers\AlkesController;
use App\Models\Alkes;
use App\Models\Ruangan;
use Illuminate\Support\Facades\Route;

Route::get('/ruangan', function () {
    return response()->json(Ruangan::all());
});

Route::match(['get', 'post'], '/alkes', [AlkesController::class, 'apiHandler'])->name('api.alkes.handler');
Route::match(['get', 'post'], '/alkes/sync', [AlkesController::class, 'apiSync'])->name('api.alkes.sync');

Route::get('/alkes/{id}', function ($id) {
    return response()->json(Alkes::with(['nomenklatur', 'ruangan', 'lokasiRuangan'])->find($id));
});
