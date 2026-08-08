@extends('layouts.app')

@section('title', 'Riwayat Pindah Ruangan Alkes')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <i class="ri-arrow-left-right-line text-teal-600"></i>
                Riwayat Pindah Ruangan Alkes
            </h3>
            <p class="text-base text-slate-600 mt-1 font-normal">Histori pemindahan lokasi fisik unit alkes antar ruangan di Rumah Sakit</p>
        </div>

        <a href="{{ route('mutasi.create') }}" class="px-5 py-3 bg-teal-600 hover:bg-teal-700 text-white font-semibold text-base rounded-xl shadow-md shadow-teal-600/30 transition flex items-center gap-2">
            <i class="ri-add-line text-xl"></i>
            Proses Pindah Ruangan
        </a>
    </div>

    <!-- Search & Filter Card -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('mutasi.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-sm font-semibold text-slate-800 mb-1.5">Cari Unit / SN / Pemohon / Alasan</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Masukkan kata kunci..." class="w-full pl-10 pr-4 h-[46px] bg-slate-50 border border-slate-300 rounded-xl text-base font-normal text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <i class="ri-search-line absolute left-3.5 top-3.5 text-slate-400 text-lg"></i>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-800 mb-1.5">Ruangan Asal</label>
                <select name="ruangan_asal_id" class="w-full px-4 h-[46px] bg-slate-50 border border-slate-300 rounded-xl text-base font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <option value="">-- Semua Ruangan Asal --</option>
                    @foreach ($ruanganList as $ruang)
                        <option value="{{ $ruang->id }}" {{ request('ruangan_asal_id') == $ruang->id ? 'selected' : '' }}>{{ $ruang->nama_ruangan }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-800 mb-1.5">Ruangan Tujuan</label>
                <div class="flex items-center gap-2">
                    <select name="ruangan_tujuan_id" class="w-full px-4 h-[46px] bg-slate-50 border border-slate-300 rounded-xl text-base font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <option value="">-- Semua Ruangan Tujuan --</option>
                        @foreach ($ruanganList as $ruang)
                            <option value="{{ $ruang->id }}" {{ request('ruangan_tujuan_id') == $ruang->id ? 'selected' : '' }}>{{ $ruang->nama_ruangan }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="h-[46px] px-6 bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm rounded-xl shadow-xs transition flex items-center justify-center gap-2 shrink-0">
                        <i class="ri-search-line text-lg"></i> Cari
                    </button>

                    @if (request()->hasAny(['search', 'ruangan_asal_id', 'ruangan_tujuan_id']))
                        <a href="{{ route('mutasi.index') }}" class="h-[46px] w-[46px] bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-700 rounded-xl border border-slate-300 transition flex items-center justify-center shrink-0" title="Reset Filter">
                            <i class="ri-refresh-line text-xl"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-teal-800 text-white border-b border-teal-900 text-xs font-bold uppercase tracking-wider">
                        <th class="px-6 py-4 border-r border-teal-700/60">Waktu Pemindahan</th>
                        <th class="px-6 py-4 border-r border-teal-700/60">Alat Kesehatan</th>
                        <th class="px-6 py-4 border-r border-teal-700/60">Perpindahan Ruangan</th>
                        <th class="px-6 py-4 border-r border-teal-700/60">Pemohon & Penanggung Jawab</th>
                        <th class="px-6 py-4 border-r border-teal-700/60">Alasan Pemindahan</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 font-normal text-slate-900">
                    @forelse ($mutasiList as $mutasi)
                        <tr class="hover:bg-teal-50/50 transition odd:bg-white even:bg-slate-50/50">
                            <!-- Waktu -->
                            <td class="px-6 py-4 whitespace-nowrap border-r border-slate-200">
                                <div class="font-bold text-slate-800">{{ $mutasi->tanggal_mutasi ? $mutasi->tanggal_mutasi->format('d M Y') : '-' }}</div>
                                <div class="text-xs text-slate-500 font-mono">{{ $mutasi->tanggal_mutasi ? $mutasi->tanggal_mutasi->format('H:i') : '' }} WIB</div>
                            </td>

                            <!-- Alkes -->
                            <td class="px-6 py-4 border-r border-slate-200">
                                <div class="font-extrabold text-teal-800 text-base">{{ $mutasi->alkes->nama_barang ?? 'Alkes' }}</div>
                                <div class="text-xs text-slate-500 font-mono mt-0.5">SN: {{ $mutasi->alkes->nomor_seri ?? '-' }}</div>
                            </td>

                            <!-- Perpindahan Ruangan -->
                            <td class="px-6 py-4 border-r border-slate-200">
                                <div class="flex items-center gap-1.5 text-xs">
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 font-medium text-slate-700 border border-slate-200">{{ $mutasi->ruanganAsal->nama_ruangan ?? 'Ruangan Asal' }}</span>
                                    <i class="ri-arrow-right-line text-teal-600 font-bold"></i>
                                    <span class="px-2.5 py-1 rounded-lg bg-teal-100 text-teal-900 font-extrabold border border-teal-200">{{ $mutasi->ruanganTujuan->nama_ruangan ?? 'Ruangan Tujuan' }}</span>
                                </div>
                            </td>

                            <!-- Pemohon & PJ -->
                            <td class="px-6 py-4 border-r border-slate-200">
                                <div class="font-semibold text-slate-800 text-xs">{{ $mutasi->pemohon }}</div>
                                <div class="text-[11px] text-slate-500">PJ: {{ $mutasi->penanggung_jawab }}</div>
                            </td>

                            <!-- Alasan -->
                            <td class="px-6 py-4 border-r border-slate-200 max-w-xs">
                                <p class="text-xs text-slate-600 leading-relaxed" title="{{ $mutasi->alasan_mutasi }}">{{ $mutasi->alasan_mutasi }}</p>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200 inline-flex items-center gap-1">
                                    <i class="ri-checkbox-circle-line"></i> {{ $mutasi->status_persetujuan ?? 'Disetujui' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-slate-400">
                                <i class="ri-arrow-left-right-line text-5xl block mb-3 text-slate-300"></i>
                                Belum ada riwayat pemindahan ruangan alat kesehatan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
            {{ $mutasiList->links('pagination.custom') }}
        </div>
    </div>

</div>
@endsection
