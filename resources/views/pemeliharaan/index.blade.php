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

    <!-- Notification Info Card -->
    <div class="bg-amber-50 p-5 rounded-2xl border border-amber-200 shadow-xs flex items-start gap-4">
        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 mt-0.5">
            <i class="ri-shield-user-line text-2xl"></i>
        </div>
        <div class="flex-1">
            <h4 class="font-bold text-amber-900 text-base">Alur Otoritas Pelaporan & Perbaikan Ruangan Elektromedis</h4>
            <p class="text-sm text-amber-800 mt-1 leading-relaxed">
                Setiap kali **Ruangan Operasional** (seperti ICU, IGD, OK, dll) melapor barang rusak, **lokasi fisik unit alkes otomatis dipindahkan ke Ruangan Elektromedis**. Notifikasi laporan akan dikirim ke Elektromedis. **Hanya Ruangan Elektromedis** yang berwenang memperbarui status perbaikan menjadi <em>'Selesai'</em> dan mengembalikan unit ke ruangan asalnya.
            </p>
        </div>
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
                            <td class="px-4 py-3.5 border-r border-slate-200 whitespace-nowrap">
                                <div class="font-semibold text-slate-900 text-sm">{{ $log->tanggal_mulai->format('d M Y') }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">Tgl Selesai: {{ $log->tanggal_selesai ? $log->tanggal_selesai->format('d M Y') : '-' }}</div>
                            </td>

                            <!-- Unit Alkes -->
                            <td class="px-4 py-3.5 border-r border-slate-200">
                                <div class="font-semibold text-slate-900 text-sm">{{ $log->alkes->nama_barang ?? 'Alkes' }}</div>
                                <div class="text-xs font-mono text-slate-500 mt-0.5">SN: {{ $log->alkes->nomor_seri ?? '-' }}</div>
                            </td>

                            <!-- Ruangan Asal & Lokasi Fisik -->
                            <td class="px-4 py-3.5 border-r border-slate-200">
                                <div class="font-semibold text-slate-900 text-sm">Ruang Asal: {{ $log->alkes->ruangan->nama_ruangan ?? '-' }}</div>
                                <div class="text-xs mt-1">
                                    <span class="px-2 py-0.5 rounded font-semibold bg-amber-100 text-amber-900 border border-amber-300">
                                        Lokasi Fisik: {{ $log->alkes->lokasiRuangan->nama_ruangan ?? 'Elektromedis' }}
                                    </span>
                                </div>
                            </td>

                            <!-- Jenis Tindakan -->
                            <td class="px-4 py-3.5 border-r border-slate-200 font-medium text-slate-800">
                                {{ $log->jenis_tindakan }}
                            </td>

                            <!-- Gejala / Deskripsi -->
                            <td class="px-4 py-3.5 border-r border-slate-200 max-w-xs">
                                <p class="text-xs text-slate-700 leading-relaxed">{{ $log->deskripsi_kerusakan ?: '-' }}</p>
                                @if ($log->tindakan_perbaikan)
                                    <p class="text-xs text-emerald-800 font-medium mt-1"><strong>Solusi:</strong> {{ $log->tindakan_perbaikan }}</p>
                                @endif
                            </td>

                            <!-- Status Perbaikan -->
                            <td class="px-4 py-3.5 border-r border-slate-200 text-center">
                                @if ($log->status_hasil === 'Selesai')
                                    <span class="px-3 py-1 rounded text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 inline-block">
                                        <i class="ri-checkbox-circle-line"></i> Selesai (Dikembalikan)
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300 inline-block animate-pulse">
                                        <i class="ri-time-line"></i> Dalam Proses (Di Elektromedis)
                                    </span>
                                @endif
                            </td>

                            <!-- Otoritas Aksi (Elektromedis) -->
                            <td class="px-4 py-3.5 text-center">
                                @if ($log->status_hasil === 'Proses')
                                    @if ($currentRole === 'elektromedis')
                                        <form method="POST" action="{{ route('pemeliharaan.resolve', $log->id) }}">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Apakah Anda yakin perbaikan telah SELESAI? Unit alkes akan otomatis dikembalikan ke ruangan asalnya ({{ $log->alkes->ruangan->nama_ruangan ?? 'Ruangan Asal' }}).')" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center gap-1.5 mx-auto">
                                                <i class="ri-check-double-line text-sm"></i>
                                                Selesaikan & Kembalikan Alat
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Khusus Elektromedis</span>
                                    @endif
                                @else
                                    <span class="text-xs text-emerald-700 font-semibold flex items-center justify-center gap-1">
                                        <i class="ri-check-line"></i> Sudah Dikembalikan
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-slate-500 text-base">
                                Belum ada catatan laporan perbaikan.
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
