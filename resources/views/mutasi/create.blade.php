@extends('layouts.app')

@section('title', 'Formulir Pindah Ruangan Alkes')

@section('content')
<!-- Tom Select CSS & JS CDN -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

<style>
    .ts-wrapper {
        border-radius: 0.75rem !important;
        width: 100% !important;
    }
    .ts-control {
        border-radius: 0.75rem !important;
        background-color: #f8fafc !important;
        border: 1px solid #cbd5e1 !important;
        padding: 0.7rem 1rem !important;
        font-size: 0.95rem !important;
        font-weight: 600 !important;
        color: #0f172a !important;
        box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05) !important;
        transition: all 0.2s ease !important;
    }
    .ts-wrapper.focus .ts-control {
        border-color: #0d9488 !important;
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15) !important;
        background-color: #ffffff !important;
    }
    .ts-dropdown {
        border-radius: 0.85rem !important;
        border: 1px solid #0d9488 !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
        overflow: hidden !important;
        z-index: 9999 !important;
        padding: 6px !important;
        background: #ffffff !important;
    }
    .ts-dropdown .option {
        padding: 10px 14px !important;
        border-radius: 0.5rem !important;
        font-size: 0.925rem !important;
        font-weight: 500 !important;
        color: #334155 !important;
    }
    .ts-dropdown .option:hover, 
    .ts-dropdown .option.active {
        background-color: #0d9488 !important;
        color: #ffffff !important;
    }
</style>

<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('mutasi.index') }}" class="p-2.5 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition text-slate-600">
            <i class="ri-arrow-left-line text-xl"></i>
        </a>
        <div>
            <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">Formulir Pindah Ruangan Alkes</h3>
            <p class="text-base text-slate-600 mt-1 font-normal">Memindahkan lokasi fisik unit alkes antar ruangan</p>
        </div>
    </div>

    <form method="POST" action="{{ route('mutasi.store') }}" class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm space-y-6">
        @csrf

        <!-- Pilih Alkes dengan Pencarian Real-Time (TomSelect) -->
        <div>
            <label class="block text-sm font-bold text-slate-800 uppercase tracking-wider mb-2">Pilih Alat Kesehatan <span class="text-rose-500">*</span></label>
            <select id="selectAlkesMutasi" name="alkes_id" required>
                <option value="">-- Ketik Nama Barang, SN, atau Ruangan --</option>
                @foreach ($alkesList as $alkes)
                    <option value="{{ $alkes->id }}" {{ $selectedAlkesId == $alkes->id ? 'selected' : '' }}>
                        {{ $alkes->nama_barang }} (SN: {{ $alkes->nomor_seri ?? '-' }}) - Lokasi Saat Ini: {{ $alkes->lokasiRuangan->nama_ruangan ?? $alkes->ruangan->nama_ruangan ?? 'Utama' }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Ruangan Tujuan -->
        <div>
            <label class="block text-sm font-bold text-slate-800 uppercase tracking-wider mb-2">Ruangan Tujuan Lokasi Baru <span class="text-rose-500">*</span></label>
            <select id="selectRuanganTujuan" name="ruangan_tujuan_id" required>
                <option value="">-- Pilih Ruangan Tujuan --</option>
                @foreach ($ruanganList as $ruang)
                    <option value="{{ $ruang->id }}">{{ $ruang->nama_ruangan }} ({{ $ruang->lokasi_lantai ?? 'Penempatan' }})</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Pemohon -->
            <div>
                <label class="block text-sm font-bold text-slate-800 uppercase tracking-wider mb-2">Nama Pemohon / Penerima <span class="text-rose-500">*</span></label>
                <input type="text" name="pemohon" value="{{ old('pemohon') }}" required placeholder="Nama perawat / petugas penerima" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-base font-normal focus:ring-2 focus:ring-teal-500">
            </div>

            <!-- Penanggung Jawab -->
            <div>
                <label class="block text-sm font-bold text-slate-800 uppercase tracking-wider mb-2">Penanggung Jawab / Kepala Ruangan <span class="text-rose-500">*</span></label>
                <input type="text" name="penanggung_jawab" value="{{ old('penanggung_jawab') }}" required placeholder="Kepala Ruangan / Dokter PJ" class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-base font-normal focus:ring-2 focus:ring-teal-500">
            </div>

        </div>

        <!-- Alasan Mutasi -->
        <div>
            <label class="block text-sm font-bold text-slate-800 uppercase tracking-wider mb-2">Alasan Pemindahan Ruangan <span class="text-rose-500">*</span></label>
            <textarea name="alasan_mutasi" rows="3" required placeholder="Jelaskan kebutuhan operasional pemindahan lokasi fisik alat ini..." class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-base font-normal focus:ring-2 focus:ring-teal-500">{{ old('alasan_mutasi') }}</textarea>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('mutasi.index') }}" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-base rounded-xl transition">Batal</a>
            <button type="submit" class="px-7 py-3 bg-teal-600 hover:bg-teal-700 text-white font-semibold text-base rounded-xl shadow-lg shadow-teal-600/30 transition flex items-center gap-2">
                <i class="ri-check-double-line text-xl"></i>
                Proses Pindah Ruangan
            </button>
        </div>

    </form>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        new TomSelect('#selectAlkesMutasi', {
            create: false,
            placeholder: 'Ketik nama barang, SN, atau lokasi...',
            maxOptions: 100,
        });

        new TomSelect('#selectRuanganTujuan', {
            create: false,
            placeholder: 'Ketik nama ruangan tujuan...',
            maxOptions: 50,
        });
    });
</script>
@endsection
