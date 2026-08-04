@extends('layouts.app')

@section('title', 'Daftar Ruangan')

@section('content')
<div class="space-y-6">

    <!-- Header Title -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <i class="ri-building-4-line text-teal-600"></i>
                Daftar Ruangan
            </h3>
            <p class="text-base text-slate-600 mt-1 font-normal">Daftar 24 unit ruangan penempatan & lokasi fisik alat kesehatan terdaftar (Urut Abjad A-Z)</p>
        </div>
    </div>

    <!-- Clean Grid Cards for 24 Authentic Hospital Rooms -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach ($ruanganList as $ruang)
            <a href="{{ route('alkes.index', ['ruangan_id' => $ruang->id]) }}" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-teal-400 hover:bg-teal-50/40 transition group flex flex-col justify-between space-y-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center border border-teal-100 group-hover:bg-teal-600 group-hover:text-white transition">
                        <i class="ri-hospital-line text-xl"></i>
                    </div>
                    <span class="px-2.5 py-1 bg-slate-100 font-mono font-bold text-slate-700 text-xs rounded-lg border border-slate-200 group-hover:bg-teal-100 group-hover:text-teal-800 transition">
                        {{ $ruang->kode_ruangan }}
                    </span>
                </div>

                <div>
                    <h4 class="font-bold text-slate-900 text-lg group-hover:text-teal-700 transition leading-snug">{{ $ruang->nama_ruangan }}</h4>
                    <p class="text-sm text-slate-500 mt-1 font-normal"><i class="ri-door-open-line text-teal-600"></i> Unit Ruangan Terdaftar</p>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-sm font-semibold text-slate-800">{{ $ruang->alkes_count }} Unit Alkes</span>
                    <span class="text-sm font-semibold text-teal-600 group-hover:translate-x-1 transition flex items-center gap-0.5">
                        Lihat Alkes <i class="ri-arrow-right-s-line"></i>
                    </span>
                </div>
            </a>
        @endforeach
    </div>

</div>
@endsection
