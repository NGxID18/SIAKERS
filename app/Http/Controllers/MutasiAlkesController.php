<?php

namespace App\Http\Controllers;

use App\Models\Alkes;
use App\Models\MutasiAlkes;
use App\Models\Ruangan;
use App\Models\Seksi;
use Illuminate\Http\Request;

class MutasiAlkesController extends Controller
{
    /**
     * Mendapatkan ID Seksi user aktif.
     */
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

        $alkesQuery = Alkes::with(['nomenklatur', 'seksi', 'ruangan']);
        if (!$isAdmin) {
            $alkesQuery->where('seksi_id', $userSeksiId);
        }

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
            'alasan_mutasi' => 'required|string',
            'pemohon' => 'nullable|string',
            'penanggung_jawab' => 'nullable|string',
        ]);

        $alkes = Alkes::findOrFail($validated['alkes_id']);

        if (!$isAdmin && $alkes->seksi_id != $userSeksiId) {
            abort(403, 'Akses Ditolak: Anda tidak dapat memutasi barang dari Seksi lain!');
        }

        $seksiAsalId = $alkes->seksi_id;
        $ruanganAsalId = $alkes->ruangan_id;

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

        $alkes->update([
            'seksi_id' => $validated['seksi_tujuan_id'],
            'ruangan_id' => $validated['ruangan_tujuan_id'] ?? null,
        ]);

        return redirect()->route('alkes.index', ['seksi_id' => $userSeksiId])
            ->with('success', 'Mutasi Alat Kesehatan berhasil diproses! Unit telah dipindahkan ke Seksi tujuan.');
    }
}
