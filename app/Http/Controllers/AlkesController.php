<?php

namespace App\Http\Controllers;

use App\Enums\KondisiAlkes;
use App\Enums\StatusAlkes;
use App\Models\ActivityLog;
use App\Models\Alkes;
use App\Models\Nomenklatur;
use App\Models\Ruangan;
use Illuminate\Http\Request;

class AlkesController extends Controller
{
    public function index(Request $request)
    {
        $query = Alkes::with(['nomenklatur', 'ruangan', 'lokasiRuangan']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('merk', 'like', "%{$search}%")
                  ->orWhere('tipe', 'like', "%{$search}%")
                  ->orWhere('nomor_seri', 'like', "%{$search}%")
                  ->orWhere('tahun_pengadaan', 'like', "%{$search}%")
                  ->orWhere('jumlah', 'like', "%{$search}%")
                  ->orWhere('lokasi_saat_ini_note', 'like', "%{$search}%")
                  ->orWhere('kondisi', 'like', "%{$search}%")
                  ->orWhere('aspak_status', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhereHas('ruangan', function ($rq) use ($search) {
                      $rq->where('nama_ruangan', 'like', "%{$search}%")
                        ->orWhere('kode_ruangan', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('ruangan_id')) {
            $query->where('ruangan_id', $request->ruangan_id);
        }

        if ($request->filled('lokasi_ruangan_id')) {
            $query->where('lokasi_ruangan_id', $request->lokasi_ruangan_id);
        }

        if ($request->filled('kondisi')) {
            $val = trim($request->kondisi);
            $query->where(function ($q) use ($val) {
                $q->where('kondisi', $val)
                  ->orWhere('kondisi', strtolower($val))
                  ->orWhere('kondisi', strtoupper($val));
            });
        }

        $sortBy = $request->input('sort_by', 'nama_barang');
        $sortDir = strtolower($request->input('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        $allowedSorts = [
            'nama_barang' => 'nama_barang',
            'merk' => 'merk',
            'tipe' => 'tipe',
            'nomor_seri' => 'nomor_seri',
            'tahun_pengadaan' => 'tahun_pengadaan',
            'jumlah' => 'jumlah',
            'kondisi' => 'kondisi',
            'aspak_status' => 'aspak_status',
            'kib_status' => 'kib_status',
            'created_at' => 'created_at',
        ];

        if (array_key_exists($sortBy, $allowedSorts)) {
            $query->orderBy($allowedSorts[$sortBy], $sortDir);
        } elseif ($sortBy === 'ruangan') {
            $query->join('ruangan', 'alkes.ruangan_id', '=', 'ruangan.id')
                  ->orderBy('ruangan.nama_ruangan', $sortDir)
                  ->select('alkes.*');
        } else {
            $query->orderBy('nama_barang', 'asc');
        }

        $perPage = $request->per_page === 'all' ? 10000 : (int) $request->get('per_page', 50);
        $alkesList = $query->paginate($perPage)->withQueryString();
        $ruanganList = Ruangan::orderBy('nama_ruangan', 'asc')->get();

        $statuses = StatusAlkes::cases();
        $kondisis = KondisiAlkes::cases();

        return view('alkes.index', compact('alkesList', 'ruanganList', 'statuses', 'kondisis', 'sortBy', 'sortDir'));
    }

    public function create()
    {
        if (session('user_role') !== 'elektromedis') {
            return redirect()->route('alkes.index')
                ->with('error', 'Akses Ditolak: Hanya Ruangan Elektromedis (Admin) yang berwenang menambah data inventaris alkes.');
        }

        $nomenklaturList = Nomenklatur::all();
        $ruanganList = Ruangan::orderBy('nama_ruangan', 'asc')->get();
        $statuses = StatusAlkes::cases();
        $kondisis = KondisiAlkes::cases();

        return view('alkes.create', compact('nomenklaturList', 'ruanganList', 'statuses', 'kondisis'));
    }

    public function store(Request $request)
    {
        if (session('user_role') !== 'elektromedis') {
            return redirect()->route('alkes.index')
                ->with('error', 'Akses Ditolak: Hanya Ruangan Elektromedis (Admin) yang berwenang menambah data inventaris alkes.');
        }

        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kode_inventaris' => 'nullable|string',
            'nomor_seri' => 'nullable|string',
            'nomenklatur_id' => 'nullable|exists:nomenklatur,id',
            'merk' => 'nullable|string',
            'tipe' => 'nullable|string',
            'tahun_pengadaan' => 'nullable|string',
            'jumlah' => 'nullable|integer|min:1',
            'ruangan_id' => 'required|exists:ruangan,id',
            'status' => 'required',
            'kondisi' => 'required',
            'aspak_status' => 'nullable|string',
            'kib_status' => 'nullable|boolean',
            'keterangan' => 'nullable|string',
        ]);

        if (empty($validated['kode_inventaris'])) {
            $maxId = Alkes::max('id') ?? 0;
            $validated['kode_inventaris'] = 'ALT-' . str_pad($maxId + 1, 4, '0', STR_PAD_LEFT);
        }

        $validated['lokasi_ruangan_id'] = $validated['ruangan_id'];

        $alkes = Alkes::create($validated);
        $ruang = Ruangan::find($validated['ruangan_id']);

        ActivityLog::record('Tambah Alkes', "Registrasi aset alkes baru '{$alkes->nama_barang}'.", $ruang->nama_ruangan ?? 'Pusat');

        return redirect()->route('alkes.index', ['ruangan_id' => $alkes->ruangan_id])
            ->with('success', 'Data Alat Kesehatan berhasil ditambahkan!');
    }

    public function show($id)
    {
        $alkes = Alkes::with(['nomenklatur', 'ruangan', 'lokasiRuangan', 'mutasi.ruanganAsal', 'mutasi.ruanganTujuan', 'logPemeliharaan'])->findOrFail($id);

        return view('alkes.show', compact('alkes'));
    }

    public function edit($id)
    {
        if (session('user_role') !== 'elektromedis') {
            return redirect()->route('alkes.index')
                ->with('error', 'Akses Ditolak: Hanya Ruangan Elektromedis (Admin) yang berwenang merubah data inventaris alkes.');
        }

        $alkes = Alkes::with(['nomenklatur', 'ruangan', 'lokasiRuangan'])->findOrFail($id);

        $nomenklaturList = Nomenklatur::all();
        $ruanganList = Ruangan::orderBy('nama_ruangan', 'asc')->get();
        $statuses = StatusAlkes::cases();
        $kondisis = KondisiAlkes::cases();

        return view('alkes.edit', compact('alkes', 'nomenklaturList', 'ruanganList', 'statuses', 'kondisis'));
    }

    public function update(Request $request, $id)
    {
        if (session('user_role') !== 'elektromedis') {
            return redirect()->route('alkes.index')
                ->with('error', 'Akses Ditolak: Hanya Ruangan Elektromedis (Admin) yang berwenang merubah data inventaris alkes.');
        }

        $alkes = Alkes::findOrFail($id);

        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kode_inventaris' => 'nullable|string',
            'nomor_seri' => 'nullable|string',
            'nomenklatur_id' => 'nullable|exists:nomenklatur,id',
            'merk' => 'nullable|string',
            'tipe' => 'nullable|string',
            'tahun_pengadaan' => 'nullable|string',
            'jumlah' => 'nullable|integer|min:1',
            'ruangan_id' => 'required|exists:ruangan,id',
            'status' => 'required',
            'kondisi' => 'required',
            'aspak_status' => 'nullable|string',
            'kib_status' => 'nullable|boolean',
            'keterangan' => 'nullable|string',
        ]);

        $alkes->update($validated);

        ActivityLog::record('Edit Alkes', "Memperbarui informasi aset alkes '{$alkes->nama_barang}'.", $alkes->ruangan->nama_ruangan ?? 'Pusat');

        return redirect()->route('alkes.show', $alkes->id)
            ->with('success', 'Data Alat Kesehatan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        if (session('user_role') !== 'elektromedis') {
            return redirect()->route('alkes.index')
                ->with('error', 'Akses Ditolak: Hanya Ruangan Elektromedis (Admin) yang berwenang menghapus data inventaris alkes.');
        }

        $alkes = Alkes::findOrFail($id);

        $namaAlat = $alkes->nama_barang;
        $alkes->delete();

        ActivityLog::record('Hapus Alkes', "Menghapus data aset alkes '{$namaAlat}'.");

        return redirect()->route('alkes.index')
            ->with('success', 'Data Alat Kesehatan berhasil dihapus!');
    }

    public function apiIndex()
    {
        $items = Alkes::with(['ruangan', 'lokasiRuangan'])->orderBy('nama_barang', 'asc')->get();

        $data = $items->map(function ($item, $index) {
            return [
                'no' => $index + 1,
                'nama_barang' => $item->nama_barang,
                'merk' => $item->merk ?: '-',
                'tipe' => $item->tipe ?: '-',
                'seri_number' => $item->nomor_seri ?: '-',
                'tahun' => $item->tahun_pengadaan ?: '-',
                'ruang_pemilik' => $item->ruangan->nama_ruangan ?? '-',
                'lokasi_fisik' => $item->lokasiRuangan->nama_ruangan ?? '-',
                'kondisi' => $item->kondisi_enum->label(),
                'status_kalibrasi' => $item->status_kalibrasi ?: 'BELUM DIKALIBRASI',
                'tanggal_kalibrasi_terakhir' => $item->tanggal_kalibrasi_terakhir ? $item->tanggal_kalibrasi_terakhir->format('Y-m-d') : 'Belum ada data',
                'keterangan' => $item->keterangan ?: '-'
            ];
        });

        return response()->json([
            'status' => 'success',
            'total' => count($data),
            'updated_at' => now()->toDateTimeString(),
            'data' => $data
        ]);
    }
}
