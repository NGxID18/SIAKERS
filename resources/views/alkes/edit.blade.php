@extends('layouts.app')

@section('title', 'Edit Data Alkes')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Bar -->
    <div class="flex items-center gap-4">
        <a href="{{ route('alkes.show', $alkes->id) }}" class="p-2.5 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition text-slate-600">
            <i class="ri-arrow-left-line text-lg"></i>
        </a>
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight">Edit Data Alkes: {{ $alkes->kode_inventaris }}</h3>
            <p class="text-sm text-slate-500">Perbarui spesifikasi dan lokasi penempatan alat kesehatan</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
        <form method="POST" action="{{ route('alkes.update', $alkes->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <input type="hidden" name="seksi_id" value="{{ $alkes->seksi_id }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Kode Inventaris -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kode Inventaris <span class="text-rose-500">*</span></label>
                    <input type="text" name="kode_inventaris" value="{{ old('kode_inventaris', $alkes->kode_inventaris) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono focus:ring-2 focus:ring-teal-500">
                </div>

                <!-- Nomor Seri -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nomor Seri (Serial Number)</label>
                    <input type="text" name="nomor_seri" value="{{ old('nomor_seri', $alkes->nomor_seri) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono focus:ring-2 focus:ring-teal-500">
                </div>

                <!-- Nomenklatur -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Nomenklatur Standard Kemenkes <span class="text-rose-500">*</span></label>
                    <select name="nomenklatur_id" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                        @foreach ($nomenklaturList as $nom)
                            <option value="{{ $nom->id }}" {{ old('nomenklatur_id', $alkes->nomenklatur_id) == $nom->id ? 'selected' : '' }}>
                                {{ $nom->nama_alat }} ({{ $nom->kategori }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Merk -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Merk / Produsen</label>
                    <input type="text" name="merk" value="{{ old('merk', $alkes->merk) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                </div>

                <!-- Tipe -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Model / Tipe</label>
                    <input type="text" name="tipe" value="{{ old('tipe', $alkes->tipe) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                </div>

                <!-- Ruangan -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Penempatan Ruangan Spesifik</label>
                    <select name="ruangan_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                        <option value="">-- Pilih Ruangan --</option>
                        @foreach ($ruanganList as $ruang)
                            <option value="{{ $ruang->id }}" {{ old('ruangan_id', $alkes->ruangan_id) == $ruang->id ? 'selected' : '' }}>
                                {{ $ruang->nama_ruangan }} ({{ $ruang->lokasi_lantai }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Penggunaan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Status Alat <span class="text-rose-500">*</span></label>
                    <select name="status" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                        @foreach ($statuses as $st)
                            <option value="{{ $st->value }}" {{ old('status', $alkes->status->value ?? $alkes->status) == $st->value ? 'selected' : '' }}>{{ $st->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Kondisi Fisik -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kondisi Fisik <span class="text-rose-500">*</span></label>
                    <select name="kondisi" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                        @foreach ($kondisis as $kd)
                            <option value="{{ $kd->value }}" {{ old('kondisi', $alkes->kondisi->value ?? $alkes->kondisi) == $kd->value ? 'selected' : '' }}>{{ $kd->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tanggal Pengadaan -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tanggal Pengadaan</label>
                    <input type="date" name="tanggal_pengadaan" value="{{ old('tanggal_pengadaan', $alkes->tanggal_pengadaan ? $alkes->tanggal_pengadaan->format('Y-m-d') : '') }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                </div>

                <!-- Catatan -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Catatan Operasional Spesifikasi</label>
                    <textarea name="catatan" rows="3" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">{{ old('catatan', $alkes->catatan) }}</textarea>
                </div>

            </div>

            <!-- Form Action -->
            <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-6">
                <a href="{{ route('alkes.show', $alkes->id) }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-teal-600/30 transition flex items-center gap-2">
                    <i class="ri-save-line text-lg"></i>
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
