@extends('layouts.app')

@section('title', 'Formulir Pindah Ruangan Alkes')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('mutasi.index') }}" class="p-2.5 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition text-slate-600">
            <i class="ri-arrow-left-line text-lg"></i>
        </a>
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight">Formulir Pindah Ruangan Alkes</h3>
            <p class="text-sm text-slate-500">Memindahkan lokasi fisik unit alkes antar ruangan di Rumah Sakit</p>
        </div>
    </div>

    <form method="POST" action="{{ route('mutasi.store') }}" class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm space-y-6">
        @csrf

        <!-- Pilih Alkes -->
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Alat Kesehatan <span class="text-rose-500">*</span></label>
            <select name="alkes_id" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 font-semibold">
                <option value="">-- Pilih Unit Alkes --</option>
                @foreach ($alkesList as $alkes)
                    <option value="{{ $alkes->id }}" {{ $selectedAlkesId == $alkes->id ? 'selected' : '' }}>
                        {{ $alkes->nama_barang }} (SN: {{ $alkes->nomor_seri ?? '-' }}) - Lokasi Saat Ini: {{ $alkes->lokasiRuangan->nama_ruangan ?? $alkes->ruangan->nama_ruangan ?? 'Ruangan RS' }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Ruangan Tujuan -->
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Ruangan Tujuan Lokasi Baru <span class="text-rose-500">*</span></label>
            <select name="ruangan_tujuan_id" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 font-semibold">
                <option value="">-- Pilih Ruangan Tujuan --</option>
                @foreach ($ruanganList as $ruang)
                    <option value="{{ $ruang->id }}">{{ $ruang->nama_ruangan }} ({{ $ruang->lokasi_lantai ?? 'RS' }})</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Pemohon -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Pemohon / Penerima <span class="text-rose-500">*</span></label>
                <input type="text" name="pemohon" value="{{ old('pemohon') }}" required placeholder="Nama perawat / petugas penerima" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 font-medium">
            </div>

            <!-- Penanggung Jawab -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Penanggung Jawab / Kepala Ruangan <span class="text-rose-500">*</span></label>
                <input type="text" name="penanggung_jawab" value="{{ old('penanggung_jawab') }}" required placeholder="Kepala Ruangan / Dokter PJ" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 font-medium">
            </div>

        </div>

        <!-- Alasan Mutasi -->
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Alasan Pemindahan Ruangan <span class="text-rose-500">*</span></label>
            <textarea name="alasan_mutasi" rows="3" required placeholder="Jelaskan kebutuhan operasional pemindahan lokasi fisik alat ini..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 font-medium">{{ old('alasan_mutasi') }}</textarea>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('mutasi.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl transition">Batal</a>
            <button type="submit" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm rounded-xl shadow-lg shadow-teal-600/30 transition flex items-center gap-2">
                <i class="ri-check-double-line text-lg"></i>
                Proses Pindah Ruangan
            </button>
        </div>

    </form>

</div>
@endsection
