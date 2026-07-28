@extends('layouts.app')

@section('title', 'Proses Pindah Lokasi Alat')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('alkes.index', ['seksi_id' => $userSeksiId]) }}" class="p-2.5 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition text-slate-600">
            <i class="ri-arrow-left-line text-lg"></i>
        </a>
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight">Formulir Pindah Lokasi / Transfer Alat</h3>
            <p class="text-sm text-slate-500">Memindahkan lokasi fisik unit alkes milik seksi Anda ke seksi tujuan RS (Hak kepemilikan aset tetap berada di Seksi Anda)</p>
        </div>
    </div>

    <form method="POST" action="{{ route('mutasi.store') }}" class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm space-y-6">
        @csrf

        <!-- Card Informatif Seksi Asal Pengirim -->
        <div class="p-4 bg-teal-50 border border-teal-200 rounded-xl flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-600 text-white flex items-center justify-center font-bold">
                    <i class="ri-building-line text-xl"></i>
                </div>
                <div>
                    <span class="text-xs font-bold text-teal-700 uppercase tracking-wider block">Seksi Pemilik Aset (Pengirim)</span>
                    <h4 class="font-extrabold text-slate-900 text-base">{{ $userSeksi->nama_seksi ?? 'Seksi RS' }}</h4>
                </div>
            </div>
            <span class="px-3 py-1 bg-teal-200 text-teal-800 text-xs font-bold rounded-lg"><i class="ri-checkbox-circle-line"></i> Pemilik Asli Aset</span>
        </div>

        <!-- Pilih Alkes Dari Seksi Pengirim -->
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Alat Kesehatan Milik / Berada di Seksi Anda <span class="text-rose-500">*</span></label>
            <select name="alkes_id" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 font-medium">
                <option value="">-- Pilih Unit Alkes --</option>
                @foreach ($alkesList as $alkes)
                    <option value="{{ $alkes->id }}" {{ $selectedAlkesId == $alkes->id ? 'selected' : '' }}>
                        {{ $alkes->kode_inventaris }} - {{ $alkes->nomenklatur->nama_alat ?? '' }} (Lokasi Saat Ini: {{ $alkes->ruangan->nama_ruangan ?? 'Seksi Utama' }})
                    </option>
                @endforeach
            </select>
            <p class="text-[11px] text-slate-400 mt-1 italic">* Menampilkan alat kesehatan yang terdaftar sebagai milik seksi Anda.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Seksi Tujuan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Seksi Tujuan Lokasi Baru <span class="text-rose-500">*</span></label>
                <select name="seksi_tujuan_id" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 font-semibold">
                    <option value="">-- Pilih Seksi Tujuan --</option>
                    @foreach ($seksiList as $seksi)
                        <option value="{{ $seksi->id }}">{{ $seksi->nama_seksi }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Ruangan Tujuan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Ruangan Tujuan Lokasi Baru</label>
                <select name="ruangan_tujuan_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                    <option value="">-- Pilih Ruangan (Opsional) --</option>
                    @foreach ($ruanganList as $ruang)
                        <option value="{{ $ruang->id }}">{{ $ruang->nama_ruangan }} ({{ $ruang->seksi->nama_seksi ?? 'RS' }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Pemohon -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Pemohon / Penerima <span class="text-rose-500">*</span></label>
                <input type="text" name="pemohon" value="{{ old('pemohon') }}" required placeholder="Nama perawat / penerima di lokasi tujuan" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
            </div>

            <!-- Penanggung Jawab -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Penanggung Jawab / Pengesah <span class="text-rose-500">*</span></label>
                <input type="text" name="penanggung_jawab" value="{{ old('penanggung_jawab') }}" required placeholder="Dokter PJ / Kepala Seksi" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
            </div>

        </div>

        <!-- Alasan Mutasi -->
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Alasan / Keperluan Pemindahan Lokasi <span class="text-rose-500">*</span></label>
            <textarea name="alasan_mutasi" rows="3" required placeholder="Jelaskan kebutuhan operasional pemindahan lokasi fisik alat ini..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">{{ old('alasan_mutasi') }}</textarea>
        </div>

        <!-- Warning Info Note -->
        <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800 flex items-center gap-2">
            <i class="ri-information-line text-base text-amber-600 shrink-0"></i>
            <span><strong>Catatan Kepemilikan:</strong> Memindahkan lokasi fisik alat ini <u>TIDAK menghapus</u> status kepemilikan aset dari Seksi Anda. Alat akan tetap muncul di submenu Seksi Anda dengan penanda lokasi baru.</span>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('alkes.index', ['seksi_id' => $userSeksiId]) }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl transition">Batal</a>
            <button type="submit" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm rounded-xl shadow-lg shadow-teal-600/30 transition flex items-center gap-2">
                <i class="ri-check-double-line text-lg"></i>
                Proses Pindah Lokasi Alat
            </button>
        </div>

    </form>

</div>
@endsection
