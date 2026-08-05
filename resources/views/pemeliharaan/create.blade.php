@extends('layouts.app')

@section('title', 'Formulir Lapor Perbaikan & Kalibrasi Alkes')

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
        border-color: #d97706 !important;
        box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.15) !important;
        background-color: #ffffff !important;
    }
    .ts-dropdown {
        border-radius: 0.85rem !important;
        border: 1px solid #d97706 !important;
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
        background-color: #d97706 !important;
        color: #ffffff !important;
    }
</style>

<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('pemeliharaan.index') }}" class="p-2.5 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition text-slate-600">
            <i class="ri-arrow-left-line text-xl"></i>
        </a>
        <div>
            <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">Formulir Lapor Perbaikan & Kerusakan</h3>
            <p class="text-base text-slate-600 mt-1 font-normal">Laporkan unit alkes yang membutuhkan perbaikan ke Ruangan Elektromedis</p>
        </div>
    </div>

    <!-- Banner Info Otomatisasi (Ringkas & Jelas) -->
    <div id="infoCreatePemeliharaan" class="bg-amber-50/90 px-4 py-3 rounded-xl border border-amber-200 text-amber-900 text-sm font-normal flex items-center justify-between gap-3 shadow-xs">
        <div class="flex items-center gap-2.5">
            <i class="ri-information-fill text-amber-600 text-lg shrink-0"></i>
            <span><strong>Catatan Otomatisasi:</strong> Memproses laporan ini akan otomatis memindahkan <strong>lokasi fisik unit ke Ruangan Elektromedis</strong> dan tercatat pada riwayat mutasi alkes.</span>
        </div>
        <button type="button" onclick="document.getElementById('infoCreatePemeliharaan').remove()" class="text-amber-500 hover:text-amber-800 p-1 text-lg rounded-lg transition" title="Tutup">
            <i class="ri-close-line"></i>
        </button>
    </div>

    <form method="POST" action="{{ route('pemeliharaan.store') }}" class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm space-y-6">
        @csrf

        <!-- Pilih Alkes dengan Label Jelas: Ruangan Pemilik Aset -->
        <div>
            <label class="block text-sm font-bold text-slate-800 uppercase tracking-wider mb-2">Pilih Alat Kesehatan Yang Rusak <span class="text-rose-500">*</span></label>
            <select id="selectAlkesPemeliharaan" name="alkes_id" required>
                <option value="">-- Ketik Nama Barang, SN, atau Ruangan Pemilik --</option>
                @foreach ($alkesList as $alkes)
                    <option value="{{ $alkes->id }}" {{ $selectedAlkesId == $alkes->id ? 'selected' : '' }}>
                        {{ $alkes->nama_barang }} (SN: {{ $alkes->nomor_seri ?? '-' }}) - Ruangan Pemilik Aset: {{ $alkes->ruangan->nama_ruangan ?? 'Penempatan' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Jenis Tindakan (Tanpa Opsi Suku Cadang - Sudah Termasuk Perbaikan) -->
            <div>
                <label class="block text-sm font-bold text-slate-800 uppercase tracking-wider mb-2">Jenis Tindakan <span class="text-rose-500">*</span></label>
                <select name="jenis_tindakan" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-base font-semibold text-slate-900 focus:ring-2 focus:ring-amber-500">
                    <option value="Perbaikan (Korektif)">Perbaikan (Korektif)</option>
                    <option value="Kalibrasi Alat">Kalibrasi Alat</option>
                    <option value="Pemeliharaan Rutin">Pemeliharaan Rutin</option>
                </select>
            </div>

            <!-- Tanggal Lapor -->
            <div>
                <label class="block text-sm font-bold text-slate-800 uppercase tracking-wider mb-2">Tanggal Lapor <span class="text-rose-500">*</span></label>
                <input type="datetime-local" name="tanggal_lapor" value="{{ date('Y-m-d\TH:i') }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-base font-medium text-slate-900 focus:ring-2 focus:ring-amber-500">
            </div>

        </div>

        <!-- Gejala Kerusakan -->
        <div>
            <label class="block text-sm font-bold text-slate-800 uppercase tracking-wider mb-2">Deskripsi Kerusakan / Indikasi Kelainan <span class="text-rose-500">*</span></label>
            <textarea name="gejala_kerusakan" rows="4" required placeholder="Jelaskan gejala kerusakan pada alat ini (misal: layar mati, pompa macet, alarm eror)..." class="w-full p-4 bg-slate-50 border border-slate-300 rounded-xl text-base font-normal text-slate-900 focus:ring-2 focus:ring-amber-500"></textarea>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('pemeliharaan.index') }}" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-base rounded-xl transition">
                Batal
            </a>
            <button type="submit" class="px-8 py-3 bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-base rounded-xl shadow-md shadow-amber-600/30 transition flex items-center gap-2">
                <i class="ri-send-plane-fill text-lg"></i>
                Kirim Laporan Kerusakan
            </button>
        </div>

    </form>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        new TomSelect('#selectAlkesPemeliharaan', {
            create: false,
            placeholder: '-- Ketik Nama Barang, SN, atau Ruangan Pemilik --',
            maxOptions: 50,
        });
    });
</script>
@endsection
