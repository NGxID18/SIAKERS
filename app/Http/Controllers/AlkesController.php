<?php

namespace App\Http\Controllers;

use App\Enums\KondisiAlkes;
use App\Enums\StatusAlkes;
use App\Models\ActivityLog;
use App\Models\Alkes;
use App\Models\Nomenklatur;
use App\Models\Ruangan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $items = Alkes::with(['ruangan', 'lokasiRuangan'])
            ->leftJoin('ruangan', 'alkes.ruangan_id', '=', 'ruangan.id')
            ->orderBy('ruangan.nama_ruangan', 'asc')
            ->orderBy('alkes.nama_barang', 'asc')
            ->select('alkes.*')
            ->get();

        $data = $items->map(function ($item, $index) {
            $lokasiNama = $item->lokasiRuangan->nama_ruangan ?? ($item->ruangan->nama_ruangan ?? '-');
            return [
                'id' => $item->id,
                'no' => $index + 1,
                'nama_barang' => $item->nama_barang,
                'merk' => $item->merk ?: '-',
                'tipe' => $item->tipe ?: '-',
                'seri_number' => $item->nomor_seri ?: '-',
                'tahun' => $item->tahun_pengadaan ?: '-',
                'ruang_pemilik' => $item->ruangan->nama_ruangan ?? '-',
                'lokasi_alkes' => $lokasiNama,
                'lokasi_fisik' => $lokasiNama,
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

    public function apiHandler(Request $request)
    {
        if ($request->isMethod('post') || $request->has('data')) {
            return $this->apiSync($request);
        }
        return $this->apiIndex();
    }

    public function apiSync(Request $request)
    {
        if ($request->isMethod('get') && !$request->has('data')) {
            return $this->apiIndex();
        }

        $secretKey = env('ZAPIN_API_KEY', 'zapin_secret_key_rsjko_2026');
        $authHeader = $request->header('X-ZAPIN-KEY') ?: $request->input('api_key');

        if ($authHeader && $authHeader !== $secretKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized: Kunci API ZAPIN tidak valid.'
            ], 401);
        }

        $items = $request->input('data', []);
        if (empty($items) || !is_array($items)) {
            return $this->apiIndex();
        }

        $updatedCount = 0;
        $createdCount = 0;
        $savedResults = [];

        DB::transaction(function () use ($items, &$updatedCount, &$createdCount, &$savedResults) {
            // Helper to get or create Ruangan
            $ruanganCache = [];
            $getRuanganId = function ($namaRuangan) use (&$ruanganCache) {
                $nameClean = trim($namaRuangan ?? '');
                if (empty($nameClean) || $nameClean === '-' || in_array(strtolower($nameClean), ['ruang pemilik', 'lokasi alkes', 'lokasi fisik', 'lokasi fisik saat ini', 'ruangan'])) {
                    $nameClean = 'CSSD';
                }
                $key = strtolower($nameClean);
                if (isset($ruanganCache[$key])) {
                    return $ruanganCache[$key];
                }

                $ruangan = Ruangan::whereRaw('LOWER(nama_ruangan) = ?', [$key])->first();
                if (!$ruangan) {
                    $kodeStr = 'R-' . strtoupper(substr(str_replace([' ', '/'], '-', $nameClean), 0, 10));
                    $ruangan = Ruangan::create([
                        'nama_ruangan' => $nameClean,
                        'kode_ruangan' => $kodeStr,
                    ]);
                }
                $ruanganCache[$key] = $ruangan->id;
                return $ruangan->id;
            };

            // Kondisi Mapping Helper
            $mapKondisi = function ($rawKondisi) {
                $clean = strtolower(trim($rawKondisi ?? ''));
                if (str_contains($clean, 'rusak berat')) return KondisiAlkes::RUSAK_BERAT->value;
                if (str_contains($clean, 'rusak ringan')) return KondisiAlkes::RUSAK_RINGAN->value;
                return KondisiAlkes::BAIK->value;
            };

            foreach ($items as $row) {
                $dbId = !empty($row['id']) ? (int) $row['id'] : null;
                $namaBarang = trim($row['nama_barang'] ?? '');
                if (empty($namaBarang) || strtolower($namaBarang) === 'nama barang' || str_starts_with(strtolower($namaBarang), 'nama barang') || str_contains(strtolower($namaBarang), 'ketik data') || str_contains(strtolower($namaBarang), 'form tambah')) {
                    continue;
                }

                $ruangPemilikId = $getRuanganId($row['ruang_pemilik'] ?? null);
                $lokasiVal = !empty($row['lokasi_alkes']) ? $row['lokasi_alkes'] : ($row['lokasi_fisik'] ?? null);
                $lokasiFisikId = !empty($lokasiVal) ? $getRuanganId($lokasiVal) : $ruangPemilikId;
                $kondisi = $mapKondisi($row['kondisi'] ?? 'Baik');
                $status = ($kondisi === KondisiAlkes::BAIK->value) ? StatusAlkes::TERSEDIA->value : StatusAlkes::DALAM_PERBAIKAN->value;

                $alkes = null;
                if ($dbId) {
                    $alkes = Alkes::find($dbId);
                }

                if (!$alkes && !empty($row['seri_number']) && $row['seri_number'] !== '-') {
                    $alkes = Alkes::where('nomor_seri', trim($row['seri_number']))->first();
                }

                if ($alkes) {
                    $newMerk = !empty($row['merk']) && $row['merk'] !== '-' ? trim($row['merk']) : $alkes->merk;
                    $newTipe = !empty($row['tipe']) && $row['tipe'] !== '-' ? trim($row['tipe']) : $alkes->tipe;
                    $newSN = !empty($row['seri_number']) && $row['seri_number'] !== '-' ? trim($row['seri_number']) : $alkes->nomor_seri;
                    $newTahun = !empty($row['tahun']) && $row['tahun'] !== '-' ? trim($row['tahun']) : $alkes->tahun_pengadaan;
                    $newKalibrasiStatus = !empty($row['status_kalibrasi']) && $row['status_kalibrasi'] !== '-' ? trim($row['status_kalibrasi']) : $alkes->status_kalibrasi;
                    $newKet = !empty($row['keterangan']) && $row['keterangan'] !== '-' ? trim($row['keterangan']) : $alkes->keterangan;
                    $currentKondisiVal = $alkes->kondisi instanceof \App\Enums\KondisiAlkes ? $alkes->kondisi->value : (string) $alkes->kondisi;

                    $isChanged = (
                        $alkes->nama_barang !== $namaBarang ||
                        $alkes->merk !== $newMerk ||
                        $alkes->tipe !== $newTipe ||
                        $alkes->nomor_seri !== $newSN ||
                        $alkes->tahun_pengadaan !== $newTahun ||
                        $alkes->ruangan_id != $ruangPemilikId ||
                        $alkes->lokasi_ruangan_id != $lokasiFisikId ||
                        $currentKondisiVal !== $kondisi ||
                        $alkes->status_kalibrasi !== $newKalibrasiStatus ||
                        $alkes->keterangan !== $newKet
                    );

                    if ($isChanged) {
                        $alkes->update([
                            'nama_barang' => $namaBarang,
                            'merk' => $newMerk,
                            'tipe' => $newTipe,
                            'nomor_seri' => $newSN,
                            'tahun_pengadaan' => $newTahun,
                            'ruangan_id' => $ruangPemilikId,
                            'lokasi_ruangan_id' => $lokasiFisikId,
                            'kondisi' => $kondisi,
                            'status' => $status,
                            'status_kalibrasi' => $newKalibrasiStatus,
                            'keterangan' => $newKet,
                        ]);

                        $updatedCount++;
                        $savedResults[] = [
                            'no' => $alkes->id,
                            'status' => 'updated',
                            'nama_barang' => $alkes->nama_barang,
                        ];
                    }
                } else {
                    // CREATE
                    $maxId = Alkes::max('id') ?? 0;
                    $kodeInventaris = 'ALT-2026-' . str_pad($maxId + 1, 4, '0', STR_PAD_LEFT);

                    $statusKalibrasi = !empty($row['status_kalibrasi']) && $row['status_kalibrasi'] !== '-' ? trim($row['status_kalibrasi']) : 'BELUM DIKALIBRASI';
                    $tglKalibrasi = null;
                    if (!empty($row['tanggal_kalibrasi_terakhir']) && $row['tanggal_kalibrasi_terakhir'] !== '-' && $row['tanggal_kalibrasi_terakhir'] !== 'Belum ada data') {
                        try {
                            $tglKalibrasi = \Carbon\Carbon::parse($row['tanggal_kalibrasi_terakhir'])->toDateString();
                        } catch (\Exception $e) {
                            $tglKalibrasi = null;
                        }
                    }

                    $newAlkes = Alkes::create([
                        'kode_inventaris' => $kodeInventaris,
                        'nama_barang' => $namaBarang,
                        'nomenklatur_id' => null,
                        'merk' => !empty($row['merk']) && $row['merk'] !== '-' ? trim($row['merk']) : null,
                        'tipe' => !empty($row['tipe']) && $row['tipe'] !== '-' ? trim($row['tipe']) : null,
                        'nomor_seri' => !empty($row['seri_number']) && $row['seri_number'] !== '-' ? trim($row['seri_number']) : null,
                        'tahun_pengadaan' => !empty($row['tahun']) && $row['tahun'] !== '-' ? trim($row['tahun']) : date('Y'),
                        'jumlah' => 1,
                        'ruangan_id' => $ruangPemilikId,
                        'lokasi_ruangan_id' => $lokasiFisikId,
                        'kondisi' => $kondisi,
                        'status' => $status,
                        'status_kalibrasi' => $statusKalibrasi,
                        'tanggal_kalibrasi_terakhir' => $tglKalibrasi,
                        'keterangan' => !empty($row['keterangan']) && $row['keterangan'] !== '-' ? trim($row['keterangan']) : null,
                    ]);

                    $createdCount++;
                    $savedResults[] = [
                        'no' => $newAlkes->id,
                        'status' => 'created',
                        'nama_barang' => $newAlkes->nama_barang,
                    ];
                }
            }

            // Catat HANYA 1 Log Ringkasan jika ada perubahan nyata
            if ($createdCount > 0 || $updatedCount > 0) {
                ActivityLog::record(
                    'Sinkronisasi Spreadsheet',
                    "Sinkronisasi Google Spreadsheet: {$createdCount} alkes baru ditambahkan, {$updatedCount} data diperbarui.",
                    'Google Sheets'
                );
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => "Sinkronisasi berhasil! {$createdCount} data baru ditambahkan, {$updatedCount} data diperbarui.",
            'created_count' => $createdCount,
            'updated_count' => $updatedCount,
            'results' => $savedResults,
        ]);
    }
}
