@extends('layouts.app')

@section('title', 'Log Perbaikan & Kalibrasi Alkes')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight flex items-center gap-2.5">
                <i class="ri-tools-line text-amber-600"></i>
                Log Pemeliharaan, Perbaikan & Kalibrasi Alkes
            </h3>
            <p class="text-sm text-slate-500">Histori pemeliharaan medis, kalibrasi tahunan BPFK, dan perbaikan unit alkes</p>
        </div>

        <a href="{{ route('pemeliharaan.create') }}" class="px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-sm rounded-xl shadow-md shadow-amber-600/30 transition flex items-center gap-2">
            <i class="ri-add-line text-lg"></i>
            Input Perbaikan / Kalibrasi
        </a>
    </div>

    <!-- Log Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs font-bold uppercase tracking-wider">
                        <th class="px-6 py-4">Waktu Mulai</th>
                        <th class="px-6 py-4">Alat Kesehatan (Ruangan)</th>
                        <th class="px-6 py-4">Jenis & Pelaksana</th>
                        <th class="px-6 py-4">Deskripsi & Perbaikan</th>
                        <th class="px-6 py-4 text-center">Status Hasil</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($logList as $log)
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- Tanggal -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-slate-800">{{ $log->tanggal_mulai ? $log->tanggal_mulai->format('d M Y') : '-' }}</div>
                                <div class="text-xs text-slate-400">Selesai: {{ $log->tanggal_selesai ? $log->tanggal_selesai->format('d M Y') : 'Proses' }}</div>
                            </td>

                            <!-- Alkes & Ruangan -->
                            <td class="px-6 py-4">
                                <div class="font-extrabold text-slate-900 text-base">{{ $log->alkes->nama_barang ?? 'Alkes' }}</div>
                                <div class="text-xs text-slate-500 font-mono mt-0.5">SN: {{ $log->alkes->nomor_seri ?? '-' }}</div>
                                <div class="text-[11px] font-bold text-teal-700 mt-1">
                                    <i class="ri-building-line"></i> Ruangan: {{ $log->alkes->ruangan->nama_ruangan ?? 'Ruangan RS' }}
                                </div>
                            </td>

                            <!-- Jenis & Pelaksana -->
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded text-xs font-extrabold bg-amber-100 text-amber-900 border border-amber-200">
                                    {{ $log->jenis_tindakan }}
                                </span>
                                <div class="text-xs text-slate-600 mt-1">Pelaksana: {{ $log->pelaksana_vendor ?? 'Teknisi ATEM' }}</div>
                            </td>

                            <!-- Deskripsi & Perbaikan -->
                            <td class="px-6 py-4 max-w-xs">
                                <div class="font-semibold text-slate-800 text-xs truncate" title="{{ $log->deskripsi_kerusakan }}">{{ $log->deskripsi_kerusakan ?? '-' }}</div>
                                <div class="text-xs text-slate-500 truncate mt-0.5" title="{{ $log->tindakan_perbaikan }}">{{ $log->tindakan_perbaikan ?? '-' }}</div>
                            </td>

                            <!-- Status Hasil -->
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-extrabold {{ $log->status_hasil === 'Selesai' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-amber-100 text-amber-800 border border-amber-200' }}">
                                    {{ $log->status_hasil }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                                Belum ada catatan log pemeliharaan / kalibrasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
            {{ $logList->links() }}
        </div>
    </div>

</div>
@endsection
