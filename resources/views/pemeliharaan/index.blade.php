@extends('layouts.app')

@section('title', 'Perbaikan Alkes')

@section('content')
<div class="space-y-5">

    @php
        $currentRole = session('user_role', 'elektromedis');
    @endphp

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h3 class="text-xl font-bold text-slate-900 tracking-tight">Perbaikan Alkes</h3>
            <p class="text-xs text-slate-500 mt-0.5">Pelaporan alkes rusak, penanganan, dan riwayat perbaikan</p>
        </div>
        <a href="{{ route('pemeliharaan.create') }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-medium text-sm rounded-lg transition flex items-center gap-1.5 shrink-0">
            <i class="ri-add-line text-base"></i>
            Lapor Kerusakan
        </a>
    </div>

    <div class="bg-white p-4 rounded-xl border border-slate-200">
        <form method="GET" action="{{ route('pemeliharaan.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Cari Unit / SN / Deskripsi</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Kata kunci..." class="w-full pl-9 pr-4 h-10 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition">
                    <i class="ri-search-line absolute left-3 top-2.5 text-slate-400 text-sm"></i>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Jenis Tindakan</label>
                <select name="jenis_tindakan" class="w-full px-3 h-10 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400">
                    <option value="">-- Semua --</option>
                    <option value="Perbaikan (Korektif)" {{ request('jenis_tindakan') == 'Perbaikan (Korektif)' ? 'selected' : '' }}>Perbaikan (Korektif)</option>
                    <option value="Kalibrasi Alat" {{ request('jenis_tindakan') == 'Kalibrasi Alat' ? 'selected' : '' }}>Kalibrasi Alat</option>
                    <option value="Pemeliharaan Rutin" {{ request('jenis_tindakan') == 'Pemeliharaan Rutin' ? 'selected' : '' }}>Pemeliharaan Rutin</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Status</label>
                <div class="flex items-center gap-2">
                    <select name="status_hasil" class="w-full px-3 h-10 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400">
                        <option value="">-- Semua --</option>
                        <option value="Proses" {{ request('status_hasil') == 'Proses' ? 'selected' : '' }}>Dalam Perbaikan</option>
                        <option value="Selesai" {{ request('status_hasil') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                    <button type="submit" class="h-10 px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-lg transition flex items-center shrink-0">
                        <i class="ri-search-line"></i>
                    </button>
                    @if (request()->hasAny(['search', 'jenis_tindakan', 'status_hasil']))
                        <a href="{{ route('pemeliharaan.index') }}" class="h-10 w-10 bg-slate-100 hover:bg-slate-200 text-slate-500 rounded-lg border border-slate-200 transition flex items-center justify-center shrink-0">
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
                        <th class="px-3 py-3">Tanggal</th>
                        <th class="px-3 py-3">Unit Alkes</th>
                        <th class="px-3 py-3">Ruangan & Lokasi</th>
                        <th class="px-3 py-3">Jenis</th>
                        <th class="px-3 py-3">Deskripsi Kerusakan</th>
                        <th class="px-3 py-3 text-center">Status</th>
                        <th class="px-3 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-[13px]">
                    @forelse ($logList as $log)
                        <tr class="hover:bg-indigo-50/30 transition">
                            <td class="px-3 py-2.5 whitespace-nowrap text-slate-500 text-xs font-medium">
                                {{ $log->tanggal_mulai ? \Carbon\Carbon::parse($log->tanggal_mulai)->translatedFormat('d M Y') : '-' }}
                            </td>

                            <td class="px-3 py-2.5">
                                <a href="{{ route('alkes.show', $log->alkes_id) }}" class="font-medium text-slate-800 hover:text-indigo-600 transition block text-sm">
                                    {{ $log->alkes->nama_barang ?? 'Alkes' }}
                                </a>
                                <span class="text-[10px] text-slate-400 font-mono">SN: {{ $log->alkes->nomor_seri ?? '-' }}</span>
                            </td>

                            <td class="px-3 py-2.5">
                                <div class="text-xs text-slate-700 font-medium">{{ $log->alkes->ruangan->nama_ruangan ?? '-' }}</div>
                                <div class="text-[10px] text-indigo-600 font-medium mt-0.5">
                                    Fisik: {{ $log->alkes->lokasiRuangan->nama_ruangan ?? 'Elektromedis' }}
                                </div>
                            </td>

                            <td class="px-3 py-2.5">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    {{ $log->jenis_tindakan ?? 'Perbaikan' }}
                                </span>
                            </td>

                            <td class="px-3 py-2.5 text-slate-600 text-xs max-w-[200px] leading-relaxed">
                                {{ $log->deskripsi_kerusakan ?: '-' }}
                            </td>

                            <td class="px-3 py-2.5 text-center">
                                @if ($log->status_hasil === 'Selesai')
                                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded-full text-[10px] font-semibold border border-emerald-200 inline-flex items-center gap-1">
                                        <i class="ri-checkbox-circle-line text-[10px]"></i> Selesai
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 bg-amber-50 text-amber-700 rounded-full text-[10px] font-semibold border border-amber-200 inline-flex items-center gap-1">
                                        <i class="ri-time-line text-[10px]"></i> Proses
                                    </span>
                                @endif
                            </td>

                            <td class="px-3 py-2.5 text-center">
                                @if ($log->status_hasil !== 'Selesai')
                                    @if ($currentRole === 'elektromedis')
                                        <form method="POST" action="{{ route('pemeliharaan.resolve', $log->id) }}" onsubmit="return confirm('Selesaikan perbaikan dan kembalikan unit ke ruangan asal?')">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-medium rounded-lg transition flex items-center gap-1 mx-auto">
                                                <i class="ri-check-double-line text-xs"></i> Selesaikan
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[10px] text-amber-600 bg-amber-50 px-2 py-0.5 rounded border border-amber-200 font-medium">Proses Elektromedis</span>
                                    @endif
                                @else
                                    <span class="text-[10px] text-emerald-600 font-medium flex items-center justify-center gap-1">
                                        <i class="ri-check-line"></i> Kembali
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <i class="ri-tools-line text-3xl block mb-2 text-slate-300"></i>
                                <span class="text-sm">Belum ada laporan perbaikan.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 bg-slate-50/50 border-t border-slate-100">
            {{ $logList->links('pagination.custom') }}
        </div>
    </div>

</div>
@endsection
