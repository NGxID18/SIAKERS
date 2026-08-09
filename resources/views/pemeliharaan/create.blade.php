@extends('layouts.app')

@section('title', 'Formulir Lapor Perbaikan & Kalibrasi Alkes')

@section('content')

<div class="max-w-3xl mx-auto space-y-5">

    <div class="flex items-center gap-3">
        <a href="{{ route('pemeliharaan.index') }}" class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition text-slate-500 hover:text-slate-700">
            <i class="ri-arrow-left-line text-base"></i>
        </a>
        <div>
            <h3 class="text-lg font-bold text-slate-900 tracking-tight">Formulir Lapor Kerusakan</h3>
            <p class="text-xs text-slate-500">Laporkan unit alkes yang membutuhkan perbaikan</p>
        </div>
    </div>

    <div id="infoCreatePemeliharaan" class="bg-amber-50 px-4 py-3 rounded-xl border border-amber-200 text-amber-800 text-xs flex items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <i class="ri-information-fill text-amber-500 text-sm shrink-0"></i>
            <span><span class="font-semibold">Otomatisasi:</span> Memproses laporan ini akan memindahkan lokasi fisik unit ke Ruangan Elektromedis.</span>
        </div>
        <button type="button" onclick="document.getElementById('infoCreatePemeliharaan').remove()" class="text-amber-400 hover:text-amber-700 p-0.5 rounded transition">
            <i class="ri-close-line text-sm"></i>
        </button>
    </div>

    <form method="POST" action="{{ route('pemeliharaan.store') }}" class="bg-white p-5 sm:p-6 rounded-xl border border-slate-200 space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1.5">Pilih Alat Kesehatan Yang Rusak <span class="text-rose-500">*</span></label>
            <select id="selectAlkesPemeliharaan" name="alkes_id" required>
                <option value="">-- Ketik Nama Barang, SN, atau Ruangan --</option>
                @foreach ($alkesList as $alkes)
                    <option value="{{ $alkes->id }}" {{ $selectedAlkesId == $alkes->id ? 'selected' : '' }}>
                        {{ $alkes->nama_barang }} (SN: {{ $alkes->nomor_seri ?? '-' }}) - {{ $alkes->ruangan->nama_ruangan ?? 'RS' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">Jenis Tindakan <span class="text-rose-500">*</span></label>
                <select name="jenis_tindakan" required class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400">
                    <option value="Perbaikan (Korektif)">Perbaikan (Korektif)</option>
                    <option value="Kalibrasi Alat">Kalibrasi Alat</option>
                    <option value="Pemeliharaan Rutin">Pemeliharaan Rutin</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">Tanggal Lapor <span class="text-rose-500">*</span></label>
                <input type="datetime-local" name="tanggal_lapor" value="{{ date('Y-m-d\TH:i') }}" required class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400">
            </div>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1.5">Deskripsi Kerusakan <span class="text-rose-500">*</span></label>
            <textarea name="gejala_kerusakan" rows="4" required placeholder="Jelaskan gejala kerusakan (misal: layar mati, pompa macet, alarm eror)..." class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400"></textarea>
        </div>

        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
            <a href="{{ route('pemeliharaan.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium text-sm rounded-lg transition">
                Batal
            </a>
            <button type="submit" class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-white font-medium text-sm rounded-lg transition flex items-center gap-1.5">
                <i class="ri-send-plane-fill text-sm"></i>
                Kirim Laporan
            </button>
        </div>

    </form>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var sel = document.getElementById('selectAlkesPemeliharaan');
        if (sel && !sel.tomselect) {
            new TomSelect('#selectAlkesPemeliharaan', {
                create: false,
                placeholder: '-- Ketik Nama Barang, SN, atau Ruangan --',
                maxOptions: 50,
            });
        }
    });
</script>
@endsection
