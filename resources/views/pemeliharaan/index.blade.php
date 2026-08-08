@extends('layouts.app')

@section('title', 'Perbaikan Alkes')

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
                Perbaikan Alkes
            </h3>
            <p class="text-base text-slate-600 mt-1 font-normal">
                Pelaporan alkes rusak dari ruangan operasional, penanganan unit di Ruangan Elektromedis, dan riwayat perbaikan
            </p>
        </div>

        <a href="{{ route('pemeliharaan.create') }}" class="px-5 py-3 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-base rounded-xl shadow-md shadow-amber-600/30 transition flex items-center gap-2">
            <i class="ri-add-line text-xl"></i>
            Lapor Barang Rusak / Perbaikan
        </a>
    </div>

    <!-- Search & Filter Card -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('pemeliharaan.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-sm font-semibold text-slate-800 mb-1.5">Cari Unit / SN / Deskripsi Kerusakan</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Masukkan kata kunci..." class="w-full pl-10 pr-4 h-[46px] bg-slate-50 border border-slate-300 rounded-xl text-base font-normal text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <i class="ri-search-line absolute left-3.5 top-3.5 text-slate-400 text-lg"></i>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-800 mb-1.5">Jenis Tindakan</label>
                <select name="jenis_tindakan" class="w-full px-4 h-[46px] bg-slate-50 border border-slate-300 rounded-xl text-base font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">-- Semua Jenis Tindakan --</option>
                    <option value="Perbaikan (Korektif)" {{ request('jenis_tindakan') == 'Perbaikan (Korektif)' ? 'selected' : '' }}>Perbaikan (Korektif)</option>
                    <option value="Kalibrasi Alat" {{ request('jenis_tindakan') == 'Kalibrasi Alat' ? 'selected' : '' }}>Kalibrasi Alat</option>
                    <option value="Pemeliharaan Rutin" {{ request('jenis_tindakan') == 'Pemeliharaan Rutin' ? 'selected' : '' }}>Pemeliharaan Rutin</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-800 mb-1.5">Status Perbaikan</label>
                <div class="flex items-center gap-2">
                    <select name="status_hasil" class="w-full px-4 h-[46px] bg-slate-50 border border-slate-300 rounded-xl text-base font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="">-- Semua Status --</option>
                        <option value="Proses" {{ request('status_hasil') == 'Proses' ? 'selected' : '' }}>Dalam Pengajuan / Perbaikan</option>
                        <option value="Selesai" {{ request('status_hasil') == 'Selesai' ? 'selected' : '' }}>Selesai & Dikembalikan</option>
                    </select>

                    <button type="submit" class="h-[46px] px-6 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-sm rounded-xl shadow-xs transition flex items-center justify-center gap-2 shrink-0">
                        <i class="ri-search-line text-lg"></i> Cari
                    </button>

                    @if (request()->hasAny(['search', 'jenis_tindakan', 'status_hasil']))
                        <a href="{{ route('pemeliharaan.index') }}" class="h-[46px] w-[46px] bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-700 rounded-xl border border-slate-300 transition flex items-center justify-center shrink-0" title="Reset Filter">
                            <i class="ri-refresh-line text-xl"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xl shadow-slate-200/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-teal-950 via-teal-900 to-teal-950 text-white border-b border-teal-800 text-xs font-bold uppercase tracking-wider">
                        <th class="px-4 py-3.5 border-r border-teal-700/60">Tanggal Lapor</th>
                        <th class="px-4 py-3.5 border-r border-teal-700/60">Unit Alkes</th>
                        <th class="px-4 py-3.5 border-r border-teal-700/60">Ruangan Asal & Lokasi Fisik</th>
                        <th class="px-4 py-3.5 border-r border-teal-700/60">Jenis Tindakan</th>
                        <th class="px-4 py-3.5 border-r border-teal-700/60">Gejala / Deskripsi Kerusakan</th>
                        <th class="px-4 py-3.5 border-r border-teal-700/60 text-center">Status Perbaikan</th>
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
