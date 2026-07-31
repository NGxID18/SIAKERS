<?php

namespace App\Http\Controllers;

use App\Enums\KondisiAlkes;
use App\Enums\StatusAlkes;
use App\Models\ActivityLog;
use App\Models\Alkes;
use App\Models\LogPemeliharaan;
use App\Models\MutasiAlkes;
use App\Models\Ruangan;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalAlkes = Alkes::count();
        $alkesTersedia = Alkes::where('status', StatusAlkes::TERSEDIA->value)->count();
        $alkesDigunakan = Alkes::where('status', StatusAlkes::SEDANG_DIGUNAKAN->value)->count();
        $alkesRusak = Alkes::where('status', StatusAlkes::DALAM_PERBAIKAN->value)
            ->orWhere('kondisi', KondisiAlkes::RUSAK_BERAT->value)
            ->count();
        $alkesKalibrasi = Alkes::where('status', StatusAlkes::PROSES_KALIBRASI->value)->count();

        // Rekap per Ruangan RS Diurutkan Berdasarkan Abjad Nama Ruangan (A-Z)
        $ruanganList = Ruangan::withCount(['alkes', 'alkes as alkes_digunakan_count' => function ($q) {
            $q->where('status', StatusAlkes::SEDANG_DIGUNAKAN->value);
        }, 'alkes as alkes_rusak_count' => function ($q) {
            $q->where('status', StatusAlkes::DALAM_PERBAIKAN->value)
              ->orWhere('kondisi', KondisiAlkes::RUSAK_BERAT->value);
        }])->orderBy('nama_ruangan', 'asc')->get();

        // Mutasi / Pindah Ruangan Terbaru
        $mutasiTerbaru = MutasiAlkes::with(['alkes', 'ruanganAsal', 'ruanganTujuan'])
            ->latest()
            ->take(6)
            ->get();

        // Log Perbaikan Terbaru
        $logPerbaikanTerbaru = LogPemeliharaan::with(['alkes.ruangan'])
            ->latest()
            ->take(5)
            ->get();

        // Data Grafik Analytics Chart.js Status
        $chartStatusData = [
            'Tersedia' => $alkesTersedia,
            'Sedang Digunakan' => $alkesDigunakan,
            'Dalam Perbaikan' => $alkesRusak,
        ];

        // Grafik Kondisi per Ruangan RS (Diurutkan Abjad A-Z)
        $chartRuanganLabels = [];
        $chartKondisiBaik = [];
        $chartKondisiRusak = [];

        foreach ($ruanganList->take(12) as $ruang) {
            $chartRuanganLabels[] = $ruang->nama_ruangan;
            $chartKondisiBaik[] = Alkes::where('ruangan_id', $ruang->id)->where('kondisi', KondisiAlkes::BAIK->value)->count();
            $chartKondisiRusak[] = Alkes::where('ruangan_id', $ruang->id)->where('kondisi', '!=', KondisiAlkes::BAIK->value)->count();
        }

        // Grafik Sumber Perolehan (DAK, APBD, BLUD, HIBAH, Beli Sendiri)
        $perolehanData = DB::table('alkes')
            ->select('cara_perolehan', DB::raw('count(id) as total'))
            ->groupBy('cara_perolehan')
            ->orderBy('total', 'desc')
            ->get();

        $chartPerolehanLabels = [];
        $chartPerolehanCounts = [];
        foreach ($perolehanData as $p) {
            $chartPerolehanLabels[] = $p->cara_perolehan ?: 'Pengadaan RS';
            $chartPerolehanCounts[] = $p->total;
        }

        // Recent Audit Trail Activity Logs
        $recentActivityLogs = ActivityLog::latest()->take(6)->get();

        return view('dashboard', compact(
            'totalAlkes',
            'alkesTersedia',
            'alkesDigunakan',
            'alkesRusak',
            'alkesKalibrasi',
            'ruanganList',
            'mutasiTerbaru',
            'logPerbaikanTerbaru',
            'chartStatusData',
            'chartRuanganLabels',
            'chartKondisiBaik',
            'chartKondisiRusak',
            'chartPerolehanLabels',
            'chartPerolehanCounts',
            'recentActivityLogs'
        ));
    }
}
