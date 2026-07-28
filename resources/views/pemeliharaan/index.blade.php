@extends('layouts.app')

@section('title', 'Pemeliharaan & Perbaikan Alkes')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight">Log Pemeliharaan, Service & Kalibrasi</h3>
            <p class="text-sm text-slate-500">Pencatatan kerusakan, jadwal kalibrasi, dan penanganan teknis alat medis</p>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs font-bold uppercase tracking-wider">
                        <th class="px-6 py-4">Tanggal & Jenis</th>
                        <th class="px-6 py-4">Alat Kesehatan</th>
                        <th class="px-6 py-4">Pelaksana Vendor / ATEM</th>
                        <th class="px-6 py-4">Deskripsi Kerusakan</th>
                        <th class="px-6 py-4 text-center">Status Hasil</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($logList as $log)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-slate-800">{{ $log->jenis_tindakan }}</div>
                                <div class="text-xs text-slate-400 mt-0.5"><i class="ri-calendar-line"></i> {{ $log->tanggal_mulai ? $log->tanggal_mulai->format('d M Y') : '-' }}</div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="font-bold text-teal-700 text-base">{{ $log->alkes->nomenklatur->nama_alat ?? 'Alkes' }}</div>
                                <div class="text-xs font-mono text-slate-400 mt-0.5">{{ $log->alkes->kode_inventaris ?? '-' }}</div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800 text-xs">{{ $log->pelaksana_vendor ?? 'Teknisi ATEM RS' }}</div>
                            </td>

                            <td class="px-6 py-4 max-w-xs">
                                <p class="text-xs text-slate-600 truncate" title="{{ $log->deskripsi_kerusakan }}">{{ $log->deskripsi_kerusakan ?? '-' }}</p>
                            </td>

                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                @if ($log->status_hasil == 'Selesai')
                                    <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">Selesai</span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-200">Proses</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                                Belum ada riwayat log pemeliharaan alkes.
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
