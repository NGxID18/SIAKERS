@extends('layouts.app')

@section('title', 'Dashboard ERP Inventaris Alkes')

@section('content')
<div class="space-y-6">

    <!-- Hero / Ringkasan Atas -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-gradient-to-r from-teal-700 via-teal-800 to-slate-900 rounded-2xl p-6 text-white shadow-xl">
        <div class="space-y-1">
            <h3 class="text-2xl font-extrabold tracking-tight">Sistem ERP & Monitoring Alkes RS</h3>
            <p class="text-teal-100 text-sm max-w-2xl">
                Pantau ketersediaan, lokasi seksi operasional, status penggunaan, hingga riwayat pemeliharaan & kalibrasi alat kesehatan secara real-time.
            </p>
        </div>
    </div>

    <!-- Status Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Total Unit Alkes -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Unit Alkes</p>
                <h4 class="text-3xl font-extrabold text-slate-900 mt-1">{{ $totalAlkes }}</h4>
                <p class="text-xs text-slate-500 mt-1">Terdaftar dalam database ERP</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-2xl">
                <i class="ri-heart-pulse-line"></i>
            </div>
        </div>

        <!-- Tersedia / Standby -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Tersedia / Standby</p>
                <h4 class="text-3xl font-extrabold text-slate-900 mt-1">{{ $alkesTersedia }}</h4>
                <p class="text-xs text-slate-500 mt-1">Siap digunakan di seksi</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl">
                <i class="ri-checkbox-circle-line"></i>
            </div>
        </div>

        <!-- Sedang Digunakan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Sedang Digunakan</p>
                <h4 class="text-3xl font-extrabold text-slate-900 mt-1">{{ $alkesDigunakan }}</h4>
                <p class="text-xs text-slate-500 mt-1">Aktif pada pasien / tindakan</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl">
                <i class="ri-pulse-line"></i>
            </div>
        </div>

        <!-- Rusak / Perbaikan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-rose-600 uppercase tracking-wider">Dalam Perbaikan (Rusak)</p>
                <h4 class="text-3xl font-extrabold text-slate-900 mt-1">{{ $alkesRusak }}</h4>
                <p class="text-xs text-slate-500 mt-1">Maintenance / Service Vendor</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-2xl">
                <i class="ri-error-warning-line"></i>
            </div>
        </div>
    </div>

    <!-- Layout Dua Kolom: Status Per Seksi & Log Terbaru -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kolom Kiri (2 Span): Rekapitulasi Alkes per Seksi -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h4 class="font-bold text-lg text-slate-800">Sebaran Alkes Per Seksi Operasional</h4>
                    <p class="text-xs text-slate-500">Ketersediaan dan kondisi alat pada setiap seksi/departemen RS</p>
                </div>
                <a href="{{ route('seksi.index') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700">Lihat Semua Seksi &rarr;</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($seksiList as $seksi)
                    <div class="p-4 rounded-xl border border-slate-100 bg-slate-50 hover:bg-slate-100 transition space-y-3">
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-teal-100 text-teal-800 uppercase">{{ $seksi->kode_seksi }}</span>
                                <h5 class="font-bold text-slate-800 text-base mt-1">{{ $seksi->nama_seksi }}</h5>
                            </div>
                            <span class="text-xs text-slate-500"><i class="ri-user-line"></i> {{ $seksi->penanggung_jawab ?? 'PJ Belum Ditentukan' }}</span>
                        </div>

                        <div class="grid grid-cols-3 gap-2 text-center text-xs">
                            <div class="p-2 rounded-lg bg-white border border-slate-200">
                                <p class="text-slate-400 text-[10px]">Total Alat</p>
                                <p class="font-bold text-slate-800 text-base">{{ $seksi->alkes_count }}</p>
                            </div>
                            <div class="p-2 rounded-lg bg-blue-50 border border-blue-100 text-blue-800">
                                <p class="text-blue-500 text-[10px]">Digunakan</p>
                                <p class="font-bold text-base">{{ $seksi->alkes_digunakan_count }}</p>
                            </div>
                            <div class="p-2 rounded-lg bg-rose-50 border border-rose-100 text-rose-800">
                                <p class="text-rose-500 text-[10px]">Rusak</p>
                                <p class="font-bold text-base">{{ $seksi->alkes_rusak_count }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Kolom Kanan (1 Span): Mutasi Alkes Terakhir -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h4 class="font-bold text-lg text-slate-800">Mutasi Alkes Terbaru</h4>
                    <p class="text-xs text-slate-500">Perpindahan alat antar seksi</p>
                </div>
                <a href="{{ route('mutasi.index') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700">Riwayat Mutasi &rarr;</a>
            </div>

            <div class="space-y-3">
                @forelse ($mutasiTerbaru as $mutasi)
                    <div class="p-3 rounded-xl border border-slate-100 bg-slate-50 text-xs space-y-2">
                        <div class="flex justify-between font-bold text-slate-800">
                            <span>{{ $mutasi->alkes->nomenklatur->nama_alat ?? 'Alkes' }}</span>
                            <span class="text-slate-400 text-[10px]">{{ $mutasi->tanggal_mutasi ? $mutasi->tanggal_mutasi->format('d/m/Y') : '-' }}</span>
                        </div>
                        <div class="text-slate-600 flex items-center gap-1.5 flex-wrap">
                            <span class="px-2 py-0.5 rounded bg-slate-200 font-medium">{{ $mutasi->seksiAsal->nama_seksi ?? 'Awal' }}</span>
                            <i class="ri-arrow-right-line text-slate-400"></i>
                            <span class="px-2 py-0.5 rounded bg-teal-100 text-teal-800 font-bold">{{ $mutasi->seksiTujuan->nama_seksi ?? 'Tujuan' }}</span>
                        </div>
                        <p class="text-[11px] text-slate-500 italic">"{{ Str::limit($mutasi->alasan_mutasi, 60) }}"</p>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 italic text-center py-4">Belum ada riwayat mutasi alkes.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
