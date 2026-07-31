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
            <p class="text-sm text-slate-500">Menambahkan aset unit alat kesehatan ke ruangan Rumah Sakit</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">

        <form method="POST" action="{{ route('alkes.store') }}" class="space-y-6">
            @csrf

            <!-- Auto-generated Kode Inventaris (Hidden) -->
            <input type="hidden" name="kode_inventaris" value="INV/ALKES/{{ date('Y') }}/{{ sprintf('%04d', rand(1000, 9999)) }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Nama Barang -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Barang / Alat Kesehatan <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_barang" value="{{ old('nama_barang') }}" required placeholder="Contoh: Infus Pump, Bed Patient, Defibrillator" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-teal-500">
                    @error('nama_barang') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
                </div>

                <!-- Nomor Seri -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nomor Seri (Serial Number / SN)</label>
                    <input type="text" name="nomor_seri" value="{{ old('nomor_seri') }}" placeholder="Contoh: SN-9812-77X, SK 10308902" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono focus:ring-2 focus:ring-teal-500">
                </div>

                <!-- Merk -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Merk / Produsen</label>
                    <input type="text" name="merk" value="{{ old('merk') }}" placeholder="Contoh: Paramount, Mindray, Philips" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                </div>

                <!-- Tipe -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Model / Tipe</label>
                    <input type="text" name="tipe" value="{{ old('tipe') }}" placeholder="Contoh: Series-90 Pro" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                </div>

                <!-- Tahun Pengadaan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tahun Pengadaan</label>
                    <input type="text" name="tahun_pengadaan" value="{{ old('tahun_pengadaan', date('Y')) }}" placeholder="Contoh: 2023" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                </div>

                <!-- Cara Perolehan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Cara Perolehan (Sumber Dana)</label>
                    <input type="text" name="cara_perolehan" value="{{ old('cara_perolehan') }}" placeholder="Contoh: DAK 2023, APBD 2021, HIBAH, BLUD, Beli Sendiri" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                </div>

                <!-- Nilai Perolehan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nilai Perolehan (Harga Rp)</label>
                    <input type="number" name="nilai_perolehan" value="{{ old('nilai_perolehan', 0) }}" placeholder="Contoh: 56000000" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono focus:ring-2 focus:ring-teal-500">
                </div>

                <!-- Jumlah Unit -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Jumlah Unit</label>
                    <input type="number" name="jumlah" value="{{ old('jumlah', 1) }}" min="1" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono focus:ring-2 focus:ring-teal-500">
                </div>

                <!-- Ruangan -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Penempatan Ruangan RS <span class="text-rose-500">*</span></label>
                    <select name="ruangan_id" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-teal-500">
                        <option value="">-- Pilih Ruangan --</option>
                        @foreach ($ruanganList as $ruang)
                            <option value="{{ $ruang->id }}" {{ old('ruangan_id') == $ruang->id ? 'selected' : '' }}>
                                {{ $ruang->nama_ruangan }} ({{ $ruang->lokasi_lantai ?? 'RS' }})
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
                    <select name="kondisi" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 font-bold">
                        @foreach ($kondisis as $kd)
                            <option value="{{ $kd->value }}">{{ $kd->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- ASPAK Status -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Status ASPAK Kemenkes</label>
                    <select name="aspak_status" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                        <option value="TERDATA">TERDATA</option>
                        <option value="TIDAK TERDATA">TIDAK TERDATA</option>
                    </select>
                </div>

                <!-- KIB Status -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Status KIB (Kartu Inventaris Barang)</label>
                    <select name="kib_status" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                        <option value="0">NON-KIB (FALSE)</option>
                        <option value="1">TERDAFTAR KIB (TRUE)</option>
                    </select>
                </div>

                <!-- Keterangan -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Keterangan / Catatan Spesifikasi</label>
                    <textarea name="keterangan" rows="3" placeholder="Catatan tambahan spesifikasi atau status Berita Acara..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">{{ old('keterangan') }}</textarea>
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
