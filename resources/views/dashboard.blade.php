@extends('layouts.app')

@section('title', 'Dashboard Inventaris Alkes')

@section('content')
<div class="space-y-6">

    @php
        $currentRole = session('user_role', 'elektromedis');
    @endphp

    <!-- Welcome Flash Alert -->
    <div id="welcomeBanner" class="p-4 bg-teal-50 border border-teal-200 rounded-2xl text-teal-900 text-sm font-semibold flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-2.5">
            <i class="ri-checkbox-circle-fill text-xl text-teal-600"></i>
            <span>Berhasil masuk sebagai <strong>{{ session('user_role_label', 'Instalasi Elektromedis') }}</strong>.</span>
        </div>
        <button type="button" onclick="document.getElementById('welcomeBanner').remove()" class="text-teal-500 hover:text-teal-800 text-lg">
            <i class="ri-close-line"></i>
        </button>
    </div>

    <!-- Top Metric Cards Row (Clean 3 Cards Layout) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Stat 1: Total Unit Alkes Terdata -->
        <a href="{{ route('alkes.index') }}" class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:border-teal-400 hover:shadow-md transition group flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Unit Alkes</p>
                <h3 class="text-4xl font-extrabold text-slate-900 mt-2 tracking-tight group-hover:text-teal-700 transition">{{ number_format($totalAlkes) }}</h3>
                <p class="text-xs text-slate-500 mt-1 font-normal">Terdata di RSJKO Engku Haji Daud</p>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-3xl group-hover:bg-teal-600 group-hover:text-white transition shadow-sm">
                <i class="ri-stethoscope-line"></i>
            </div>
        </a>

        <!-- Stat 2: Unit Operasional / Baik di Ruangan -->
        <a href="{{ route('alkes.index', ['kondisi' => 'BAIK']) }}" class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:border-emerald-400 hover:shadow-md transition group flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Unit Operasional (Baik)</p>
                <h3 class="text-4xl font-extrabold text-emerald-700 mt-2 tracking-tight">{{ number_format($alkesTersedia) }}</h3>
                <p class="text-xs text-slate-500 mt-1 font-normal">Aktif Digunakan di Ruangan</p>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-3xl group-hover:bg-emerald-600 group-hover:text-white transition shadow-sm">
                <i class="ri-checkbox-circle-line"></i>
            </div>
        </a>

        <!-- Stat 3: Unit Rusak (Dalam Perbaikan / Kalibrasi) -->
        <a href="{{ route('pemeliharaan.index') }}" class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:border-rose-400 hover:shadow-md transition group flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-rose-800 uppercase tracking-wider">Unit Perlu Perbaikan</p>
                <h3 class="text-4xl font-extrabold text-rose-700 mt-2 tracking-tight">{{ number_format($alkesRusak) }}</h3>
                <p class="text-xs text-rose-800 font-semibold mt-1">Dalam Perbaikan / Kalibrasi</p>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-3xl group-hover:bg-rose-600 group-hover:text-white transition shadow-sm">
                <i class="ri-error-warning-line"></i>
            </div>
        </a>

    </div>

    <!-- Charts Split Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">
        
        <!-- Chart 1: Donut Chart Status Kondisi Alkes RS -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <h4 class="font-bold text-slate-900 text-lg flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="ri-pie-chart-2-line text-teal-600"></i>
                Kondisi Aset Alkes RS
            </h4>
            <div class="relative h-64 flex items-center justify-center">
                <canvas id="chartStatusKondisi"></canvas>
            </div>
        </div>

        <!-- Chart 2: Stacked Bar Chart Kondisi Alkes per Ruangan -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <h4 class="font-bold text-slate-900 text-lg flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="ri-bar-chart-grouped-line text-teal-600"></i>
                Kondisi Alkes per Ruangan
            </h4>
            <div class="relative h-64">
                <canvas id="chartRuanganKondisi"></canvas>
            </div>
        </div>

    </div>

    <!-- Grid Rekapitulasi Ruangan -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h4 class="font-bold text-slate-900 text-lg flex items-center gap-2">
                <i class="ri-building-4-line text-teal-600"></i>
                Sebaran Unit Alkes per Ruangan
            </h4>
            <a href="{{ route('ruangan.index') }}" class="text-sm font-semibold text-teal-600 hover:text-teal-800 transition flex items-center gap-1">
                Lihat Semua Ruangan <i class="ri-arrow-right-line"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ($ruanganList as $ruang)
                <a href="{{ route('alkes.index', ['ruangan_id' => $ruang->id]) }}" class="p-4 bg-slate-50 hover:bg-teal-50 hover:border-teal-300 rounded-xl border border-slate-200 transition group flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-slate-900 text-base group-hover:text-teal-800 transition">{{ $ruang->nama_ruangan }}</span>
                        <span class="px-2.5 py-0.5 bg-white text-slate-800 font-semibold text-xs rounded-md border border-slate-200 group-hover:bg-teal-600 group-hover:text-white transition">
                            {{ $ruang->alkes_count }} Unit
                        </span>
                    </div>

                    <div class="flex items-center gap-2.5 text-xs text-slate-600 mt-2.5">
                        <span class="text-emerald-700 font-semibold">Baik: {{ $ruang->alkes_count - $ruang->alkes_rusak_count }}</span>
                        <span>•</span>
                        <span class="text-rose-700 font-semibold">Rusak: {{ $ruang->alkes_rusak_count }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Bottom Split: Riwayat Aktivitas Sistem (Left 50%) vs Pindah Ruangan Terbaru (Right 50%) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

        <!-- Riwayat Aktivitas Sistem (KIRI) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h4 class="font-bold text-slate-900 text-lg flex items-center gap-2">
                    <i class="ri-history-line text-teal-600"></i>
                    Riwayat Aktivitas Sistem
                </h4>
                <a href="{{ route('activity-logs.index') }}" class="text-sm font-semibold text-teal-600 hover:text-teal-800 transition flex items-center gap-1">
                    Lihat Log Lengkap <i class="ri-arrow-right-line"></i>
                </a>
            </div>

            <div class="space-y-3">
                @forelse ($recentActivityLogs as $log)
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-sm shrink-0">
                            <i class="ri-pulse-line"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-slate-900 text-sm">{{ $log->action }}</span>
                                <span class="text-[11px] text-slate-500 font-mono">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs text-slate-600 mt-0.5 leading-snug">{{ $log->description }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400 text-sm">
                        Belum ada riwayat aktivitas recorded.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pindah Ruangan Terbaru (KANAN) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h4 class="font-bold text-slate-900 text-lg flex items-center gap-2">
                    <i class="ri-arrow-left-right-line text-teal-600"></i>
                    Pindah Ruangan Terbaru
                </h4>
                <a href="{{ route('mutasi.index') }}" class="text-sm font-semibold text-teal-600 hover:text-teal-800 transition flex items-center gap-1">
                    Lihat Semua Mutasi <i class="ri-arrow-right-line"></i>
                </a>
            </div>

            <div class="space-y-3">
                @forelse ($mutasiTerbaru as $mutasi)
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <h5 class="font-bold text-slate-900 text-sm truncate">{{ $mutasi->alkes->nama_barang ?? 'Alkes' }}</h5>
                            <div class="flex items-center gap-1.5 text-xs text-slate-600 mt-1">
                                <span class="px-2 py-0.5 bg-white rounded border border-slate-200 font-medium">{{ $mutasi->ruanganAsal->nama_ruangan ?? '-' }}</span>
                                <i class="ri-arrow-right-line text-teal-600 font-bold"></i>
                                <span class="px-2 py-0.5 bg-teal-100 text-teal-800 rounded border border-teal-200 font-bold">{{ $mutasi->ruanganTujuan->nama_ruangan ?? '-' }}</span>
                            </div>
                        </div>
                        <span class="text-xs text-slate-500 font-mono shrink-0">{{ $mutasi->tanggal_mutasi ? $mutasi->tanggal_mutasi->format('d/m/Y') : '-' }}</span>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400 text-sm">
                        Belum ada data pemindahan ruangan alkes.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>

<!-- Chart.js Script Configuration -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chart 1: Donut Chart Status Kondisi Alkes
        const ctxStatus = document.getElementById('chartStatusKondisi');
        if (ctxStatus) {
            new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode(array_keys($chartStatusData)) !!},
                    datasets: [{
                        data: {!! json_encode(array_values($chartStatusData)) !!},
                        backgroundColor: ['#10b981', '#f43f5e'],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: { size: 12, weight: '600' },
                                padding: 16,
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        }

        // Chart 2: Stacked Bar Chart Kondisi per Ruangan
        const ctxRuangan = document.getElementById('chartRuanganKondisi');
        if (ctxRuangan) {
            new Chart(ctxRuangan, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartRuanganLabels) !!},
                    datasets: [
                        {
                            label: 'Kondisi Baik (Operasional)',
                            data: {!! json_encode($chartKondisiBaik) !!},
                            backgroundColor: '#10b981',
                            borderRadius: 4,
                        },
                        {
                            label: 'Dalam Perbaikan (Rusak)',
                            data: {!! json_encode($chartKondisiRusak) !!},
                            backgroundColor: '#f43f5e',
                            borderRadius: 4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            stacked: true,
                            grid: { display: false },
                            ticks: { font: { size: 10, weight: '500' }, maxRotation: 45, minRotation: 45 }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            grid: { color: '#f1f5f9' },
                            ticks: { font: { size: 11 } }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { font: { size: 11, weight: '600' } }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection
