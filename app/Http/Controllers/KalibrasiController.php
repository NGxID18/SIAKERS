<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Alkes;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KalibrasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Alkes::with(['ruangan', 'lokasiRuangan', 'nomenklatur']);

        // Search Filter
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('merk', 'like', "%{$search}%")
                  ->orWhere('tipe', 'like', "%{$search}%")
                  ->orWhere('nomor_seri', 'like', "%{$search}%")
                  ->orWhere('kode_inventaris', 'like', "%{$search}%");
            });
        }

        // Filter 1: Ruangan Pemilik
        if ($request->filled('ruangan_id')) {
            $query->where('ruangan_id', $request->ruangan_id);
        }

        // Filter 2: Status Kalibrasi (TERKALIBRASI, EXPIRED, BELUM)
        if ($request->filled('status_kalibrasi')) {
            $status = $request->status_kalibrasi;
            $today = now()->toDateString();

            if ($status === 'TERKALIBRASI') {
                $query->whereNotNull('tanggal_kalibrasi_terakhir')
                      ->where('tanggal_kalibrasi_berikutnya', '>=', $today);
            } elseif ($status === 'EXPIRED') {
                $query->whereNotNull('tanggal_kalibrasi_berikutnya')
                      ->where('tanggal_kalibrasi_berikutnya', '<', $today);
            } elseif ($status === 'BELUM') {
                $query->whereNull('tanggal_kalibrasi_terakhir');
            }
        }

        $alkesList = $query->orderByRaw('CASE WHEN tanggal_kalibrasi_berikutnya IS NULL THEN 1 ELSE 0 END')
                          ->orderBy('tanggal_kalibrasi_berikutnya', 'asc')
                          ->paginate(25)
                          ->withQueryString();

        $ruanganList = Ruangan::orderBy('nama_ruangan')->get();

        // Hitung Statistik Kalibrasi
        $totalAlkes = Alkes::count();
        $totalTerkalibrasi = Alkes::whereNotNull('tanggal_kalibrasi_terakhir')
            ->where('tanggal_kalibrasi_berikutnya', '>=', now()->toDateString())
            ->count();
        $totalExpired = Alkes::whereNotNull('tanggal_kalibrasi_berikutnya')
            ->where('tanggal_kalibrasi_berikutnya', '<', now()->toDateString())
            ->count();
        $totalBelum = Alkes::whereNull('tanggal_kalibrasi_terakhir')->count();

        return view('kalibrasi.index', compact(
            'alkesList',
            'ruanganList',
            'totalAlkes',
            'totalTerkalibrasi',
            'totalExpired',
            'totalBelum'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_kalibrasi_terakhir' => 'required|date',
            'tanggal_kalibrasi_berikutnya' => 'required|date|after_or_equal:tanggal_kalibrasi_terakhir',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $alkes = Alkes::with('ruangan')->findOrFail($id);

        $tglTerakhir = $request->tanggal_kalibrasi_terakhir;
        $tglBerikutnya = $request->tanggal_kalibrasi_berikutnya;

        $alkes->update([
            'tanggal_kalibrasi_terakhir' => $tglTerakhir,
            'tanggal_kalibrasi_berikutnya' => $tglBerikutnya,
        ]);

        if ($request->filled('keterangan')) {
            $catatanLama = $alkes->keterangan ? $alkes->keterangan . ' | ' : '';
            $alkes->update([
                'keterangan' => $catatanLama . '[Kalibrasi ' . Carbon::parse($tglTerakhir)->format('d/m/Y') . ']: ' . $request->keterangan,
            ]);
        }

        ActivityLog::record(
            'Pembaruan Kalibrasi',
            "Memperbarui sertifikat & jadwal kalibrasi alat '{$alkes->nama_barang}' (SN: " . ($alkes->nomor_seri ?: '-') . "). Tanggal Kalibrasi Terakhir: " . Carbon::parse($tglTerakhir)->format('d/m/Y') . ", Jadwal Ulang: " . Carbon::parse($tglBerikutnya)->format('d/m/Y') . '.',
            session('user_role_label', 'Instalasi Elektromedis')
        );

        return redirect()->back()->with('success', "Sertifikat & Jadwal Kalibrasi untuk unit '{$alkes->nama_barang}' berhasil diperbarui!");
    }
}
