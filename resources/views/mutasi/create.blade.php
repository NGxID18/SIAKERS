@extends('layouts.app')

@section('title', 'Formulir Pindah Ruangan Alkes')

@section('content')

<div class="max-w-3xl mx-auto space-y-5">

    <div class="flex items-center gap-3">
        <a href="{{ route('mutasi.index') }}" class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition text-slate-500 hover:text-slate-700">
            <i class="ri-arrow-left-line text-base"></i>
        </a>
        <div>
            <h3 class="text-lg font-bold text-slate-900 tracking-tight">Formulir Pindah Ruangan</h3>
            <p class="text-xs text-slate-500">Memindahkan lokasi fisik unit alkes antar ruangan</p>
        </div>
    </div>

    <form method="POST" action="{{ route('mutasi.store') }}" class="bg-white p-5 sm:p-6 rounded-xl border border-slate-200 space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1.5">Pilih Alat Kesehatan <span class="text-rose-500">*</span></label>
            <select id="selectAlkesMutasi" name="alkes_id" required>
                <option value="">-- Ketik Nama Barang, SN, atau Ruangan --</option>
                @foreach ($alkesList as $alkes)
                    <option value="{{ $alkes->id }}" {{ $selectedAlkesId == $alkes->id ? 'selected' : '' }}>
                        {{ $alkes->nama_barang }} (SN: {{ $alkes->nomor_seri ?? '-' }}) - {{ $alkes->lokasiRuangan->nama_ruangan ?? $alkes->ruangan->nama_ruangan ?? 'Utama' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1.5">Ruangan Tujuan <span class="text-rose-500">*</span></label>
            <select id="selectRuanganTujuan" name="ruangan_tujuan_id" required>
                <option value="">-- Pilih Ruangan Tujuan --</option>
                @foreach ($ruanganList as $ruang)
                    <option value="{{ $ruang->id }}">{{ $ruang->nama_ruangan }} ({{ $ruang->lokasi_lantai ?? 'RS' }})</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">Nama Pemohon / Penerima <span class="text-rose-500">*</span></label>
                <input type="text" name="pemohon" value="{{ old('pemohon') }}" required placeholder="Nama perawat / petugas penerima" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">Penanggung Jawab <span class="text-rose-500">*</span></label>
                <input type="text" name="penanggung_jawab" value="{{ old('penanggung_jawab') }}" required placeholder="Kepala Ruangan / Dokter PJ" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition">
            </div>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1.5">Alasan Pemindahan <span class="text-rose-500">*</span></label>
            <textarea name="alasan_mutasi" rows="3" required placeholder="Jelaskan kebutuhan operasional pemindahan..." class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition">{{ old('alasan_mutasi') }}</textarea>
        </div>

        <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
            <a href="{{ route('mutasi.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-medium text-sm rounded-lg transition">Batal</a>
            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-lg transition flex items-center gap-1.5">
                <i class="ri-check-double-line text-base"></i>
                Proses Pindah
            </button>
        </div>

    </form>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('selectAlkesMutasi') && !document.getElementById('selectAlkesMutasi').tomselect) {
            new TomSelect('#selectAlkesMutasi', { create: false, placeholder: 'Ketik nama barang, SN, atau lokasi...', maxOptions: 100 });
        }
        if (document.getElementById('selectRuanganTujuan') && !document.getElementById('selectRuanganTujuan').tomselect) {
            new TomSelect('#selectRuanganTujuan', { create: false, placeholder: 'Ketik nama ruangan tujuan...', maxOptions: 50 });
        }
    });
</script>
@endsection
