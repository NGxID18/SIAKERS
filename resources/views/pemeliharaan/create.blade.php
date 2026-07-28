@extends('layouts.app')

@section('title', 'Lapor Perbaikan / Kalibrasi')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('pemeliharaan.index') }}" class="p-2.5 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition text-slate-600">
            <i class="ri-arrow-left-line text-lg"></i>
        </a>
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight">Catat Perbaikan / Kalibrasi Alkes</h3>
            <p class="text-sm text-slate-500">Laporkan kendala medis, tindakan service vendor, atau kalibrasi berkala</p>
        </div>
    </div>

    <form method="POST" action="{{ route('pemeliharaan.store') }}" class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm space-y-6">
        @csrf

        <!-- Pilih Alkes -->
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Unit Alkes Bermasalah / Diperbaiki <span class="text-rose-500">*</span></label>
            <select name="alkes_id" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                <option value="">-- Pilih Unit Alkes --</option>
                @foreach ($alkesList as $alkes)
                    <option value="{{ $alkes->id }}" {{ $selectedAlkesId == $alkes->id ? 'selected' : '' }}>
                        {{ $alkes->kode_inventaris }} - {{ $alkes->nomenklatur->nama_alat ?? '' }} (Seksi: {{ $alkes->seksi->nama_seksi ?? '-' }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Jenis Tindakan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Jenis Pemeliharaan <span class="text-rose-500">*</span></label>
                <select name="jenis_tindakan" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                    <option value="Perbaikan Kerusakan">Perbaikan Kerusakan (Troubleshooting)</option>
                    <option value="Pemeliharaan Rutin">Pemeliharaan Rutin (Preventive Maintenance)</option>
                    <option value="Kalibrasi BPFR">Kalibrasi & Sertifikasi Laik Pakai</option>
                </select>
            </div>

            <!-- Pelaksana Vendor -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pelaksana Teknisi / Vendor</label>
                <input type="text" name="pelaksana_vendor" value="{{ old('pelaksana_vendor', 'Tim Bio-Medis RS') }}" placeholder="PT Siemens / BPFK / Tim ATEM Internal" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
            </div>

            <!-- Tanggal Mulai -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tanggal Mulai Pengerjaan <span class="text-rose-500">*</span></label>
                <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', date('Y-m-d')) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
            </div>

            <!-- Tanggal Selesai -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tanggal Selesai (Kosongkan bila masih proses)</label>
                <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
            </div>

        </div>

        <!-- Deskripsi Kerusakan -->
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Deskripsi Gejala / Kerusakan</label>
            <textarea name="deskripsi_kerusakan" rows="2" placeholder="Uraikan keluhan alat (layar mati, sensor galat, error E-04)..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">{{ old('deskripsi_kerusakan') }}</textarea>
        </div>

        <!-- Tindakan Perbaikan -->
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tindakan Perbaikan yang Dilakukan</label>
            <textarea name="tindakan_perbaikan" rows="2" placeholder="Detail pergantian sparepart, penyolderan ulang, kalibrasi ulang..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">{{ old('tindakan_perbaikan') }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Biaya -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Estimasi / Biaya Perbaikan (Rp)</label>
                <input type="number" step="0.01" name="biaya" value="{{ old('biaya', 0) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
            </div>

            <!-- Status Hasil -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Status Hasil Pengerjaan <span class="text-rose-500">*</span></label>
                <select name="status_hasil" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                    <option value="Selesai">Selesai (Alat Siap Pakai Kembali)</option>
                    <option value="Proses">Sedang Dalam Proses Perbaikan</option>
                    <option value="Gagal">Gagal / Direkomendasikan Afkir</option>
                </select>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('pemeliharaan.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl transition">Batal</a>
            <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-sm rounded-xl shadow-lg shadow-amber-600/30 transition flex items-center gap-2">
                <i class="ri-save-line text-lg"></i>
                Simpan Log Perbaikan
            </button>
        </div>

    </form>

</div>
@endsection
