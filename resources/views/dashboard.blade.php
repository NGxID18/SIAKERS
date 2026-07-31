@extends('layouts.app')

@section('title', 'Dashboard Inventaris Alkes')

@section('content')
<div class="space-y-6">

    <!-- Top Hero Welcome & Stat Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Alkes -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Unit Alkes</span>
                <h3 class="text-3xl font-extrabold text-slate-900 mt-1">{{ number_format($totalAlkes) }}</h3>
                <span class="text-xs text-teal-600 font-semibold mt-1 inline-block">Terdata di Database</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-2xl shadow-xs">
                <i class="ri-stethoscope-line"></i>
            </div>
        </div>

        <!-- Alkes Tersedia -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Unit Tersedia (Baik)</span>
                <h3 class="text-3xl font-extrabold text-emerald-600 mt-1">{{ number_format($alkesTersedia) }}</h3>
                <span class="text-xs text-slate-500 font-medium mt-1 inline-block">Siap Digunakan</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl shadow-xs">
                <i class="ri-checkbox-circle-line"></i>
            </div>
        </div>

        <!-- Sedang Digunakan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Sedang Digunakan</span>
                <h3 class="text-3xl font-extrabold text-blue-600 mt-1">{{ number_format($alkesDigunakan) }}</h3>
                <span class="text-xs text-slate-500 font-medium mt-1 inline-block">Aktif di Ruangan</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl shadow-xs">
                <i class="ri-pulse-line"></i>
            </div>
        </div>

        <!-- Dalam Perbaikan / Rusak -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Perlu Perbaikan / Rusak</span>
                <h3 class="text-3xl font-extrabold text-rose-600 mt-1">{{ number_format($alkesRusak) }}</h3>
                <span class="text-xs text-rose-600 font-semibold mt-1 inline-block">Butuh Maintenance</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-2xl shadow-xs">
                <i class="ri-error-warning-line"></i>
            </div>
        </div>

    </div>

    <!-- Analytics Charts Row 1 -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <!-- Chart 1: Status Distribution (Doughnut) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <h4 class="font-extrabold text-slate-900 text-base flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="ri-pie-chart-line text-teal-600"></i>
                Status Penggunaan Alkes
            </h4>
            <div class="relative h-64">
                <canvas id="chartStatus"></canvas>
            </div>
        </div>

        <!-- Chart 2: Kondisi Fisik Alat per Ruangan Utama (Bar Stacked) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <h4 class="font-extrabold text-slate-900 text-base flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="ri-bar-chart-grouped-line text-teal-600"></i>
                Kondisi Alkes per Ruangan Utama
            </h4>
            <div class="relative h-64">
                <canvas id="chartRuanganKondisi"></canvas>
            </div>
        </div>

    </div>

    <!-- Chart 3: Distribusi Alat Berdasarkan Cara Perolehan (DAK/APBD/BLUD/HIBAH) -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <h4 class="font-extrabold text-slate-900 text-base flex items-center gap-2 border-b border-slate-100 pb-3">
            <i class="ri-price-tag-3-line text-teal-600"></i>
            Distribusi Alkes Berdasarkan Cara Perolehan (Sumber Dana)
        </h4>
        <div class="relative h-64">
            <canvas id="chartPerolehan"></canvas>
        </div>
    </div>

    <!-- Grid Rekapitulasi Ruangan (Urut Abjad A-Z) -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h4 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                <i class="ri-building-4-line text-teal-600"></i>
                Sebaran Unit Alkes per Ruangan (Abjad A-Z)
            </h4>
            <a href="{{ route('ruangan.index') }}" class="text-xs font-bold text-teal-600 hover:text-teal-800 transition flex items-center gap-1">
                Lihat Semua Ruangan <i class="ri-arrow-right-line"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
            @foreach ($ruanganList as $ruang)
                <a href="{{ route('alkes.index', ['ruangan_id' => $ruang->id]) }}" class="p-3.5 bg-slate-50 hover:bg-teal-50 hover:border-teal-300 rounded-xl border border-slate-200 transition group flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="font-extrabold text-slate-900 text-sm group-hover:text-teal-800 transition">{{ $ruang->nama_ruangan }}</span>
                        <span class="px-2 py-0.5 bg-white text-slate-700 font-bold text-xs rounded border border-slate-200 group-hover:bg-teal-600 group-hover:text-white transition">
                            {{ $ruang->alkes_count }} Unit
                        </span>
                    </div>

                    <div class="flex items-center gap-2 text-[11px] text-slate-500 mt-2">
                        <span class="text-emerald-600 font-bold">Baik: {{ $ruang->alkes_count - $ruang->alkes_rusak_count }}</span>
                        <span>•</span>
                        <span class="text-rose-600 font-bold">Rusak: {{ $ruang->alkes_rusak_count }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Bottom Split: Audit Trail (Left 50%) vs Pindah Ruangan Terbaru (Right 50%) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

        <!-- Activity Audit Trail Log System (KIRI) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h4 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                    <i class="ri-history-line text-teal-600"></i>
                    Audit Trail Activity Log System
                </h4>
                <a href="{{ route('activity-logs.index') }}" class="text-xs font-bold text-teal-600 hover:text-teal-800 transition flex items-center gap-1">
                    Lihat Log Lengkap <i class="ri-arrow-right-line"></i>
                </a>
            </div>

            <div class="space-y-3">
                @forelse ($recentActivityLogs as $log)
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-teal-100 text-teal-700 font-bold flex items-center justify-center shrink-0 mt-0.5 text-xs">
                            <i class="ri-user-follow-line"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-bold text-xs text-slate-900 truncate">{{ $log->user_role }} ({{ $log->ruangan_name ?? 'Utama' }})</span>
                                <span class="text-[10px] text-slate-400 shrink-0 font-mono">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                            <span class="inline-block px-2 py-0.5 bg-teal-50 text-teal-800 text-[10px] font-extrabold rounded border border-teal-200 mt-1">
                                {{ $log->action }}
                            </span>
                            <p class="text-xs text-slate-600 mt-1 truncate">{{ $log->description }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-4">Belum ada catatan audit trail.</p>
                @endforelse
            </div>
        </div>

        <!-- Pindah Ruangan Terbaru (KANAN) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h4 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                    <i class="ri-arrow-left-right-line text-teal-600"></i>
                    Pindah Ruangan Alkes Terbaru
                </h4>
                <a href="{{ route('mutasi.index') }}" class="text-xs font-bold text-teal-600 hover:text-teal-800 transition flex items-center gap-1">
                    Lihat Histori <i class="ri-arrow-right-line"></i>
                </a>
            </div>

            <div class="space-y-3">
                @forelse ($mutasiTerbaru as $mut)
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 font-bold flex items-center justify-center shrink-0 mt-0.5 text-xs">
                            <i class="ri-swap-line"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-xs text-slate-900 truncate">{{ $mut->alkes->nama_barang ?? 'Alkes' }}</div>
                            <div class="flex items-center gap-1 text-[11px] mt-1">
                                <span class="px-2 py-0.5 bg-slate-200 text-slate-700 rounded font-semibold">{{ $mut->ruanganAsal->nama_ruangan ?? 'Ruangan Asal' }}</span>
                                <i class="ri-arrow-right-line text-teal-600 font-bold"></i>
                                <span class="px-2 py-0.5 bg-teal-100 text-teal-800 rounded font-extrabold">{{ $mut->ruanganTujuan->nama_ruangan ?? 'Ruangan Tujuan' }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-4">Belum ada riwayat pemindahan ruangan alkes.</p>
                @endforelse
            </div>
        </div>

    </div>

</div>

<!-- Chart.js Data Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chart 1: Status Doughnut
        new Chart(document.getElementById('chartStatus'), {
            type: 'doughnut',
            data: {
                labels: @json(array_keys($chartStatusData)),
                datasets: [{
                    data: @json(array_values($chartStatusData)),
                    backgroundColor: ['#10b981', '#3b82f6', '#f43f5e'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Chart 2: Kondisi per Ruangan Stacked Bar
        new Chart(document.getElementById('chartRuanganKondisi'), {
            type: 'bar',
            data: {
                labels: @json($chartRuanganLabels),
                datasets: [
                    {
                        label: 'Kondisi Baik',
                        data: @json($chartKondisiBaik),
                        backgroundColor: '#10b981'
                    },
                    {
                        label: 'Kondisi Rusak',
                        data: @json($chartKondisiRusak),
                        backgroundColor: '#f43f5e'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { stacked: true },
                    y: { stacked: true }
                },
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Chart 3: Cara Perolehan Bar Chart
        new Chart(document.getElementById('chartPerolehan'), {
            type: 'bar',
            data: {
                labels: @json($chartPerolehanLabels),
                datasets: [{
                    label: 'Jumlah Unit Alkes',
                    data: @json($chartPerolehanCounts),
                    backgroundColor: '#0d9488',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                }
            }
        });
    });
</script>
@endsection
