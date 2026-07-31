@extends('layouts.app')

@section('title', 'Daftar Ruangan')

@section('content')
<div class="space-y-8">

    <!-- Header Title -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight flex items-center gap-2.5">
                <i class="ri-building-4-line text-teal-600"></i>
                Daftar Ruangan
            </h3>
            <p class="text-sm text-slate-500">Unit penempatan & lokasi fisik alat kesehatan dikelompokkan berdasarkan <strong>Lantai & Gedung</strong></p>
        </div>
    </div>

    <!-- Grouped Ruangan by Lantai & Gedung -->
    @foreach ($ruanganGrouped as $lokasiLantai => $ruanganList)
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            
            <!-- Group Header Bar -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-teal-600 text-white font-bold flex items-center justify-center text-lg shadow-xs">
                        <i class="ri-building-2-line"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-slate-900 text-lg tracking-tight">{{ $lokasiLantai ?? 'Lantai & Gedung' }}</h4>
                        <span class="text-xs text-slate-400 font-medium">{{ $ruanganList->count() }} Ruangan Operasional Terdaftar</span>
                    </div>
                </div>
            </div>

            <!-- Grid Cards for Rooms in this Group -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 pt-1">
                @foreach ($ruanganList as $ruang)
                    <a href="{{ route('alkes.index', ['ruangan_id' => $ruang->id]) }}" class="bg-slate-50 p-4 rounded-xl border border-slate-200 shadow-2xs hover:shadow-md hover:border-teal-400 hover:bg-teal-50/40 transition group flex flex-col justify-between space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="w-9 h-9 rounded-lg bg-white text-teal-700 flex items-center justify-center border border-slate-200 group-hover:bg-teal-600 group-hover:text-white transition">
                                <i class="ri-hospital-line text-lg"></i>
                            </div>
                            <span class="px-2 py-0.5 bg-white font-mono font-bold text-slate-700 text-xs rounded border border-slate-200 group-hover:bg-teal-100 group-hover:text-teal-800 transition">
                                {{ $ruang->kode_ruangan }}
                            </span>
                        </div>

                        <div>
                            <h5 class="font-extrabold text-slate-900 text-base group-hover:text-teal-700 transition leading-snug">{{ $ruang->nama_ruangan }}</h5>
                            <p class="text-xs text-slate-500 mt-0.5"><i class="ri-map-pin-line text-teal-600"></i> {{ $ruang->lokasi_lantai ?? 'Lantai Penempatan' }}</p>
                        </div>

                        <div class="pt-2.5 border-t border-slate-200/80 flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-700">{{ $ruang->alkes_count }} Unit Alkes</span>
                            <span class="text-xs font-bold text-teal-600 group-hover:translate-x-1 transition flex items-center gap-0.5">
                                Lihat Alkes <i class="ri-arrow-right-s-line"></i>
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>

        </div>
    @endforeach

</div>
@endsection
