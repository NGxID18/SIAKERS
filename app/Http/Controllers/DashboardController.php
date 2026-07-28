<?php

namespace App\Http\Controllers;

use App\Enums\KondisiAlkes;
use App\Enums\StatusAlkes;
use App\Models\ActivityLog;
use App\Models\Alkes;
use App\Models\LogPemeliharaan;
use App\Models\MutasiAlkes;
use App\Models\Nomenklatur;
use App\Models\Seksi;
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

        // Rekap per Seksi Pemilik
        $seksiList = Seksi::withCount(['alkes', 'alkes as alkes_digunakan_count' => function ($q) {
            $q->where('status', StatusAlkes::SEDANG_DIGUNAKAN->value);
        }, 'alkes as alkes_rusak_count' => function ($q) {
            $q->where('status', StatusAlkes::DALAM_PERBAIKAN->value);
        }])->get();

        // Mutasi Terbaru
        $mutasiTerbaru = MutasiAlkes::with(['alkes.nomenklatur', 'seksiAsal', 'seksiTujuan'])
            ->latest()
            ->take(5)
            ->get();

        // Log Perbaikan Terbaru
        $logPerbaikanTerbaru = LogPemeliharaan::with(['alkes.nomenklatur', 'alkes.seksiPemilik'])
            ->latest()
            ->take(5)
            ->get();

        // Data Grafik Analytics Chart.js
        $chartStatusData = [
            'Tersedia' => $alkesTersedia,
            'Sedang Digunakan' => $alkesDigunakan,
            'Dalam Perbaikan' => $alkesRusak,
        ];

        $chartSeksiLabels = [];
        $chartKondisiBaik = [];
        $chartKondisiRusak = [];

        foreach ($seksiList as $seksi) {
            $chartSeksiLabels[] = str_replace('Seksi ', '', $seksi->nama_seksi);
            $chartKondisiBaik[] = Alkes::where('seksi_pemilik_id', $seksi->id)->where('kondisi', KondisiAlkes::BAIK->value)->count();
            $chartKondisiRusak[] = Alkes::where('seksi_pemilik_id', $seksi->id)->where('kondisi', '!=', KondisiAlkes::BAIK->value)->count();
        }

        // Grafik Kategori Nomenklatur (Jumlah Alat per Kategori Medis)
        $kategoriData = DB::table('alkes')
            ->join('nomenklatur', 'alkes.nomenklatur_id', '=', 'nomenklatur.id')
            ->select('nomenklatur.kategori', DB::raw('count(alkes.id) as total'))
            ->groupBy('nomenklatur.kategori')
            ->orderBy('total', 'desc')
            ->get();

        $chartKategoriLabels = [];
        $chartKategoriCounts = [];
        foreach ($kategoriData as $kat) {
            $chartKategoriLabels[] = $kat->kategori ?? 'Lainnya';
            $chartKategoriCounts[] = $kat->total;
        }

        // Recent Audit Trail Activity Logs
        $recentActivityLogs = ActivityLog::latest()->take(6)->get();

        return view('dashboard', compact(
            'totalAlkes',
            'alkesTersedia',
            'alkesDigunakan',
            'alkesRusak',
            'alkesKalibrasi',
            'seksiList',
            'mutasiTerbaru',
            'logPerbaikanTerbaru',
            'chartStatusData',
            'chartSeksiLabels',
            'chartKondisiBaik',
            'chartKondisiRusak',
            'chartKategoriLabels',
            'chartKategoriCounts',
            'recentActivityLogs'
        ));
    }
}
