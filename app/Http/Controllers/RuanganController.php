<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;

class RuanganController extends Controller
{
    public function index()
    {
        $ruanganList = Ruangan::withCount('alkes')
            ->orderBy('nama_ruangan', 'asc')
            ->get();

        return view('ruangan.index', compact('ruanganList'));
    }
}
