<?php

namespace App\Http\Controllers;

use App\Models\Seksi;
use Illuminate\Http\Request;

class SeksiController extends Controller
{
    private function isAdmin()
    {
        return session('is_admin', false);
    }

    public function index()
    {
        $seksiList = Seksi::with(['ruangan', 'alkes.nomenklatur'])->get();
        $isAdmin = $this->isAdmin();

        return view('seksi.index', compact('seksiList', 'isAdmin'));
    }

    public function store(Request $request)
    {
        if (!$this->isAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Administrator RS yang memiliki wewenang untuk mengelola (CRUD) Master Seksi dan Ruangan!');
        }

        $validated = $request->validate([
            'kode_seksi' => 'required|unique:seksi,kode_seksi',
            'nama_seksi' => 'required|string',
            'penanggung_jawab' => 'nullable|string',
            'kontak' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        Seksi::create($validated);

        return redirect()->route('seksi.index')->with('success', 'Data Seksi berhasil ditambahkan!');
    }
}
