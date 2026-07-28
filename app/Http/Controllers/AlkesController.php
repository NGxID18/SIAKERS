<?php

namespace App\Http\Controllers;

use App\Enums\KondisiAlkes;
use App\Enums\StatusAlkes;
use App\Models\Alkes;
use App\Models\Nomenklatur;
use App\Models\Ruangan;
use App\Models\Seksi;
use Illuminate\Http\Request;

class AlkesController extends Controller
{
    /**
     * Mendapatkan ID Seksi user aktif (default: 1 - Seksi Penunjang Medis).
     */
    private function getUserSeksiId()
    {
        return session('user_seksi_id', 1);
    }

    public function index(Request $request)
    {
        $query = Alkes::with(['nomenklatur', 'seksi', 'ruangan']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_inventaris', 'like', "%{$search}%")
                  ->orWhere('nomor_seri', 'like', "%{$search}%")
                  ->orWhere('merk', 'like', "%{$search}%")
                  ->orWhere('tipe', 'like', "%{$search}%")
                  ->orWhereHas('nomenklatur', function ($nq) use ($search) {
                      $nq->where('nama_alat', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('seksi_id') && $request->seksi_id != 0) {
            $query->where('seksi_id', $request->seksi_id);
        }

        if ($request->filled('ruangan_id')) {
            $query->where('ruangan_id', $request->ruangan_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        $alkesList = $query->latest()->paginate(10)->withQueryString();
        $seksiList = Seksi::all();

        // Menampilkan SELURUH lokasi ruangan di rumah sakit
        $ruanganList = Ruangan::with('seksi')->get();

        $statuses = StatusAlkes::cases();
        $kondisis = KondisiAlkes::cases();
        $userSeksiId = $this->getUserSeksiId();

        return view('alkes.index', compact('alkesList', 'seksiList', 'ruanganList', 'statuses', 'kondisis', 'userSeksiId'));
    }

    public function create()
    {
        $userSeksiId = $this->getUserSeksiId();
        $userSeksi = Seksi::findOrFail($userSeksiId);

        $nomenklaturList = Nomenklatur::all();
        $seksiList = Seksi::where('id', $userSeksiId)->get();
        $ruanganList = Ruangan::where('seksi_id', $userSeksiId)->get();
        $statuses = StatusAlkes::cases();
        $kondisis = KondisiAlkes::cases();

        return view('alkes.create', compact('nomenklaturList', 'seksiList', 'ruanganList', 'statuses', 'kondisis', 'userSeksi', 'userSeksiId'));
    }

    public function store(Request $request)
    {
        $userSeksiId = $this->getUserSeksiId();

        if ($request->seksi_id != $userSeksiId) {
            abort(403, 'Akses Ditolak: Anda hanya berhak menambahkan alat kesehatan ke Seksi Anda sendiri!');
        }

        $validated = $request->validate([
            'kode_inventaris' => 'required|unique:alkes,kode_inventaris',
            'nomor_seri' => 'nullable|string',
            'nomenklatur_id' => 'required|exists:nomenklatur,id',
            'merk' => 'nullable|string',
            'tipe' => 'nullable|string',
            'seksi_id' => 'required|exists:seksi,id',
            'ruangan_id' => 'nullable|exists:ruangan,id',
            'status' => 'required',
            'kondisi' => 'required',
            'tanggal_pengadaan' => 'nullable|date',
            'nilai_aset' => 'nullable|numeric',
            'catatan' => 'nullable|string',
        ]);

        Alkes::create($validated);

        return redirect()->route('alkes.index', ['seksi_id' => $userSeksiId])
            ->with('success', 'Data Alat Kesehatan berhasil ditambahkan ke Seksi Anda!');
    }

    public function show(Alkes $alkes)
    {
        $alkes->load(['nomenklatur', 'seksi', 'ruangan', 'mutasi.seksiAsal', 'mutasi.seksiTujuan', 'logPemeliharaan']);
        $userSeksiId = $this->getUserSeksiId();

        return view('alkes.show', compact('alkes', 'userSeksiId'));
    }

    public function edit(Alkes $alkes)
    {
        $userSeksiId = $this->getUserSeksiId();

        if ($alkes->seksi_id != $userSeksiId) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki hak akses untuk mengedit alat kesehatan milik seksi lain!');
        }

        $nomenklaturList = Nomenklatur::all();
        $seksiList = Seksi::where('id', $userSeksiId)->get();
        $ruanganList = Ruangan::where('seksi_id', $alkes->seksi_id)->get();
        $statuses = StatusAlkes::cases();
        $kondisis = KondisiAlkes::cases();

        return view('alkes.edit', compact('alkes', 'nomenklaturList', 'seksiList', 'ruanganList', 'statuses', 'kondisis', 'userSeksiId'));
    }

    public function update(Request $request, Alkes $alkes)
    {
        $userSeksiId = $this->getUserSeksiId();

        if ($alkes->seksi_id != $userSeksiId) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki hak akses untuk mengupdate alat kesehatan milik seksi lain!');
        }

        $validated = $request->validate([
            'kode_inventaris' => 'required|unique:alkes,kode_inventaris,' . $alkes->id,
            'nomor_seri' => 'nullable|string',
            'nomenklatur_id' => 'required|exists:nomenklatur,id',
            'merk' => 'nullable|string',
            'tipe' => 'nullable|string',
            'seksi_id' => 'required|exists:seksi,id',
            'ruangan_id' => 'nullable|exists:ruangan,id',
            'status' => 'required',
            'kondisi' => 'required',
            'tanggal_pengadaan' => 'nullable|date',
            'nilai_aset' => 'nullable|numeric',
            'catatan' => 'nullable|string',
        ]);

        $alkes->update($validated);

        return redirect()->route('alkes.show', $alkes->id)
            ->with('success', 'Data Alat Kesehatan berhasil diperbarui!');
    }

    public function destroy(Alkes $alkes)
    {
        $userSeksiId = $this->getUserSeksiId();

        if ($alkes->seksi_id != $userSeksiId) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki hak akses untuk menghapus alat kesehatan milik seksi lain!');
        }

        $alkes->delete();

        return redirect()->route('alkes.index', ['seksi_id' => $userSeksiId])
            ->with('success', 'Data Alat Kesehatan berhasil dihapus!');
    }
}
