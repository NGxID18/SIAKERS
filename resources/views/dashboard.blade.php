@extends('layouts.app')

@section('title', 'Dashboard Inventaris Alkes')

@section('content')
<div class="space-y-6">

    @php
        $currentRole = session('user_role', 'elektromedis');
    @endphp

    <!-- Welcome Flash Alert -->
    <div id="welcomeBanner" class="p-5 bg-gradient-to-r from-teal-900 via-teal-800 to-slate-900 text-white rounded-3xl border border-teal-700/60 shadow-xl flex items-center justify-between transition-all">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-teal-500/30 text-teal-300 flex items-center justify-center text-xl shrink-0 border border-teal-400/30">
                <i class="ri-checkbox-circle-fill"></i>
            </div>
            <div>
                <p class="font-bold text-sm sm:text-base text-white">Selamat Datang di Sistem Informasi SIAKERS</p>
                <p class="text-xs text-teal-200 mt-0.5">Anda aktif masuk sebagai <span class="font-bold text-amber-300">{{ session('user_role_label', 'Instalasi Elektromedis') }}</span> di RSJKO Engku Haji Daud.</p>
            </div>
        </div>
        <button type="button" onclick="document.getElementById('welcomeBanner').remove()" class="text-teal-300 hover:text-white text-xl p-1 rounded-xl transition">
            <i class="ri-close-line"></i>
        </button>
    </div>

    <!-- Top Metric Cards Row (Clean 3 Cards Layout with Depth & Hover Elevation) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Stat 1: Total Unit Alkes Terdata -->
        <a href="{{ route('alkes.index') }}" class="bg-white p-6 rounded-3xl border border-slate-200/80 border-l-4 border-l-teal-600 shadow-lg shadow-slate-200/50 hover:shadow-xl hover:shadow-teal-900/10 hover:-translate-y-1 transition-all duration-300 group flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Unit Alkes</p>
                <h3 class="text-4xl font-black text-slate-900 mt-2 tracking-tight group-hover:text-teal-600 transition-colors">{{ number_format($totalAlkes) }}</h3>
                <p class="text-xs text-slate-500 mt-1 font-medium flex items-center gap-1">
                    <i class="ri-hospital-line text-teal-600"></i> Terdata di RSJKO Engku Haji Daud
                </p>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-3xl group-hover:bg-teal-600 group-hover:text-white transition-all duration-300 shadow-sm shrink-0">
                <i class="ri-stethoscope-line"></i>
            </div>
        </a>

        <!-- Stat 2: Unit Operasional / Baik di Ruangan -->
        <a href="{{ route('alkes.index', ['kondisi' => 'BAIK']) }}" class="bg-white p-6 rounded-3xl border border-slate-200/80 border-l-4 border-l-emerald-500 shadow-lg shadow-slate-200/50 hover:shadow-xl hover:shadow-emerald-900/10 hover:-translate-y-1 transition-all duration-300 group flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Unit Operasional (Baik)</p>
                <h3 class="text-4xl font-black text-emerald-700 mt-2 tracking-tight">{{ number_format($alkesTersedia) }}</h3>
                <p class="text-xs text-slate-500 mt-1 font-medium flex items-center gap-1">
                    <i class="ri-checkbox-circle-line text-emerald-600"></i> Aktif Digunakan di Ruangan
                </p>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-3xl group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300 shadow-sm shrink-0">
                <i class="ri-checkbox-circle-line"></i>
            </div>
        </a>

        <!-- Stat 3: Unit Rusak (Dalam Perbaikan / Kalibrasi) -->
        <a href="{{ route('pemeliharaan.index') }}" class="bg-white p-6 rounded-3xl border border-slate-200/80 border-l-4 border-l-rose-500 shadow-lg shadow-slate-200/50 hover:shadow-xl hover:shadow-rose-900/10 hover:-translate-y-1 transition-all duration-300 group flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-rose-700 uppercase tracking-wider">Unit Perlu Perbaikan</p>
                <h3 class="text-4xl font-black text-rose-700 mt-2 tracking-tight">{{ number_format($alkesRusak) }}</h3>
                <p class="text-xs text-rose-600 font-bold mt-1 flex items-center gap-1">
                    <i class="ri-tools-line text-rose-500"></i> Dalam Perbaikan / Kalibrasi
                </p>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-3xl group-hover:bg-rose-600 group-hover:text-white transition-all duration-300 shadow-sm shrink-0">
                <i class="ri-error-warning-line"></i>
            </div>
        </a>

    </div>

    <!-- Charts Split Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">
        
        <!-- Chart 1: Donut Chart Status Kondisi Alkes RS -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-lg shadow-slate-200/50 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h4 class="font-bold text-slate-900 text-base sm:text-lg flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-lg">
                        <i class="ri-pie-chart-2-line"></i>
                    </div>
                    Kondisi Aset Alkes RS
                </h4>
                <span class="text-xs font-semibold px-3 py-1 bg-slate-100 text-slate-600 rounded-xl">Proporsi Total</span>
            </div>
            <div class="relative h-64 flex items-center justify-center">
                <canvas id="chartStatusKondisi"></canvas>
            </div>
        </div>

        <!-- Chart 2: Stacked Bar Chart Kondisi Alkes per Ruangan -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-lg shadow-slate-200/50 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h4 class="font-bold text-slate-900 text-base sm:text-lg flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-lg">
                        <i class="ri-bar-chart-grouped-line"></i>
                    </div>
                    Kondisi Alkes per Ruangan
                </h4>
                <span class="text-xs font-semibold px-3 py-1 bg-slate-100 text-slate-600 rounded-xl">Per Ruangan</span>
            </div>
            <div class="relative h-64">
                <canvas id="chartRuanganKondisi"></canvas>
            </div>
        </div>

    </div>

    <!-- Grid Rekapitulasi Sebaran Ruangan -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-lg shadow-slate-200/50 space-y-5">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between border-b border-slate-100 pb-4 gap-2">
            <div>
                <h4 class="font-extrabold text-slate-900 text-lg flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-lg">
                        <i class="ri-building-4-line"></i>
                    </div>
                    Sebaran Unit Alkes per Ruangan
                </h4>
                <p class="text-xs text-slate-500 mt-0.5">Distribusi aset alat kesehatan aktif di setiap unit instalasi/ruangan</p>
            </div>
            <a href="{{ route('ruangan.index') }}" class="px-4 py-2 bg-teal-50 hover:bg-teal-600 text-teal-700 hover:text-white rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 border border-teal-200">
                <span>Lihat Semua Ruangan</span>
                <i class="ri-arrow-right-line"></i>
            </a>
        </div>

        <!-- Ruangan Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ($ruanganList as $r)
                <a href="{{ route('alkes.index', ['ruangan_id' => $r->id]) }}" class="bg-slate-50 hover:bg-white p-4 rounded-2xl border border-slate-200 hover:border-teal-400 hover:shadow-md transition-all duration-200 group flex flex-col justify-between">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block truncate">Ruangan</span>
                            <h5 class="font-bold text-slate-800 text-sm group-hover:text-teal-600 transition truncate mt-0.5">{{ $r->nama_ruangan }}</h5>
                        </div>
                        <div class="w-8 h-8 rounded-xl bg-teal-100/80 text-teal-700 flex items-center justify-center text-sm font-bold shrink-0 group-hover:bg-teal-600 group-hover:text-white transition">
                            <i class="ri-hospital-line"></i>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-200/60 flex items-center justify-between text-xs">
                        <span class="text-slate-500 font-medium">Jumlah Unit:</span>
                        <span class="font-black text-slate-900 bg-white px-2.5 py-0.5 rounded-lg border border-slate-200 group-hover:border-teal-300 group-hover:text-teal-700 transition">
                            {{ $r->alkes_count }} Unit
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

