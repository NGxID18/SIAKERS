<?php

use App\Models\Alkes;
use App\Models\Ruangan;
use Illuminate\Support\Facades\Route;

Route::get('/ruangan', function () {
    return response()->json(Ruangan::all());
});

Route::get('/alkes/{id}', function ($id) {
    return response()->json(Alkes::with(['nomenklatur', 'ruangan', 'lokasiRuangan'])->find($id));
});
