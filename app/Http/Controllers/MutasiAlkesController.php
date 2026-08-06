<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Alkes;
use App\Models\MutasiAlkes;
use App\Models\Ruangan;
use Illuminate\Http\Request;

class MutasiAlkesController extends Controller
{
    public function index(Request $request)
    {
        $query = MutasiAlkes::with(['alkes.ruangan', 'ruanganAsal', 'ruanganTujuan']);

        // Search Bar (Cari Alkes, SN, Pemohon, atau Alasan Mutasi)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('alasan_mutasi', 'like', "%{$search}%")
                  ->orWhere('pemohon', 'like', "%{$search}%")
                  ->orWhere('penanggung_jawab', 'like', "%{$search}%")
                  ->orWhereHas('alkes', function ($aq) use ($search) {
                      $aq->where('nama_barang', 'like', "%{$search}%")
                         ->orWhere('nomor_seri', 'like', "%{$search}%");
                  });
            });
        }

        // Filter 1: Ruangan Asal
        if ($request->filled('ruangan_asal_id')) {
            $query->where('ruangan_asal_id', $request->ruangan_asal_id);
        }

        // Filter 2: Ruangan Tujuan
        if ($request->filled('ruangan_tujuan_id')) {
            $query->where('ruangan_tujuan_id', $request->ruangan_tujuan_id);
        }

        $mutasiList = $query->latest()->paginate(30)->withQueryString();
        $ruanganList = Ruangan::orderBy('nama_ruangan', 'asc')->get();

        return view('mutasi.index', compact('mutasiList', 'ruanganList'));
    }

    public function create(Request $request)
    {
        $selectedAlkesId = $request->query('alkes_id');
        $alkesList = Alkes::with(['ruangan', 'lokasiRuangan'])->get();
        $ruanganList = Ruangan::orderBy('nama_ruangan', 'asc')->get();

        return view('mutasi.create', compact('alkesList', 'ruanganList', 'selectedAlkesId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'alkes_id' => 'required|exists:alkes,id',
            'ruangan_tujuan_id' => 'required|exists:ruangan,id',
            'pemohon' => 'required|string|max:255',
            'penanggung_jawab' => 'required|string|max:255',
            'alasan_mutasi' => 'required|string',
        ]);

        $alkes = Alkes::findOrFail($validated['alkes_id']);
        $ruanganAsalId = $alkes->lokasi_ruangan_id ?? $alkes->ruangan_id;

        if ($ruanganAsalId == $validated['ruangan_tujuan_id']) {
            return back()->withErrors(['ruangan_tujuan_id' => 'Ruangan tujuan harus berbeda dari ruangan asal fisik saat ini!']);
        }

        // Simpan Log Mutasi
        $mutasi = MutasiAlkes::create([
            'alkes_id' => $alkes->id,
            'ruangan_asal_id' => $ruanganAsalId,
            'ruangan_tujuan_id' => $validated['ruangan_tujuan_id'],
            'tanggal_mutasi' => now(),
            'pemohon' => $validated['pemohon'],
            'penanggung_jawab' => $validated['penanggung_jawab'],
            'alasan_mutasi' => $validated['alasan_mutasi'],
            'status_persetujuan' => 'Disetujui',
        ]);

        // Perbarui Lokasi Keberadaan Fisik Alat (ruangan_id Asli Aset Tetap Tidak Berubah)
        $alkes->update([
            'lokasi_ruangan_id' => $validated['ruangan_tujuan_id'],
        ]);

        $mutasi->load(['ruanganAsal', 'ruanganTujuan']);
        $rAsal = $mutasi->ruanganAsal->nama_ruangan ?? 'Ruangan Asal';
        $rTujuan = $mutasi->ruanganTujuan->nama_ruangan ?? 'Ruangan Tujuan';

        // Audit Trail Logging
        ActivityLog::record(
            'Pindah Ruangan Alkes',
            "Memindahkan lokasi fisik unit '{$alkes->nama_barang}' ({$alkes->kode_inventaris}) dari {$rAsal} ke {$rTujuan}.",
            $rTujuan
        );

        return redirect()->route('mutasi.index')
            ->with('success', "Proses pemindahan lokasi unit alkes ke {$rTujuan} berhasil!");
    }
}