</div>

<!-- Chart.js Script Configuration -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Donut Chart - Kondisi Aset Alkes RS
    const ctxKondisi = document.getElementById('chartStatusKondisi').getContext('2d');
    new Chart(ctxKondisi, {
        type: 'doughnut',
        data: {
            labels: ['Operasional / Baik', 'Dalam Perbaikan / Rusak'],
            datasets: [{
                data: [{{ $alkesTersedia }}, {{ $alkesRusak }}],
                backgroundColor: ['#10b981', '#ef4444'],
                borderWidth: 3,
                borderColor: '#ffffff',
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        font: { family: 'Source Sans 3', size: 12, weight: '600' },
                        padding: 16
                    }
                }
            },
            cutout: '70%'
        }
    });

    // 2. Stacked Bar Chart - Kondisi per Ruangan
    const ctxRuangan = document.getElementById('chartRuanganKondisi').getContext('2d');
    new Chart(ctxRuangan, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartRuanganLabels) !!},
            datasets: [
                {
                    label: 'Baik / Operasional',
                    data: {!! json_encode($chartKondisiBaik) !!},
                    backgroundColor: '#10b981',
                    borderRadius: 6
                },
                {
                    label: 'Rusak / Perbaikan',
                    data: {!! json_encode($chartKondisiRusak) !!},
                    backgroundColor: '#ef4444',
                    borderRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { stacked: true, grid: { display: false } },
                y: { stacked: true, beginAtZero: true, grid: { color: '#f1f5f9' } }
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { usePointStyle: true, font: { family: 'Source Sans 3', size: 11, weight: '600' } }
                }
            }
        }
    });
});
</script>
@endsection
