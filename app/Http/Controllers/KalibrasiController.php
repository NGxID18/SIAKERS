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

        if ($request->filled('ruangan_id')) {
            $query->where('ruangan_id', $request->ruangan_id);
        }

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
        if (session('user_role') !== 'elektromedis') {
            return redirect()->back()->with('error', 'Akses Ditolak! Hanya peran Elektromedis yang memiliki otoritas memperbarui data & sertifikat kalibrasi.');
        }

        $request->validate([
            'tanggal_kalibrasi_terakhir' => 'required|date',
            'tanggal_kalibrasi_berikutnya' => 'required|date|after_or_equal:tanggal_kalibrasi_terakhir',
            'sertifikat_pdf' => 'nullable|file|mimes:pdf|max:10240', // Max 10MB PDF
            'keterangan' => 'nullable|string|max:500',
        ]);

        $alkes = Alkes::with('ruangan')->findOrFail($id);

        $tglTerakhir = $request->tanggal_kalibrasi_terakhir;
        $tglBerikutnya = $request->tanggal_kalibrasi_berikutnya;

        $updateData = [
            'tanggal_kalibrasi_terakhir' => $tglTerakhir,
            'tanggal_kalibrasi_berikutnya' => $tglBerikutnya,
        ];

        if ($request->hasFile('sertifikat_pdf')) {
            $file = $request->file('sertifikat_pdf');
            $uploadDir = public_path('uploads/sertifikat');
            if (!file_exists($uploadDir)) {
                @mkdir($uploadDir, 0777, true);
            }
            $filename = 'sertifikat_' . $alkes->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $updateData['sertifikat_kalibrasi'] = '/uploads/sertifikat/' . $filename;
        }

        $alkes->update($updateData);

        if ($request->filled('keterangan')) {
            $catatanLama = $alkes->keterangan ? $alkes->keterangan . ' | ' : '';
            $alkes->update([
                'keterangan' => $catatanLama . '[Kalibrasi ' . Carbon::parse($tglTerakhir)->format('d/m/Y') . ']: ' . $request->keterangan,
            ]);
        }

        ActivityLog::record(
            'Pembaruan Kalibrasi',
            "Memperbarui sertifikat PDF & jadwal kalibrasi alat '{$alkes->nama_barang}' (SN: " . ($alkes->nomor_seri ?: '-') . "). Tanggal Kalibrasi Terakhir: " . Carbon::parse($tglTerakhir)->format('d/m/Y') . ", Jadwal Ulang: " . Carbon::parse($tglBerikutnya)->format('d/m/Y') . '.',
            session('user_role_label', 'Instalasi Elektromedis')
        );

        return redirect()->back()->with('success', "Sertifikat PDF & Jadwal Kalibrasi untuk unit '{$alkes->nama_barang}' berhasil diperbarui!");
    }
}
