@extends('layouts.app')

@section('title', 'Riwayat Mutasi Alkes Antar Seksi')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight">Mutasi & Pelacakan Lokasi Alkes</h3>
            <p class="text-sm text-slate-500">Histori serah terima dan pemindahan unit alkes antar seksi/ruangan RS</p>
        </div>
    </div>

    <!-- Mutasi Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs font-bold uppercase tracking-wider">
                        <th class="px-6 py-4">Waktu Mutasi</th>
                        <th class="px-6 py-4">Alat Kesehatan</th>
                        <th class="px-6 py-4">Perpindahan Seksi</th>
                        <th class="px-6 py-4">Pemohon & Penanggung Jawab</th>
                        <th class="px-6 py-4">Alasan Mutasi</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($mutasiList as $mutasi)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-slate-800">{{ $mutasi->tanggal_mutasi ? $mutasi->tanggal_mutasi->format('d M Y') : '-' }}</div>
                                <div class="text-xs text-slate-400">{{ $mutasi->tanggal_mutasi ? $mutasi->tanggal_mutasi->format('H:i') : '' }} WIB</div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="font-bold text-teal-700 text-base">{{ $mutasi->alkes->nomenklatur->nama_alat ?? 'Alkes' }}</div>
                                <div class="text-xs font-mono text-slate-400 mt-0.5">{{ $mutasi->alkes->kode_inventaris ?? '-' }}</div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5 text-xs">
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 font-medium text-slate-700 border border-slate-200">{{ $mutasi->seksiAsal->nama_seksi ?? 'Gudang Utama' }}</span>
                                    <i class="ri-arrow-right-line text-teal-500 font-bold"></i>
                                    <span class="px-2.5 py-1 rounded-lg bg-teal-100 text-teal-800 font-bold border border-teal-200">{{ $mutasi->seksiTujuan->nama_seksi ?? '-' }}</span>
                                </div>
                                <div class="text-[11px] text-slate-400 mt-1">Ruangan: {{ $mutasi->ruanganTujuan->nama_ruangan ?? 'Spesifikasi Seksi' }}</div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800 text-xs">{{ $mutasi->pemohon }}</div>
                                <div class="text-[11px] text-slate-500">PJ: {{ $mutasi->penanggung_jawab }}</div>
                            </td>

                            <td class="px-6 py-4 max-w-xs">
                                <p class="text-xs text-slate-600 truncate" title="{{ $mutasi->alasan_mutasi }}">{{ $mutasi->alasan_mutasi }}</p>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    {{ $mutasi->status_persetujuan ?? 'Disetujui' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                                Belum ada catatan mutasi alat kesehatan.
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
