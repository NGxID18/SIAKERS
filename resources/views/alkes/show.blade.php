@extends('layouts.app')

@section('title', 'Detail Alkes - ' . $alkes->nama_barang)

@section('content')
<div class="space-y-6">

    <!-- Top Navigation & Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('alkes.index') }}" class="p-2.5 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition text-slate-600">
                <i class="ri-arrow-left-line text-lg"></i>
            </a>
            <div>
                <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $alkes->nama_barang }}</h3>
                <span class="text-xs text-slate-500 font-medium">Merk: {{ $alkes->merk ?? '-' }} | Tipe: {{ $alkes->tipe ?? '-' }}</span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('mutasi.create', ['alkes_id' => $alkes->id]) }}" class="px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm rounded-xl transition shadow flex items-center gap-2">
                <i class="ri-arrow-left-right-line"></i>
                Pindah Ruangan
            </a>
            <a href="{{ route('pemeliharaan.create', ['alkes_id' => $alkes->id]) }}" class="px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-sm rounded-xl transition shadow flex items-center gap-2">
                <i class="ri-tools-line"></i>
                Lapor Perbaikan
            </a>
            <a href="{{ route('alkes.edit', $alkes->id) }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition" title="Edit Data">
                <i class="ri-edit-line text-lg"></i>
            </a>
        </div>
    </div>

    <!-- Main Detail Info Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Informasi Utama -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-6">
            <h4 class="font-bold text-lg text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
                <i class="ri-information-line text-teal-600"></i>
                Informasi & Spesifikasi Alat (Data Asli RS)
            </h4>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-xs text-slate-400 font-medium uppercase block">Nama Barang / Alat</span>
                    <span class="font-extrabold text-slate-900 text-base mt-1 block">{{ $alkes->nama_barang }}</span>
                </div>

                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-xs text-slate-400 font-medium uppercase block">Merk / Produsen</span>
                    <span class="font-bold text-teal-800 text-base mt-1 block">{{ $alkes->merk ?? '-' }}</span>
                </div>

                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-xs text-slate-400 font-medium uppercase block">Model / Tipe</span>
                    <span class="font-bold text-slate-800 text-sm mt-1 block">{{ $alkes->tipe ?? '-' }}</span>
                </div>

                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-xs text-slate-400 font-medium uppercase block">Nomor Seri (Serial Number)</span>
                    <span class="font-mono font-extrabold text-slate-900 text-base mt-1 block">{{ $alkes->nomor_seri ?? '-' }}</span>
                </div>

                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-xs text-slate-400 font-medium uppercase block">Tahun Pengadaan</span>
                    <span class="font-bold text-slate-800 text-sm mt-1 block">{{ $alkes->tahun_pengadaan ?? '-' }}</span>
                </div>

                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-xs text-slate-400 font-medium uppercase block">Jumlah Unit</span>
                    <span class="font-bold text-slate-800 text-sm mt-1 block">{{ $alkes->jumlah }} Unit</span>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <span class="text-xs text-slate-400 font-medium uppercase block mb-1">Catatan Tambahan / Keterangan</span>
                <p class="text-sm text-slate-700 bg-slate-50 p-4 rounded-xl border border-slate-100 leading-relaxed">
                    {{ $alkes->keterangan ?? 'Tidak ada catatan tambahan khusus untuk unit alkes ini.' }}
                </p>
            </div>
        </div>

        <!-- Kolom Kanan: Status, Registrasi & Lokasi -->
        <div class="space-y-6">

            <!-- Card Status & Registrasi -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
                <h4 class="font-bold text-base text-slate-800 border-b border-slate-100 pb-3">Status Registrasi & Kondisi</h4>

                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-xs text-slate-400 block mb-1">Kondisi Fisik Alat:</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-extrabold border {{ $alkes->kondisi_enum->warnaBadge() }}">
                            {{ $alkes->kondisi_enum->label() }}
                        </span>
                    </div>

                    <div>
                        <span class="text-xs text-slate-400 block mb-1">Status Penggunaan:</span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $alkes->status_enum->warnaBadge() }}">
                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                            {{ $alkes->status_enum->label() }}
                        </span>
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-xs text-slate-400">Status ASPAK Kemenkes:</span>
                        <span class="px-2.5 py-0.5 rounded text-xs font-bold {{ $alkes->aspak_status == 'TERDATA' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                            {{ $alkes->aspak_status ?? 'TERDATA' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-400">Kartu Inventaris Barang (KIB):</span>
                        <span class="px-2.5 py-0.5 rounded text-xs font-bold {{ $alkes->kib_status ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-slate-100 text-slate-500' }}">
                            {{ $alkes->kib_status ? 'TERDAFTAR' : 'NON-KIB' }}
                        </span>
                    </div>

                    <div class="pt-2 border-t border-slate-100">
                        <span class="text-xs text-slate-400 block">Ruangan Penempatan Aset:</span>
                        <span class="font-bold text-slate-900 text-base block mt-0.5"><i class="ri-building-line text-slate-500"></i> {{ $alkes->ruangan->nama_ruangan ?? 'Ruangan RS' }}</span>
                    </div>

                    <div>
                        <span class="text-xs text-slate-400 block">Lokasi Fisik Keberadaan Alat:</span>
                        <span class="font-bold text-teal-700 block mt-0.5"><i class="ri-map-pin-line"></i> {{ $alkes->lokasiRuangan->nama_ruangan ?? $alkes->ruangan->nama_ruangan ?? 'Ruangan RS' }}</span>
                    </div>

                    @if ($alkes->lokasi_saat_ini_note)
                        <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-900 font-bold">
                            📌 {{ $alkes->lokasi_saat_ini_note }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Card Riwayat Pemeliharaan & Mutasi -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-3">
                <h4 class="font-bold text-base text-slate-800 border-b border-slate-100 pb-3">Riwayat Terkait Unit</h4>

                <div class="space-y-2 text-xs">
                    <p class="text-slate-600"><strong>Riwayat Pindah Ruangan:</strong> {{ $alkes->mutasi->count() }} kali dipindahkan</p>
                    <p class="text-slate-600"><strong>Log Perbaikan / Kalibrasi:</strong> {{ $alkes->logPemeliharaan->count() }} catatan pemeliharaan</p>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
