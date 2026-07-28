@extends('layouts.app')

@section('title', 'Master Data Seksi & Ruangan RS')

@section('content')
<div class="space-y-6">

    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight">Seksi Operasional & Ruangan RS</h3>
            <p class="text-sm text-slate-500">Struktur seksi penunjang medis, pelayanan medis, keperawatan, dan distribusi ruangan</p>
        </div>
        @if (!$isAdmin)
            <span class="px-3 py-1.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 text-xs font-medium flex items-center gap-1.5">
                <i class="ri-eye-line text-slate-500"></i> Mode Tinjauan Structure RS (Read-Only)
            </span>
        @endif
    </div>

    <!-- Grid Seksi List & Modal Form -->
    <div class="grid grid-cols-1 {{ $isAdmin ? 'lg:grid-cols-3' : '' }} gap-6">

        {{-- Form Tambah Seksi HANYA muncul untuk Admin RS --}}
        @if ($isAdmin)
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                <h4 class="font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
                    <i class="ri-add-box-line text-teal-600 text-lg"></i>
                    Tambah Seksi Baru (Admin)
                </h4>

                <form method="POST" action="{{ route('seksi.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kode Seksi <span class="text-rose-500">*</span></label>
                        <input type="text" name="kode_seksi" placeholder="SEK-FARMASI" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 uppercase">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Seksi <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_seksi" placeholder="Seksi Farmasi & Alkes" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Penanggung Jawab</label>
                        <input type="text" name="penanggung_jawab" placeholder="Apt. Rahmawati, S.Farm" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kontak Person / Ekstensi</label>
                        <input type="text" name="kontak" placeholder="0812-xxxx-xxxx / Ext 104" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Keterangan Fungsi</label>
                        <textarea name="keterangan" rows="2" placeholder="Fungsi dan peran seksi..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500"></textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm rounded-xl shadow-lg shadow-teal-600/30 transition flex items-center justify-center gap-2">
                        <i class="ri-save-line"></i>
                        Simpan Seksi Baru
                    </button>
                </form>
            </div>
        @endif

        <!-- Daftar Seksi Existing -->
        <div class="{{ $isAdmin ? 'lg:col-span-2' : 'col-span-1' }} grid grid-cols-1 {{ !$isAdmin ? 'md:grid-cols-2' : '' }} gap-4">
            @foreach ($seksiList as $seksi)
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-start justify-between border-b border-slate-100 pb-3">
                            <div>
                                <span class="text-[10px] font-bold px-2.5 py-0.5 rounded bg-teal-100 text-teal-800 uppercase">{{ $seksi->kode_seksi }}</span>
                                <h4 class="font-extrabold text-slate-900 text-lg mt-1">{{ $seksi->nama_seksi }}</h4>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $seksi->keterangan ?? 'Seksi operasional RS' }}</p>
                            </div>
                            <a href="{{ route('alkes.index', ['seksi_id' => $seksi->id]) }}" class="px-3 py-1 bg-teal-50 hover:bg-teal-100 text-teal-700 rounded-full text-xs font-bold transition flex items-center gap-1">
                                {{ $seksi->alkes->count() }} Alkes &rarr;
                            </a>
                        </div>

                        <div class="space-y-2 text-xs text-slate-600">
                            <div>
                                <p><strong class="text-slate-800">Penanggung Jawab:</strong> {{ $seksi->penanggung_jawab ?? '-' }}</p>
                                <p class="mt-0.5"><strong class="text-slate-800">Kontak:</strong> {{ $seksi->kontak ?? '-' }}</p>
                            </div>

                            <!-- Ruangan di bawah seksi -->
                            <div class="pt-2 border-t border-slate-100">
                                <p class="font-bold text-slate-800 mb-1">Daftar Ruangan Terdaftar:</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @forelse ($seksi->ruangan as $ruang)
                                        <span class="px-2 py-0.5 rounded bg-slate-100 border border-slate-200 font-mono text-[11px] text-slate-700" title="{{ $ruang->lokasi_lantai }}">
                                            {{ $ruang->nama_ruangan }}
                                        </span>
                                    @empty
                                        <span class="text-slate-400 italic">Belum ada data ruangan khusus</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

</div>
@endsection
