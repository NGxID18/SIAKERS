@extends('layouts.app')

@section('title', 'Perbaikan & Kalibrasi Alkes')

@section('content')
<div class="space-y-6">

    @php
        $currentRole = session('user_role', 'elektromedis');
    @endphp

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <i class="ri-tools-line text-amber-600"></i>
                Perbaikan & Kalibrasi Alkes
            </h3>
            <p class="text-base text-slate-600 mt-1 font-normal">
                Pelaporan perbaikan dari ruangan operasional, penanganan unit di Ruangan Elektromedis, dan riwayat pemeliharaan
            </p>
        </div>

        <a href="{{ route('pemeliharaan.create') }}" class="px-5 py-3 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-base rounded-xl shadow-md shadow-amber-600/30 transition flex items-center gap-2">
            <i class="ri-add-line text-xl"></i>
            Lapor Barang Rusak / Perbaikan
        </a>
    </div>

    <!-- Ringkas & Jelas: Compact 1-Line Info Banner dengan Tombol Tutup -->
    <div id="infoPerbaikanBanner" class="bg-amber-50/90 px-4 py-3 rounded-xl border border-amber-200 text-amber-900 text-sm font-normal flex items-center justify-between gap-3 shadow-xs">
        <div class="flex items-center gap-2.5">
            <i class="ri-information-fill text-amber-600 text-lg shrink-0"></i>
            <span><strong>Alur Perbaikan:</strong> Unit alkes yang dilaporkan rusak otomatis dipindahkan ke <strong>Ruangan Elektromedis</strong> untuk diperbaiki dan dikembalikan ke ruangan asal setelah selesai.</span>
        </div>
        <button type="button" onclick="document.getElementById('infoPerbaikanBanner').remove()" class="text-amber-500 hover:text-amber-800 p-1 text-lg rounded-lg transition" title="Tutup">
            <i class="ri-close-line"></i>
        </button>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-900 text-white border-b border-slate-800 text-xs font-bold uppercase tracking-wider">
                        <th class="px-4 py-3.5 border-r border-slate-800">Tanggal Lapor</th>
                        <th class="px-4 py-3.5 border-r border-slate-800">Unit Alkes</th>
                        <th class="px-4 py-3.5 border-r border-slate-800">Ruangan Asal & Lokasi Fisik</th>
                        <th class="px-4 py-3.5 border-r border-slate-800">Jenis Tindakan</th>
                        <th class="px-4 py-3.5 border-r border-slate-800">Gejala / Deskripsi Kerusakan</th>
                        <th class="px-4 py-3.5 border-r border-slate-800 text-center">Status Perbaikan</th>
                        <th class="px-4 py-3.5 text-center">Otoritas Aksi (Elektromedis)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 font-normal text-slate-900">
                    @forelse ($logList as $log)
                        <tr class="hover:bg-amber-50/40 transition odd:bg-white even:bg-slate-50/50">
                            <!-- Tanggal Lapor -->
                            <td class="px-4 py-3.5 border-r border-slate-200 whitespace-nowrap font-medium text-slate-600">
                                {{ $log->tanggal_mulai ? \Carbon\Carbon::parse($log->tanggal_mulai)->translatedFormat('d M Y') : '-' }}
                            </td>

                            <!-- Unit Alkes -->
                            <td class="px-4 py-3.5 border-r border-slate-200">
                                <a href="{{ route('alkes.show', $log->alkes_id) }}" class="font-bold text-slate-900 hover:text-amber-600 transition block">
                                    {{ $log->alkes->nama_barang ?? 'Alkes' }}
                                </a>
                                <span class="text-xs text-slate-500 font-mono">SN: {{ $log->alkes->nomor_seri ?? '-' }}</span>
                            </td>

                            <!-- Ruangan Asal & Lokasi Fisik -->
                            <td class="px-4 py-3.5 border-r border-slate-200">
                                <div class="font-medium text-slate-900">Asal: {{ $log->alkes->ruangan->nama_ruangan ?? '-' }}</div>
                                <div class="text-xs text-amber-700 font-semibold mt-0.5">
                                    Fisik saat ini: {{ $log->alkes->lokasiRuangan->nama_ruangan ?? 'Elektromedis' }}
                                </div>
                            </td>

                            <!-- Jenis Tindakan -->
                            <td class="px-4 py-3.5 border-r border-slate-200">
                                <span class="px-2.5 py-0.5 rounded text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                    {{ $log->jenis_tindakan ?? 'Perbaikan' }}
                                </span>
                            </td>

                            <!-- Gejala / Deskripsi Kerusakan -->
                            <td class="px-4 py-3.5 border-r border-slate-200 text-slate-700 max-w-xs leading-relaxed">
                                {{ $log->deskripsi_kerusakan ?: '-' }}
                            </td>

                            <!-- Status Perbaikan -->
                            <td class="px-4 py-3.5 border-r border-slate-200 text-center">
                                @if ($log->status_hasil === 'Selesai')
                                    <span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-bold border border-emerald-300 inline-flex items-center gap-1">
                                        <i class="ri-checkbox-circle-line"></i> Selesai & Dikembalikan
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-amber-100 text-amber-900 rounded-full text-xs font-bold border border-amber-300 inline-flex items-center gap-1 animate-pulse">
                                        <i class="ri-time-line"></i> Dalam Pengajuan / Perbaikan
                                    </span>
                                @endif
                            </td>

                            <!-- Otoritas Aksi (Hanya Elektromedis) -->
                            <td class="px-4 py-3.5 text-center">
                                @if ($log->status_hasil !== 'Selesai')
                                    @if ($currentRole === 'elektromedis')
                                        <form method="POST" action="{{ route('pemeliharaan.resolve', $log->id) }}" onsubmit="return confirm('Apakah Anda yakin perbaikan telah selesai dan unit akan dikembalikan ke ruangan asal?')">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center justify-center gap-1.5 mx-auto">
                                                <i class="ri-check-double-line text-sm"></i>
                                                Selesaikan & Kembalikan Alat
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200 font-semibold" title="Hanya Ruangan Elektromedis (Admin) yang dapat menyelesaikan perbaikan">
                                            Proses Elektromedis
                                        </span>
                                    @endif
                                @else
                                    <span class="text-xs text-emerald-700 font-semibold flex items-center justify-center gap-1">
                                        <i class="ri-check-line text-emerald-600"></i> Alat Kembali di Ruangan
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-slate-500 text-base">
                                <i class="ri-tools-line text-5xl block mb-3 text-slate-300"></i>
                                Belum ada catatan laporan perbaikan alat kesehatan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
            {{ $logList->links('pagination.custom') }}
        </div>
    </div>

</div>
@endsection
