<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Alkes;
use App\Models\MutasiAlkes;
use App\Models\Ruangan;
use App\Models\Seksi;
use Illuminate\Http\Request;

class MutasiAlkesController extends Controller
{
    private function getUserSeksiId()
    {
        return session('user_seksi_id', 6);
    }

    public function index()
    {
        $mutasiList = MutasiAlkes::with(['alkes.nomenklatur', 'seksiAsal', 'seksiTujuan', 'ruanganAsal', 'ruanganTujuan'])
            ->latest()
            ->paginate(30);

        return view('mutasi.index', compact('mutasiList'));
    }

    public function create(Request $request)
    {
        $isAdmin = session('is_admin', false);
        $userSeksiId = $this->getUserSeksiId();
        $userSeksi = Seksi::find($userSeksiId);

        $selectedAlkesId = $request->query('alkes_id');

        // Menampilkan alkes milik seksi user (atau lokasi seksi user saat ini)
        $alkesQuery = Alkes::with(['nomenklatur', 'seksiPemilik', 'lokasiSeksi', 'ruangan']);
        if (!$isAdmin) {
            $alkesQuery->where(function ($q) use ($userSeksiId) {
                $q->where('seksi_pemilik_id', $userSeksiId)
                  ->orWhere('lokasi_seksi_id', $userSeksiId);
            });
        }

        if ($selectedAlkesId) {
            $targetAlkes = Alkes::find($selectedAlkesId);
            if ($targetAlkes && !$isAdmin && $targetAlkes->seksi_pemilik_id != $userSeksiId && $targetAlkes->lokasi_seksi_id != $userSeksiId) {
                abort(403, 'Akses Ditolak: Anda hanya berhak memindahkan alat kesehatan milik atau yang sedang berada di Seksi Anda!');
            }
        }

        $alkesList = $alkesQuery->get();
        $seksiList = Seksi::where('id', '!=', $userSeksiId)->get();
        $ruanganList = Ruangan::all();

        return view('mutasi.create', compact('alkesList', 'seksiList', 'ruanganList', 'selectedAlkesId', 'userSeksi', 'userSeksiId', 'isAdmin'));
    }

    public function store(Request $request)
    {
        $isAdmin = session('is_admin', false);
        $userSeksiId = $this->getUserSeksiId();

        $validated = $request->validate([
            'alkes_id' => 'required|exists:alkes,id',
            'seksi_tujuan_id' => 'required|exists:seksi,id',
            'ruangan_tujuan_id' => 'nullable|exists:ruangan,id',
            'alasan_mutasi' => 'required|string',
            'pemohon' => 'nullable|string',
            'penanggung_jawab' => 'nullable|string',
        ]);

        $alkes = Alkes::with(['nomenklatur', 'seksiPemilik', 'lokasiSeksi'])->findOrFail($validated['alkes_id']);

        if (!$isAdmin && $alkes->seksi_pemilik_id != $userSeksiId && $alkes->lokasi_seksi_id != $userSeksiId) {
            abort(403, 'Akses Ditolak: Anda tidak dapat memindahkan barang dari Seksi lain!');
        }

        $seksiAsalId = $alkes->lokasi_seksi_id;
        $ruanganAsalId = $alkes->ruangan_id;
        $seksiTujuan = Seksi::findOrFail($validated['seksi_tujuan_id']);

        MutasiAlkes::create([
            'alkes_id' => $alkes->id,
            'seksi_asal_id' => $seksiAsalId,
            'seksi_tujuan_id' => $validated['seksi_tujuan_id'],
            'ruangan_asal_id' => $ruanganAsalId,
            'ruangan_tujuan_id' => $validated['ruangan_tujuan_id'] ?? null,
            'tanggal_mutasi' => now(),
            'pemohon' => $validated['pemohon'] ?? session('user_role_name', 'Petugas Seksi'),
            'penanggung_jawab' => $validated['penanggung_jawab'] ?? 'Penanggung Jawab Seksi',
            'alasan_mutasi' => $validated['alasan_mutasi'],
            'status_persetujuan' => 'Disetujui',
        ]);

        // PERATURAN UTAMA: Kepemilikan (seksi_pemilik_id) TETAP PERMANEN! Hanya lokasi_seksi_id & ruangan_id yang berubah.
        $alkes->update([
            'lokasi_seksi_id' => $validated['seksi_tujuan_id'],
            'ruangan_id' => $validated['ruangan_tujuan_id'] ?? null,
        ]);

        // Automatic Audit Trail Logging
        ActivityLog::record('Pindah Lokasi Alkes', "Memindahkan lokasi fisik '{$alkes->nomenklatur->nama_alat}' ({$alkes->kode_inventaris}) ke {$seksiTujuan->nama_seksi}. Kepemilikan aset tetap di {$alkes->seksiPemilik->nama_seksi}.");

        return redirect()->route('alkes.index', ['seksi_id' => $alkes->seksi_pemilik_id])
            ->with('success', "Lokasi fisik alat kesehatan berhasil dipindahkan ke {$seksiTujuan->nama_seksi}! Kepemilikan aset tetap berada di Seksi Anda.");
    }
}
