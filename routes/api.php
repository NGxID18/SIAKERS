<?php

use App\Models\Alkes;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/ruangan-by-seksi/{seksi_id}', function ($seksi_id) {
    return response()->json(Ruangan::where('seksi_id', $seksi_id)->get());
});

Route::get('/alkes/{id}', function ($id) {
    return response()->json(Alkes::with(['nomenklatur', 'seksi', 'ruangan'])->find($id));
});
