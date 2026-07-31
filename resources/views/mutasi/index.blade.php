@extends('layouts.app')

@section('title', 'Riwayat Pindah Ruangan Alkes')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight flex items-center gap-2.5">
                <i class="ri-arrow-left-right-line text-teal-600"></i>
                Riwayat Pindah Ruangan Alkes
            </h3>
            <p class="text-sm text-slate-500">Histori pemindahan lokasi fisik unit alkes antar ruangan di Rumah Sakit</p>
        </div>

        <a href="{{ route('mutasi.create') }}" class="px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm rounded-xl shadow-md shadow-teal-600/30 transition flex items-center gap-2">
            <i class="ri-add-line text-lg"></i>
            Proses Pindah Ruangan
        </a>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs font-bold uppercase tracking-wider">
                        <th class="px-6 py-4">Waktu Pemindahan</th>
                        <th class="px-6 py-4">Alat Kesehatan</th>
                        <th class="px-6 py-4">Perpindahan Ruangan</th>
                        <th class="px-6 py-4">Pemohon & Penanggung Jawab</th>
                        <th class="px-6 py-4">Alasan Pemindahan</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($mutasiList as $mutasi)
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- Waktu -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-slate-800">{{ $mutasi->tanggal_mutasi ? $mutasi->tanggal_mutasi->format('d M Y') : '-' }}</div>
                                <div class="text-xs text-slate-400">{{ $mutasi->tanggal_mutasi ? $mutasi->tanggal_mutasi->format('H:i') : '' }} WIB</div>
                            </td>

                            <!-- Alkes -->
                            <td class="px-6 py-4">
                                <div class="font-extrabold text-teal-800 text-base">{{ $mutasi->alkes->nama_barang ?? 'Alkes' }}</div>
                                <div class="text-xs text-slate-500 font-medium mt-0.5">SN: {{ $mutasi->alkes->nomor_seri ?? '-' }}</div>
                            </td>

                            <!-- Perpindahan Ruangan -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5 text-xs">
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 font-medium text-slate-700 border border-slate-200">{{ $mutasi->ruanganAsal->nama_ruangan ?? 'Ruangan Asal' }}</span>
                                    <i class="ri-arrow-right-line text-teal-600 font-bold"></i>
                                    <span class="px-2.5 py-1 rounded-lg bg-teal-100 text-teal-900 font-extrabold border border-teal-200">{{ $mutasi->ruanganTujuan->nama_ruangan ?? 'Ruangan Tujuan' }}</span>
                                </div>
                            </td>

                            <!-- Pemohon & PJ -->
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800 text-xs">{{ $mutasi->pemohon }}</div>
                                <div class="text-[11px] text-slate-500">PJ: {{ $mutasi->penanggung_jawab }}</div>
                            </td>

                            <!-- Alasan -->
                            <td class="px-6 py-4 max-w-xs">
                                <p class="text-xs text-slate-600 truncate" title="{{ $mutasi->alasan_mutasi }}">{{ $mutasi->alasan_mutasi }}</p>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    {{ $mutasi->status_persetujuan ?? 'Disetujui' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                                Belum ada riwayat pemindahan ruangan alat kesehatan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
            {{ $mutasiList->links() }}
        </div>
    </div>

</div>
@endsection
