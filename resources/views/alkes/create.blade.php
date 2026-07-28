@extends('layouts.app')

@section('title', 'Tambah Alat Kesehatan Baru')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Navigation -->
    <div class="flex items-center gap-4 border-b border-slate-200 pb-4">
        <a href="{{ route('alkes.index') }}" class="p-2.5 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition text-slate-600">
            <i class="ri-arrow-left-line text-lg"></i>
        </a>
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight">Registrasi Alkes Baru</h3>
            <p class="text-sm text-slate-500">Tambahkan unit alat kesehatan baru ke dalam inventaris seksi Anda</p>
        </div>
    </div>

    <!-- Form Card -->
    <form method="POST" action="{{ route('alkes.store') }}" class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm space-y-6">
        @csrf

        @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm space-y-1">
                <p class="font-bold flex items-center gap-1.5"><i class="ri-error-warning-line text-lg"></i> Terjadi Kesalahan Input:</p>
                <ul class="list-disc pl-5 text-xs space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Informasi Seksi Penanggung Jawab -->
        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-600 text-white flex items-center justify-center font-bold">
                    <i class="ri-building-line text-xl"></i>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Seksi Penanggung Jawab</span>
                    <h4 class="font-extrabold text-slate-900 text-base">{{ $userSeksi->nama_seksi ?? 'Seksi Penunjang Medis' }}</h4>
                </div>
            </div>
            <input type="hidden" name="seksi_id" value="{{ $userSeksiId }}">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Kode Inventaris -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kode Inventaris RS <span class="text-rose-500">*</span></label>
                <input type="text" name="kode_inventaris" value="{{ old('kode_inventaris', 'INV/ALKES/' . date('Y') . '/' . rand(100, 999)) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono focus:ring-2 focus:ring-teal-500">
            </div>

            <!-- Nomor Seri -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nomor Seri (Serial Number)</label>
                <input type="text" name="nomor_seri" value="{{ old('nomor_seri') }}" placeholder="SN-12345678" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono focus:ring-2 focus:ring-teal-500">
            </div>

            <!-- Nomenklatur -->
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Standard Nomenklatur <span class="text-rose-500">*</span></label>
                <select name="nomenklatur_id" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                    <option value="">-- Pilih Nomenklatur Alat --</option>
                    @foreach ($nomenklaturList as $nom)
                        <option value="{{ $nom->id }}" {{ old('nomenklatur_id') == $nom->id ? 'selected' : '' }}>{{ $nom->nama_alat }} ({{ $nom->kategori }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Merk & Tipe -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Merk / Produsen</label>
                <input type="text" name="merk" value="{{ old('merk') }}" placeholder="Draeger, Zoll, GE..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tipe / Model</label>
                <input type="text" name="tipe" value="{{ old('tipe') }}" placeholder="Model X-100" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
            </div>

            <!-- Ruangan -->
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Ruangan Spesifik Seksi Anda</label>
                <select name="ruangan_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                    <option value="">-- Pilih Ruangan Seksi (Opsional) --</option>
                    @foreach ($ruanganList as $ruang)
                        <option value="{{ $ruang->id }}" {{ old('ruangan_id') == $ruang->id ? 'selected' : '' }}>{{ $ruang->nama_ruangan }} ({{ $ruang->kode_ruangan }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Penggunaan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Status Awal Alat <span class="text-rose-500">*</span></label>
                <select name="status" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                    @foreach ($statuses as $st)
                        <option value="{{ $st->value }}">{{ $st->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Kondisi Fisik -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kondisi Fisik <span class="text-rose-500">*</span></label>
                <select name="kondisi" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                    @foreach ($kondisis as $kd)
                        <option value="{{ $kd->value }}">{{ $kd->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Tanggal Pengadaan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tanggal Pengadaan</label>
                <input type="date" name="tanggal_pengadaan" value="{{ old('tanggal_pengadaan', date('Y-m-d')) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
            </div>

            <!-- Nilai Aset -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nilai Aset (Rp)</label>
                <input type="number" step="0.01" name="nilai_aset" value="{{ old('nilai_aset', 0) }}" placeholder="0" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
            </div>

            <!-- Catatan -->
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Catatan Operasional Spesifikasi</label>
                <textarea name="catatan" rows="3" placeholder="Catatan khusus lokasi atau spesifikasi alat..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">{{ old('catatan') }}</textarea>
            </div>

        </div>

        <!-- Form Action -->
        <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-6">
            <a href="{{ route('alkes.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-teal-600/30 transition flex items-center gap-2">
                <i class="ri-save-line text-lg"></i>
                Simpan Alkes
            </button>
        </div>

    </form>

</div>
@endsection
