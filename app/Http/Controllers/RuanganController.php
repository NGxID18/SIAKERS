<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;

class RuanganController extends Controller
{
    public function index()
    {
        $ruanganGrouped = Ruangan::withCount('alkes')
            ->orderBy('lokasi_lantai', 'asc')
            ->orderBy('nama_ruangan', 'asc')
            ->get()
            ->groupBy('lokasi_lantai');

        return view('ruangan.index', compact('ruanganGrouped'));
    }
}
