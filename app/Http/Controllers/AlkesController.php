<?php

namespace App\Http\Controllers;

use App\Enums\KondisiAlkes;
use App\Enums\StatusAlkes;
use App\Models\ActivityLog;
use App\Models\Alkes;
use App\Models\Nomenklatur;
use App\Models\Ruangan;
use App\Models\Seksi;
use Illuminate\Http\Request;

class AlkesController extends Controller
{
    private function getUserSeksiId()
    {
        return session('user_seksi_id', 1);
    }

    public function index(Request $request)
    {
        $query = Alkes::with(['nomenklatur', 'seksiPemilik', 'lokasiSeksi', 'ruangan']);

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

        // Filter berdasarkan Seksi Pemilik Permanen (Submenu Seksi)
        if ($request->filled('seksi_id') && $request->seksi_id != 0) {
            $query->where('seksi_pemilik_id', $request->seksi_id);
        }

        // Filter berdasarkan Lokasi Keberadaan Fisik Alat
        if ($request->filled('lokasi_seksi_id')) {
            $query->where('lokasi_seksi_id', $request->lokasi_seksi_id);
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

        $alkesList = $query->latest()->paginate(30)->withQueryString();
        $seksiList = Seksi::all();
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
            'catatan' => 'nullable|string',
        ]);

        $validated['seksi_pemilik_id'] = $userSeksiId;
        $validated['lokasi_seksi_id'] = $userSeksiId;

        $alkes = Alkes::create($validated);
        $alkes->load('nomenklatur');

        // Automatic Audit Trail Logging
        ActivityLog::record('Tambah Alkes', "Registrasi aset alkes baru '{$alkes->nomenklatur->nama_alat}' ({$alkes->kode_inventaris}).");

        return redirect()->route('alkes.index', ['seksi_id' => $userSeksiId])
            ->with('success', 'Data Alat Kesehatan berhasil ditambahkan ke Seksi Anda!');
    }

    public function show($id)
    {
        $alkes = Alkes::with(['nomenklatur', 'seksiPemilik', 'lokasiSeksi', 'ruangan', 'mutasi.seksiAsal', 'mutasi.seksiTujuan', 'logPemeliharaan'])->findOrFail($id);
        $userSeksiId = $this->getUserSeksiId();

        return view('alkes.show', compact('alkes', 'userSeksiId'));
    }

    public function edit($id)
    {
        $alkes = Alkes::with(['nomenklatur', 'seksiPemilik', 'lokasiSeksi', 'ruangan'])->findOrFail($id);
        $userSeksiId = $this->getUserSeksiId();

        if ($alkes->seksi_pemilik_id != $userSeksiId) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki hak akses untuk mengedit alat kesehatan milik seksi lain!');
        }

        $nomenklaturList = Nomenklatur::all();
        $seksiList = Seksi::where('id', $userSeksiId)->get();
        $ruanganList = Ruangan::where('seksi_id', $alkes->lokasi_seksi_id)->get();
        $statuses = StatusAlkes::cases();
        $kondisis = KondisiAlkes::cases();

        return view('alkes.edit', compact('alkes', 'nomenklaturList', 'seksiList', 'ruanganList', 'statuses', 'kondisis', 'userSeksiId'));
    }

    public function update(Request $request, $id)
    {
        $alkes = Alkes::findOrFail($id);
        $userSeksiId = $this->getUserSeksiId();

        if ($alkes->seksi_pemilik_id != $userSeksiId) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki hak akses untuk mengupdate alat kesehatan milik seksi lain!');
        }

        $validated = $request->validate([
            'kode_inventaris' => 'required|unique:alkes,kode_inventaris,' . $alkes->id,
            'nomor_seri' => 'nullable|string',
            'nomenklatur_id' => 'required|exists:nomenklatur,id',
            'merk' => 'nullable|string',
            'tipe' => 'nullable|string',
            'ruangan_id' => 'nullable|exists:ruangan,id',
            'status' => 'required',
            'kondisi' => 'required',
            'tanggal_pengadaan' => 'nullable|date',
            'catatan' => 'nullable|string',
        ]);

        $alkes->update($validated);
        $alkes->load('nomenklatur');

        // Automatic Audit Trail Logging
        ActivityLog::record('Edit Alkes', "Memperbarui informasi aset alkes '{$alkes->nomenklatur->nama_alat}' ({$alkes->kode_inventaris}).");

        return redirect()->route('alkes.show', $alkes->id)
            ->with('success', 'Data Alat Kesehatan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $alkes = Alkes::with('nomenklatur')->findOrFail($id);
        $userSeksiId = $this->getUserSeksiId();

        if ($alkes->seksi_pemilik_id != $userSeksiId) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki hak akses untuk menghapus alat kesehatan milik seksi lain!');
        }

        $namaAlat = $alkes->nomenklatur->nama_alat ?? 'Alkes';
        $kodeInv = $alkes->kode_inventaris;
        $alkes->delete();

        // Automatic Audit Trail Logging
        ActivityLog::record('Hapus Alkes', "Menghapus data aset alkes '{$namaAlat}' ({$kodeInv}).");

        return redirect()->route('alkes.index', ['seksi_id' => $userSeksiId])
            ->with('success', 'Data Alat Kesehatan berhasil dihapus!');
    }
}
