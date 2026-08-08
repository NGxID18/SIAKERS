<?php

namespace App\Http\Controllers;

use App\Enums\KondisiAlkes;
use App\Enums\StatusAlkes;
use App\Models\ActivityLog;
use App\Models\Alkes;
use App\Models\LogPemeliharaan;
use App\Models\MutasiAlkes;
use App\Models\Ruangan;

class DashboardController extends Controller
{
    public function index()
    {
        $totalAlkes = Alkes::count();

        // Unit Rusak / Dalam Perbaikan
        $alkesRusak = Alkes::where(function ($q) {
            $q->where('status', StatusAlkes::DALAM_PERBAIKAN->value)
              ->orWhere('kondisi', '!=', KondisiAlkes::BAIK->value);
        })->count();

        // Unit Baik / Operasional di Ruangan (Tersedia & Aktif Digunakan)
        $alkesTersedia = $totalAlkes - $alkesRusak;

        // Rekap per Ruangan RS Diurutkan Berdasarkan Abjad Nama Ruangan (A-Z)
        $ruanganList = Ruangan::withCount(['alkes', 'alkes as alkes_rusak_count' => function ($q) {
            $q->where(function ($subQ) {
                $subQ->where('status', StatusAlkes::DALAM_PERBAIKAN->value)
                     ->orWhere('kondisi', '!=', KondisiAlkes::BAIK->value);
            });
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

        // Data Grafik Analytics Status
        $chartStatusData = [
            'Operasional / Baik' => $alkesTersedia,
            'Dalam Perbaikan / Rusak' => $alkesRusak,
        ];

        // Grafik Kondisi per Ruangan RS (Diurutkan Abjad A-Z)
        $chartRuanganLabels = [];
        $chartKondisiBaik = [];
        $chartKondisiRusak = [];

        foreach ($ruanganList as $ruang) {
            if ($ruang->alkes_count > 0) {
                $chartRuanganLabels[] = $ruang->nama_ruangan;
                $rusakCount = (int) $ruang->alkes_rusak_count;
                $baikCount = max(0, ((int) $ruang->alkes_count) - $rusakCount);
                $chartKondisiBaik[] = $baikCount;
                $chartKondisiRusak[] = $rusakCount;
            }
        }

        // Recent Audit Trail Activity Logs
        $recentActivityLogs = ActivityLog::latest()->take(6)->get();

        return view('dashboard', compact(
            'totalAlkes',
            'alkesTersedia',
            'alkesRusak',
            'ruanganList',
            'mutasiTerbaru',
            'logPerbaikanTerbaru',
            'chartStatusData',
            'chartRuanganLabels',
            'chartKondisiBaik',
            'chartKondisiRusak',
            'recentActivityLogs'
        ));
    }
}
