@extends('layouts.app')

@section('title', 'Riwayat Pindah Ruangan Alkes')

@section('content')
<div class="space-y-5">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h3 class="text-xl font-bold text-slate-900 tracking-tight">Riwayat Pindah Ruangan</h3>
            <p class="text-xs text-slate-500 mt-0.5">Histori pemindahan lokasi fisik unit alkes antar ruangan</p>
        </div>
        <a href="{{ route('mutasi.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-lg transition flex items-center gap-1.5 shrink-0">
            <i class="ri-add-line text-base"></i>
            Pindah Ruangan
        </a>
    </div>

    <div class="bg-white p-4 rounded-xl border border-slate-200">
        <form method="GET" action="{{ route('mutasi.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Cari Unit / SN / Pemohon</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Kata kunci..." class="w-full pl-9 pr-4 h-10 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition">
                    <i class="ri-search-line absolute left-3 top-2.5 text-slate-400 text-sm"></i>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Ruangan Asal</label>
                <select name="ruangan_asal_id" class="w-full px-3 h-10 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400">
                    <option value="">-- Semua --</option>
                    @foreach ($ruanganList as $ruang)
                        <option value="{{ $ruang->id }}" {{ request('ruangan_asal_id') == $ruang->id ? 'selected' : '' }}>{{ $ruang->nama_ruangan }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Ruangan Tujuan</label>
                <div class="flex items-center gap-2">
                    <select name="ruangan_tujuan_id" class="w-full px-3 h-10 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400">
                        <option value="">-- Semua --</option>
                        @foreach ($ruanganList as $ruang)
                            <option value="{{ $ruang->id }}" {{ request('ruangan_tujuan_id') == $ruang->id ? 'selected' : '' }}>{{ $ruang->nama_ruangan }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="h-10 px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-lg transition flex items-center shrink-0">
                        <i class="ri-search-line"></i>
                    </button>
                    @if (request()->hasAny(['search', 'ruangan_asal_id', 'ruangan_tujuan_id']))
                        <a href="{{ route('mutasi.index') }}" class="h-10 w-10 bg-slate-100 hover:bg-slate-200 text-slate-500 rounded-lg border border-slate-200 transition flex items-center justify-center shrink-0">
                            <i class="ri-refresh-line text-sm"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 border-b border-slate-200 text-[11px] font-semibold uppercase tracking-wider">
                        <th class="px-3 py-3">Waktu</th>
                        <th class="px-3 py-3">Alat Kesehatan</th>
                        <th class="px-3 py-3">Perpindahan</th>
                        <th class="px-3 py-3">Pemohon & PJ</th>
                        <th class="px-3 py-3">Alasan</th>
                        <th class="px-3 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-[13px]">
                    @forelse ($mutasiList as $mutasi)
                        <tr class="hover:bg-indigo-50/30 transition">
                            <td class="px-3 py-2.5 whitespace-nowrap">
                                <div class="font-medium text-slate-800 text-xs">{{ $mutasi->tanggal_mutasi ? $mutasi->tanggal_mutasi->format('d M Y') : '-' }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $mutasi->tanggal_mutasi ? $mutasi->tanggal_mutasi->format('H:i') : '' }} WIB</div>
                            </td>

                            <td class="px-3 py-2.5">
                                <div class="font-medium text-slate-800 text-sm">{{ $mutasi->alkes->nama_barang ?? 'Alkes' }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">SN: {{ $mutasi->alkes->nomor_seri ?? '-' }}</div>
                            </td>

                            <td class="px-3 py-2.5">
                                <div class="flex items-center gap-1.5 text-[11px]">
                                    <span class="px-2 py-0.5 rounded bg-slate-50 text-slate-600 font-medium border border-slate-200">{{ $mutasi->ruanganAsal->nama_ruangan ?? 'Asal' }}</span>
                                    <i class="ri-arrow-right-line text-indigo-400 text-xs"></i>
                                    <span class="px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 font-semibold border border-indigo-200">{{ $mutasi->ruanganTujuan->nama_ruangan ?? 'Tujuan' }}</span>
                                </div>
                            </td>

                            <td class="px-3 py-2.5">
                                <div class="font-medium text-slate-700 text-xs">{{ $mutasi->pemohon }}</div>
                                <div class="text-[10px] text-slate-400">PJ: {{ $mutasi->penanggung_jawab }}</div>
                            </td>

                            <td class="px-3 py-2.5 max-w-[200px]">
                                <p class="text-xs text-slate-500 leading-relaxed" title="{{ $mutasi->alasan_mutasi }}">{{ $mutasi->alasan_mutasi }}</p>
                            </td>

                            <td class="px-3 py-2.5 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 inline-flex items-center gap-1">
                                    <i class="ri-checkbox-circle-line text-[10px]"></i> {{ $mutasi->status_persetujuan ?? 'Disetujui' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <i class="ri-arrow-left-right-line text-3xl block mb-2 text-slate-300"></i>
                                <span class="text-sm">Belum ada riwayat pemindahan.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 bg-slate-50/50 border-t border-slate-100">
            {{ $mutasiList->links('pagination.custom') }}
        </div>
    </div>

</div>
@endsection
