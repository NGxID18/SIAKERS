<?php

namespace App\Http\Controllers;

use App\Enums\KondisiAlkes;
use App\Enums\StatusAlkes;
use App\Models\ActivityLog;
use App\Models\Alkes;
use App\Models\LogPemeliharaan;
use App\Models\Notification;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogPemeliharaanController extends Controller
{
    public function index()
    {
        $logList = LogPemeliharaan::with(['alkes.ruangan', 'alkes.lokasiRuangan'])
            ->latest()
            ->paginate(30);

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
            'tanggal_mulai' => 'required|date',
            'pelaksana_vendor' => 'nullable|string',
            'deskripsi_kerusakan' => 'nullable|string',
            'tindakan_perbaikan' => 'nullable|string',
            'biaya' => 'nullable|numeric',
        ]);

        DB::transaction(function () use ($validated) {
            $alkes = Alkes::with(['ruangan', 'lokasiRuangan'])->findOrFail($validated['alkes_id']);
            $elektromedisRuang = Ruangan::where('nama_ruangan', 'Elektromedis')->first();

            // 1. Simpan Log Pemeliharaan
            $log = LogPemeliharaan::create([
                'alkes_id' => $alkes->id,
                'jenis_tindakan' => $validated['jenis_tindakan'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'pelaksana_vendor' => $validated['pelaksana_vendor'] ?? 'Teknisi Elektromedis RS',
                'deskripsi_kerusakan' => $validated['deskripsi_kerusakan'],
                'tindakan_perbaikan' => $validated['tindakan_perbaikan'] ?? 'Penanganan awal laporan perbaikan',
                'biaya' => $validated['biaya'] ?? 0,
                'status_hasil' => 'Proses',
            ]);

            // 2. OTOMATIS: Pindahkan Lokasi Fisik Alkes ke Elektromedis & ubah status
            $alkes->update([
                'status' => StatusAlkes::DALAM_PERBAIKAN->value,
                'kondisi' => KondisiAlkes::RUSAK_BERAT->value,
                'lokasi_ruangan_id' => $elektromedisRuang ? $elektromedisRuang->id : $alkes->lokasi_ruangan_id,
                'lokasi_saat_ini_note' => 'Di Ruangan Elektromedis (Dalam Perbaikan)',
            ]);

            // 3. Notifikasi ke Elektromedis
            Notification::create([
                'alkes_id' => $alkes->id,
                'ruangan_asal_id' => $alkes->ruangan_id,
                'judul' => 'Laporan Kerusakan Masuk dari Ruang ' . ($alkes->ruangan->nama_ruangan ?? 'RS'),
                'pesan' => "Unit {$alkes->nama_barang} (SN: " . ($alkes->nomor_seri ?? '-') . ") dikirim dari Ruang " . ($alkes->ruangan->nama_ruangan ?? 'RS') . " ke Elektromedis untuk " . $validated['jenis_tindakan'] . '.',
                'tipe' => 'laporan_kerusakan',
            ]);

            // 4. Audit Trail Log
            ActivityLog::record(
                'Lapor Perbaikan',
                "Melaporkan kerusakan '{$alkes->nama_barang}'. Lokasi fisik unit otomatis dipindahkan ke Ruangan Elektromedis.",
                $alkes->ruangan->nama_ruangan ?? 'RS'
            );
        });

        return redirect()->route('pemeliharaan.index')->with('success', 'Laporan kerusakan berhasil dikirim! Unit fisik alkes otomatis dipindahkan ke Ruangan Elektromedis.');
    }

    /**
     * OTORITAS ELEKTROMEDIS: Menyelesaikan perbaikan & mengembalikan unit ke ruangan asal.
     */
    public function resolve(Request $request, $id)
    {
        DB::transaction(function () use ($id, $request) {
            $log = LogPemeliharaan::findOrFail($id);
            $alkes = Alkes::with('ruangan')->findOrFail($log->alkes_id);

            // 1. Update Log Pemeliharaan
            $log->update([
                'status_hasil' => 'Selesai',
                'tanggal_selesai' => now(),
                'tindakan_perbaikan' => $request->input('tindakan_perbaikan', $log->tindakan_perbaikan ?: 'Perbaikan dan kalibrasi selesai oleh Elektromedis.'),
            ]);

            // 2. OTOMATIS ELEKTROMEDIS: Kembalikan lokasi fisik alkes ke ruangan asal
            $alkes->update([
                'status' => StatusAlkes::TERSEDIA->value,
                'kondisi' => KondisiAlkes::BAIK->value,
                'lokasi_ruangan_id' => $alkes->ruangan_id,
                'lokasi_saat_ini_note' => null,
            ]);

            // 3. Notifikasi Pengembalian
            Notification::create([
                'alkes_id' => $alkes->id,
                'ruangan_asal_id' => $alkes->ruangan_id,
                'judul' => 'Perbaikan Selesai & Unit Dikembalikan ke ' . ($alkes->ruangan->nama_ruangan ?? 'Ruangan'),
                'pesan' => "Unit {$alkes->nama_barang} telah selesai diperbaiki/dikalibrasi oleh Elektromedis dan dikirim kembali ke Ruang " . ($alkes->ruangan->nama_ruangan ?? 'Asal') . '.',
                'tipe' => 'perbaikan_selesai',
            ]);

            // 4. Audit Trail Log
            ActivityLog::record(
                'Perbaikan Selesai',
                "Elektromedis telah menyelesaikan perbaikan '{$alkes->nama_barang}' dan mengembalikan unit ke Ruang " . ($alkes->ruangan->nama_ruangan ?? 'Asal') . '.',
                'Elektromedis'
            );
        });

        return redirect()->route('pemeliharaan.index')->with('success', 'Perbaikan berhasil diselesaikan! Unit alkes telah dikembalikan ke ruangan asalnya.');
    }

    /**
     * Tandai semua notifikasi telah dibaca
     */
    public function markNotificationsRead()
    {
        Notification::where('dibaca', false)->update(['dibaca' => true]);
        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }
}
