<?php

namespace App\Http\Controllers;

use App\Enums\KondisiAlkes;
use App\Enums\StatusAlkes;
use App\Models\Alkes;
use App\Models\LogPemeliharaan;
use App\Models\MutasiAlkes;
use App\Models\Seksi;

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

        // Rekap per Seksi
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
        $logPerbaikanTerbaru = LogPemeliharaan::with(['alkes.nomenklatur', 'alkes.seksi'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalAlkes',
            'alkesTersedia',
            'alkesDigunakan',
            'alkesRusak',
            'alkesKalibrasi',
            'seksiList',
            'mutasiTerbaru',
            'logPerbaikanTerbaru'
        ));
    }
}
