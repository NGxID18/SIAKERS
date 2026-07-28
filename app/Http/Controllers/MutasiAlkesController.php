<?php

namespace App\Http\Controllers;

use App\Models\Alkes;
use App\Models\MutasiAlkes;
use App\Models\Ruangan;
use App\Models\Seksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MutasiAlkesController extends Controller
{
    private function getUserSeksiId()
    {
        return session('user_seksi_id', 6); // Default 6: Gudang Pusat Alkes & ATEM
    }

    public function index()
    {
        $mutasiList = MutasiAlkes::with(['alkes.nomenklatur', 'seksiAsal', 'seksiTujuan', 'ruanganAsal', 'ruanganTujuan'])
            ->latest()
            ->paginate(15);

        return view('mutasi.index', compact('mutasiList'));
    }

    public function create(Request $request)
    {
        $isAdmin = session('is_admin', false);
        $userSeksiId = $this->getUserSeksiId();
        $userSeksi = Seksi::find($userSeksiId);

        $selectedAlkesId = $request->query('alkes_id');

        // Query Alkes: Jika bukan Admin, HANYA tampilkan Alkes yang ada di Seksi pengirim saat ini
        $alkesQuery = Alkes::with(['nomenklatur', 'seksi', 'ruangan']);
        if (!$isAdmin) {
            $alkesQuery->where('seksi_id', $userSeksiId);
        }

        // Cek jika alkes_id dispesifikasikan di URL
        if ($selectedAlkesId) {
            $targetAlkes = Alkes::find($selectedAlkesId);
            if ($targetAlkes && !$isAdmin && $targetAlkes->seksi_id != $userSeksiId) {
                abort(403, 'Akses Ditolak: Anda hanya berhak melakukan mutasi alat kesehatan yang ada di Seksi/Gudang Anda sendiri!');
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
            'pemohon' => 'required|string',
            'penanggung_jawab' => 'required|string',
            'alasan_mutasi' => 'required|string',
        ]);

        $alkes = Alkes::findOrFail($validated['alkes_id']);

        // Proteksi Backend: Cek kepemilikan alat
        if (!$isAdmin && $alkes->seksi_id != $userSeksiId) {
            abort(403, 'Akses Ditolak: Anda tidak berhak mengirimkan alat kesehatan milik seksi lain!');
        }

        $seksiTujuan = Seksi::find($validated['seksi_tujuan_id']);

        DB::transaction(function () use ($validated, $alkes) {
            MutasiAlkes::create([
                'alkes_id' => $alkes->id,
                'seksi_asal_id' => $alkes->seksi_id,
                'ruangan_asal_id' => $alkes->ruangan_id,
                'seksi_tujuan_id' => $validated['seksi_tujuan_id'],
                'ruangan_tujuan_id' => $validated['ruangan_tujuan_id'],
                'tanggal_mutasi' => now(),
                'pemohon' => $validated['pemohon'],
                'penanggung_jawab' => $validated['penanggung_jawab'],
                'alasan_mutasi' => $validated['alasan_mutasi'],
                'status_persetujuan' => 'Disetujui',
            ]);

            // Update lokasi alkes ke seksi & ruangan tujuan
            $alkes->update([
                'seksi_id' => $validated['seksi_tujuan_id'],
                'ruangan_id' => $validated['ruangan_tujuan_id'],
            ]);
        });

        return redirect()->route('alkes.index', ['seksi_id' => $userSeksiId])
            ->with('success', "Alat kesehatan {$alkes->kode_inventaris} berhasil dimutasi & dikirim ke {$seksiTujuan->nama_seksi}!");
    }
}
