@extends('layouts.app')

@section('title', 'Tambah Inventaris Alkes Baru')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Bar -->
    <div class="flex items-center gap-4">
        <a href="{{ route('alkes.index') }}" class="p-2.5 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition text-slate-600">
            <i class="ri-arrow-left-line text-lg"></i>
        </a>
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight">Registrasi Inventaris Alkes Baru</h3>
            <p class="text-sm text-slate-500">Menambahkan aset unit alat kesehatan ke seksi operasional Anda</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">

        {{-- Seksi Banner Status --}}
        <div class="mb-6 p-4 bg-teal-50 border border-teal-200 rounded-2xl flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-600 text-white flex items-center justify-center text-xl shrink-0">
                    <i class="ri-building-line"></i>
                </div>
                <div>
                    <span class="text-xs text-teal-700 font-bold uppercase block">Lokasi Seksi Pendaftaran:</span>
                    <span class="font-extrabold text-teal-900 text-base">{{ $userSeksi->nama_seksi }}</span>
                </div>
            </div>
            <span class="px-3 py-1 bg-teal-200 text-teal-800 font-bold text-xs rounded-full">Seksi Sendiri</span>
        </div>

        <form method="POST" action="{{ route('alkes.store') }}" class="space-y-6">
            @csrf

            <!-- Hidden Input Seksi ID -->
            <input type="hidden" name="seksi_id" value="{{ $userSeksiId }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Kode Inventaris -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kode Inventaris <span class="text-rose-500">*</span></label>
                    <input type="text" name="kode_inventaris" value="{{ old('kode_inventaris', 'INV/ALKES/' . date('Y') . '/' . sprintf('%03d', rand(100, 999))) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono focus:ring-2 focus:ring-teal-500">
                    @error('kode_inventaris') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
                </div>

                <!-- Nomor Seri -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nomor Seri (Serial Number)</label>
                    <input type="text" name="nomor_seri" value="{{ old('nomor_seri') }}" placeholder="Contoh: SN-9812-77X" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono focus:ring-2 focus:ring-teal-500">
                </div>

                <!-- Nomenklatur -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Nomenklatur Standard Kemenkes <span class="text-rose-500">*</span></label>
                    <select name="nomenklatur_id" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                        <option value="">-- Pilih Nomenklatur Alat --</option>
                        @foreach ($nomenklaturList as $nom)
                            <option value="{{ $nom->id }}" {{ old('nomenklatur_id') == $nom->id ? 'selected' : '' }}>
                                {{ $nom->nama_alat }} ({{ $nom->kategori }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Merk -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Merk / Produsen</label>
                    <input type="text" name="merk" value="{{ old('merk') }}" placeholder="Contoh: Philips, Draeger, Mindray" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                </div>

                <!-- Tipe -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Model / Tipe</label>
                    <input type="text" name="tipe" value="{{ old('tipe') }}" placeholder="Contoh: Series-90 Pro" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                </div>

                <!-- Ruangan -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Penempatan Ruangan Spesifik</label>
                    <select name="ruangan_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                        <option value="">-- Pilih Ruangan --</option>
                        @foreach ($ruanganList as $ruang)
                            <option value="{{ $ruang->id }}" {{ old('ruangan_id') == $ruang->id ? 'selected' : '' }}>
                                {{ $ruang->nama_ruangan }} ({{ $ruang->lokasi_lantai }})
                            </option>
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
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tanggal Pengadaan</label>
                    <input type="date" name="tanggal_pengadaan" value="{{ old('tanggal_pengadaan', date('Y-m-d')) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
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
                    Simpan Inventaris
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
