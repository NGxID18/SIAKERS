@extends('layouts.app')

@section('title', 'Formulir Lapor Perbaikan & Kalibrasi Alkes')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('pemeliharaan.index') }}" class="p-2.5 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition text-slate-600">
            <i class="ri-arrow-left-line text-lg"></i>
        </a>
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight">Formulir Lapor Perbaikan & Kalibrasi</h3>
            <p class="text-sm text-slate-500">Catat tindakan pemeliharaan medis, perbaikan teknis, atau kalibrasi BPFK</p>
        </div>
    </div>

    <form method="POST" action="{{ route('pemeliharaan.store') }}" class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm space-y-6">
        @csrf

        <!-- Pilih Alkes -->
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Alat Kesehatan <span class="text-rose-500">*</span></label>
            <select name="alkes_id" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 font-semibold">
                <option value="">-- Pilih Unit Alkes --</option>
                @foreach ($alkesList as $alkes)
                    <option value="{{ $alkes->id }}" {{ $selectedAlkesId == $alkes->id ? 'selected' : '' }}>
                        {{ $alkes->nama_barang }} (SN: {{ $alkes->nomor_seri ?? '-' }}) - Ruangan: {{ $alkes->ruangan->nama_ruangan ?? 'RS' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Jenis Tindakan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Jenis Tindakan <span class="text-rose-500">*</span></label>
                <select name="jenis_tindakan" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 font-semibold">
                    <option value="Perbaikan">Perbaikan Kerusakan</option>
                    <option value="Kalibrasi">Kalibrasi Tahunan BPFK</option>
                    <option value="Pemeliharaan Rutin">Pemeliharaan Rutin ATEM</option>
                </select>
            </div>

            <!-- Pelaksana / Vendor -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pelaksana / Vendor</label>
                <input type="text" name="pelaksana_vendor" value="{{ old('pelaksana_vendor') }}" placeholder="Teknisi ATEM RS / Vendor PT. Medika" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 font-medium">
            </div>

            <!-- Tanggal Mulai -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tanggal Mulai <span class="text-rose-500">*</span></label>
                <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', date('Y-m-d')) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 font-medium">
            </div>

            <!-- Tanggal Selesai -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tanggal Selesai (Opsional)</label>
                <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 font-medium">
            </div>

        </div>

        <!-- Deskripsi Kerusakan -->
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Deskripsi Kerusakan / Gejala</label>
            <textarea name="deskripsi_kerusakan" rows="2" placeholder="Jelaskan gejala kerusakan atau indikasi kelainan pada alat..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 font-medium">{{ old('deskripsi_kerusakan') }}</textarea>
        </div>

        <!-- Tindakan Perbaikan -->
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tindakan Perbaikan / Solusi</label>
            <textarea name="tindakan_perbaikan" rows="2" placeholder="Jelaskan perbaikan yang dilakukan (misal: penggantian sparepart, perapian kabel, dll)..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 font-medium">{{ old('tindakan_perbaikan') }}</textarea>
        </div>

        <!-- Status Hasil -->
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Status Hasil Perbaikan <span class="text-rose-500">*</span></label>
            <select name="status_hasil" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 font-bold">
                <option value="Selesai">Selesai (Alat Siap Digunakan Kembali)</option>
                <option value="Proses">Dalam Proses Perbaikan</option>
            </select>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('pemeliharaan.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl transition">Batal</a>
            <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-sm rounded-xl shadow-lg shadow-amber-600/30 transition flex items-center gap-2">
                <i class="ri-check-double-line text-lg"></i>
                Simpan Log Perbaikan
            </button>
        </div>

    </form>

</div>
@endsection
