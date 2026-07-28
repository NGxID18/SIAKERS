@extends('layouts.app')

@section('title', 'Detail Alkes - ' . ($alkes->nomenklatur->nama_alat ?? 'Alkes'))

@section('content')
<div class="space-y-6">

    <!-- Top Navigation & Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('alkes.index') }}" class="p-2.5 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition text-slate-600">
                <i class="ri-arrow-left-line text-lg"></i>
            </a>
            <div>
                <span class="text-xs font-mono px-2 py-0.5 rounded bg-slate-200 text-slate-700 font-bold uppercase">{{ $alkes->kode_inventaris }}</span>
                <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight mt-1">{{ $alkes->nomenklatur->nama_alat ?? 'Alat Kesehatan' }}</h3>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if ($alkes->seksi_id == $userSeksiId)
                <a href="{{ route('mutasi.create', ['alkes_id' => $alkes->id]) }}" class="px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm rounded-xl transition shadow flex items-center gap-2">
                    <i class="ri-arrow-left-right-line"></i>
                    Mutasi Seksi
                </a>
                <a href="{{ route('pemeliharaan.create', ['alkes_id' => $alkes->id]) }}" class="px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-sm rounded-xl transition shadow flex items-center gap-2">
                    <i class="ri-tools-line"></i>
                    Lapor Perbaikan
                </a>
                <a href="{{ route('alkes.edit', $alkes->id) }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition" title="Edit Data">
                    <i class="ri-edit-line text-lg"></i>
                </a>
            @endif
        </div>
    </div>

    <!-- Main Detail Info Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Informasi Utama -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-6">
            <h4 class="font-bold text-lg text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
                <i class="ri-information-line text-teal-600"></i>
                Informasi & Spesifikasi Alat
            </h4>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-xs text-slate-400 font-medium uppercase block">Nama Nomenklatur Standard</span>
                    <span class="font-bold text-slate-800 text-sm mt-1 block">{{ $alkes->nomenklatur->nama_alat ?? '-' }}</span>
                </div>

                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-xs text-slate-400 font-medium uppercase block">Kategori Alat</span>
                    <span class="font-bold text-slate-800 text-sm mt-1 block">{{ $alkes->nomenklatur->kategori ?? '-' }}</span>
                </div>

                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-xs text-slate-400 font-medium uppercase block">Merk / Produsen</span>
                    <span class="font-bold text-slate-800 text-sm mt-1 block">{{ $alkes->merk ?? '-' }}</span>
                </div>

                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-xs text-slate-400 font-medium uppercase block">Model / Tipe</span>
                    <span class="font-bold text-slate-800 text-sm mt-1 block">{{ $alkes->tipe ?? '-' }}</span>
                </div>

                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-xs text-slate-400 font-medium uppercase block">Nomor Seri (Serial Number)</span>
                    <span class="font-mono font-bold text-slate-800 text-sm mt-1 block">{{ $alkes->nomor_seri ?? '-' }}</span>
                </div>

                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-xs text-slate-400 font-medium uppercase block">Tanggal Pengadaan</span>
                    <span class="font-bold text-slate-800 text-sm mt-1 block">{{ $alkes->tanggal_pengadaan ? $alkes->tanggal_pengadaan->format('d M Y') : '-' }}</span>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <span class="text-xs text-slate-400 font-medium uppercase block mb-1">Catatan Tambahan & Kondisi Operasional</span>
                <p class="text-sm text-slate-700 bg-slate-50 p-4 rounded-xl border border-slate-100 leading-relaxed">
                    {{ $alkes->catatan ?? 'Tidak ada catatan tambahan untuk unit alat kesehatan ini.' }}
                </p>
            </div>
        </div>

        <!-- Kolom Kanan: Status & Lokasi -->
        <div class="space-y-6">

            <!-- Card Status & Lokasi -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
                <h4 class="font-bold text-base text-slate-800 border-b border-slate-100 pb-3">Status & Penempatan</h4>

                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-xs text-slate-400 block">Status Penggunaan:</span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border mt-1 {{ $alkes->status_enum->warnaBadge() }}">
                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                            {{ $alkes->status_enum->label() }}
                        </span>
                    </div>

                    <div>
                        <span class="text-xs text-slate-400 block">Kondisi Fisik:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold border mt-1 {{ $alkes->kondisi_enum->warnaBadge() }}">
                            {{ $alkes->kondisi_enum->label() }}
                        </span>
                    </div>

                    <div class="pt-2 border-t border-slate-100">
                        <span class="text-xs text-slate-400 block">Lokasi Seksi Operasional:</span>
                        <span class="font-bold text-slate-800 block mt-0.5">{{ $alkes->seksi->nama_seksi ?? '-' }}</span>
                    </div>

                    <div>
                        <span class="text-xs text-slate-400 block">Ruangan Spesifik:</span>
                        <span class="font-bold text-slate-800 block mt-0.5">{{ $alkes->ruangan->nama_ruangan ?? 'Tanpa Spesifikasi Ruangan' }}</span>
                    </div>
                </div>
            </div>

            <!-- Card Riwayat Pemeliharaan & Mutasi -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-3">
                <h4 class="font-bold text-base text-slate-800 border-b border-slate-100 pb-3">Riwayat Terkait Unit</h4>

                <div class="space-y-2 text-xs">
                    <p class="text-slate-500"><strong>Riwayat Mutasi:</strong> {{ $alkes->mutasi->count() }} kali dipindahkan</p>
                    <p class="text-slate-500"><strong>Log Perbaikan:</strong> {{ $alkes->logPemeliharaan->count() }} catatan pemeliharaan</p>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
