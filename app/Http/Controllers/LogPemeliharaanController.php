<?php

namespace App\Http\Controllers;

use App\Enums\KondisiAlkes;
use App\Enums\StatusAlkes;
use App\Models\ActivityLog;
use App\Models\Alkes;
use App\Models\LogPemeliharaan;
use App\Models\MutasiAlkes;
use App\Models\Notification;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogPemeliharaanController extends Controller
{
    public function index(Request $request)
    {
        $query = LogPemeliharaan::with(['alkes.ruangan', 'alkes.lokasiRuangan']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('deskripsi_kerusakan', 'like', "%{$search}%")
                  ->orWhere('tindakan_perbaikan', 'like', "%{$search}%")
                  ->orWhere('pelaksana_vendor', 'like', "%{$search}%")
                  ->orWhereHas('alkes', function ($aq) use ($search) {
                      $aq->where('nama_barang', 'like', "%{$search}%")
                         ->orWhere('nomor_seri', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('jenis_tindakan')) {
            $query->where('jenis_tindakan', $request->jenis_tindakan);
        }

        if ($request->filled('status_hasil')) {
            $query->where('status_hasil', $request->status_hasil);
        }

        $logList = $query->latest()->paginate(30)->withQueryString();

        $notifications = Notification::with(['alkes', 'ruanganAsal'])
            ->latest()
            ->take(10)
            ->get();

        $unreadCount = Notification::where('dibaca', false)->count();

        return view('pemeliharaan.index', compact('logList', 'notifications', 'unreadCount'));
    }

    public function create(Request $request)
    {
        $selectedAlkesId = $request->query('alkes_id');
        $alkesList = Alkes::with(['ruangan', 'lokasiRuangan'])->get();

        return view('pemeliharaan.create', compact('alkesList', 'selectedAlkesId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'alkes_id' => 'required|exists:alkes,id',
            'jenis_tindakan' => 'required|string',
            'tanggal_lapor' => 'nullable|string',
            'tanggal_mulai' => 'nullable|string',
            'gejala_kerusakan' => 'nullable|string',
            'deskripsi_kerusakan' => 'nullable|string',
            'pelaksana_vendor' => 'nullable|string',
            'tindakan_perbaikan' => 'nullable|string',
            'biaya' => 'nullable|numeric',
        ]);

        $tglMulai = $validated['tanggal_lapor'] ?? $validated['tanggal_mulai'] ?? now()->toDateString();
        $deskripsi = $validated['gejala_kerusakan'] ?? $validated['deskripsi_kerusakan'] ?? 'Laporan kerusakan unit alkes.';

        DB::transaction(function () use ($validated, $tglMulai, $deskripsi) {
            $alkes = Alkes::with(['ruangan', 'lokasiRuangan'])->findOrFail($validated['alkes_id']);
            $elektromedisRuang = Ruangan::where('nama_ruangan', 'Elektromedis')->first();

            $ruanganAsalFisikId = $alkes->lokasi_ruangan_id ?: $alkes->ruangan_id;
            $ruanganTujuanId = $elektromedisRuang ? $elektromedisRuang->id : $ruanganAsalFisikId;

            $log = LogPemeliharaan::create([
                'alkes_id' => $alkes->id,
                'jenis_tindakan' => $validated['jenis_tindakan'],
                'tanggal_mulai' => substr($tglMulai, 0, 10),
                'pelaksana_vendor' => $validated['pelaksana_vendor'] ?? 'Teknisi Elektromedis RS',
                'deskripsi_kerusakan' => $deskripsi,
                'tindakan_perbaikan' => $validated['tindakan_perbaikan'] ?? 'Dalam Proses Penanganan Elektromedis',
                'biaya' => $validated['biaya'] ?? 0,
                'status_hasil' => 'Proses',
            ]);

            MutasiAlkes::create([
                'alkes_id' => $alkes->id,
                'ruangan_asal_id' => $ruanganAsalFisikId,
                'ruangan_tujuan_id' => $ruanganTujuanId,
                'tanggal_mutasi' => now(),
                'pemohon' => session('user_role_label', 'Petugas Ruangan'),
                'penanggung_jawab' => 'Petugas Ruangan & ATEM Elektromedis',
                'alasan_mutasi' => 'Pengajuan ' . $validated['jenis_tindakan'] . ' - Unit Dipindahkan ke Ruangan Elektromedis',
                'status_persetujuan' => 'Disetujui',
            ]);

            $alkes->update([
                'status' => StatusAlkes::DALAM_PERBAIKAN->value,
                'kondisi' => KondisiAlkes::RUSAK_BERAT->value,
                'lokasi_ruangan_id' => $ruanganTujuanId,
                'lokasi_saat_ini_note' => 'Di Ruangan Elektromedis (Dalam Perbaikan)',
            ]);

            Notification::create([
                'alkes_id' => $alkes->id,
                'ruangan_asal_id' => $alkes->ruangan_id,
                'judul' => 'Laporan Kerusakan Masuk dari Ruang ' . ($alkes->ruangan->nama_ruangan ?? 'RS'),
                'pesan' => "Unit {$alkes->nama_barang} (SN: " . ($alkes->nomor_seri ?? '-') . ") dikirim dari Ruang " . ($alkes->ruangan->nama_ruangan ?? 'RS') . " ke Elektromedis untuk " . $validated['jenis_tindakan'] . '.',
                'tipe' => 'laporan_kerusakan',
            ]);

            ActivityLog::record(
                'Lapor Perbaikan',
                "Melaporkan kerusakan '{$alkes->nama_barang}'. Lokasi fisik unit otomatis dipindahkan ke Ruangan Elektromedis dan dicatat pada log mutasi.",
                $alkes->ruangan->nama_ruangan ?? 'RS'
            );
        });

        return redirect()->route('pemeliharaan.index')->with('success', 'Laporan kerusakan berhasil dikirim! Mutasi fisik unit ke Ruangan Elektromedis telah otomatis tercatat pada Riwayat Mutasi Alkes.');
    }

    public function resolve(Request $request, $id)
    {
        DB::transaction(function () use ($id, $request) {
            $log = LogPemeliharaan::findOrFail($id);
            $alkes = Alkes::with(['ruangan', 'lokasiRuangan'])->findOrFail($log->alkes_id);
            $elektromedisRuang = Ruangan::where('nama_ruangan', 'Elektromedis')->first();

            $ruanganAsalElektroId = $elektromedisRuang ? $elektromedisRuang->id : $alkes->lokasi_ruangan_id;

            $log->update([
                'status_hasil' => 'Selesai',
                'tanggal_selesai' => now(),
                'tindakan_perbaikan' => $request->input('tindakan_perbaikan', $log->tindakan_perbaikan ?: 'Perbaikan dan kalibrasi selesai oleh Elektromedis.'),
            ]);

            MutasiAlkes::create([
                'alkes_id' => $alkes->id,
                'ruangan_asal_id' => $ruanganAsalElektroId,
                'ruangan_tujuan_id' => $alkes->ruangan_id,
                'tanggal_mutasi' => now(),
                'pemohon' => 'Ruangan Elektromedis (Admin)',
                'penanggung_jawab' => 'Teknisi Elektromedis RS',
                'alasan_mutasi' => 'Perbaikan & Kalibrasi Selesai - Unit Dikembalikan ke Ruangan Asal',
                'status_persetujuan' => 'Disetujui',
            ]);

            $alkes->update([
                'status' => StatusAlkes::TERSEDIA->value,
                'kondisi' => KondisiAlkes::BAIK->value,
                'lokasi_ruangan_id' => $alkes->ruangan_id,
                'lokasi_saat_ini_note' => null,
            ]);

            Notification::create([
                'alkes_id' => $alkes->id,
                'ruangan_asal_id' => $alkes->ruangan_id,
                'judul' => 'Perbaikan Selesai & Unit Dikembalikan ke ' . ($alkes->ruangan->nama_ruangan ?? 'Ruangan'),
                'pesan' => "Unit {$alkes->nama_barang} telah selesai diperbaiki/dikalibrasi oleh Elektromedis dan dikirim kembali ke Ruang " . ($alkes->ruangan->nama_ruangan ?? 'Asal') . '.',
                'tipe' => 'perbaikan_selesai',
            ]);

            ActivityLog::record(
                'Perbaikan Selesai',
                "Elektromedis telah menyelesaikan perbaikan '{$alkes->nama_barang}' dan mengembalikan unit ke Ruang " . ($alkes->ruangan->nama_ruangan ?? 'Asal') . '.',
                'Elektromedis'
            );
        });

        return redirect()->route('pemeliharaan.index')->with('success', 'Perbaikan berhasil diselesaikan! Mutasi pengembalian unit alkes ke ruangan asal telah tercatat pada Riwayat Mutasi Alkes.');
    }

    public function markNotificationsRead()
    {
        Notification::where('dibaca', false)->update(['dibaca' => true]);
        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }
}
