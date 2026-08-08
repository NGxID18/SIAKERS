@extends('layouts.app')

@section('title', 'Daftar Ruangan')

@section('content')
<div class="space-y-6">

    <!-- Header Title Banner -->
    <div class="bg-gradient-to-r from-teal-900 via-teal-800 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 text-teal-300 font-semibold text-xs tracking-wider uppercase mb-1">
                <i class="ri-building-4-line text-lg"></i>
                <span>Master Unit Penempatan & Lokasi Fisik Alkes</span>
            </div>
            <h3 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Daftar Ruangan RSJKO Engku Haji Daud</h3>
            <p class="text-slate-300 text-sm mt-1 max-w-2xl">
                Menampilkan {{ $ruanganList->count() }} unit ruangan terdaftar (Urut Abjad A-Z) beserta rekapitulasi jumlah aset inventaris di setiap ruangan.
            </p>
        </div>
    </div>

    <!-- Elevated Grid Cards for All Hospital Rooms -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
        @foreach ($ruanganList as $ruang)
            <a href="{{ route('alkes.index', ['ruangan_id' => $ruang->id]) }}" class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-lg shadow-slate-200/50 hover:shadow-xl hover:shadow-teal-900/10 hover:border-teal-400 hover:-translate-y-1 transition-all duration-300 group flex flex-col justify-between space-y-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center border border-teal-100 group-hover:bg-teal-600 group-hover:text-white transition-all duration-300 shadow-sm shrink-0">
                        <i class="ri-hospital-line text-2xl"></i>
                    </div>
                    <span class="px-3 py-1 bg-slate-100 font-mono font-bold text-slate-700 text-xs rounded-xl border border-slate-200 group-hover:bg-teal-100 group-hover:text-teal-800 transition">
                        {{ $ruang->kode_ruangan }}
                    </span>
                </div>

                <div>
                    <h4 class="font-extrabold text-slate-800 text-base sm:text-lg group-hover:text-teal-600 transition-colors leading-snug">{{ $ruang->nama_ruangan }}</h4>
                    <p class="text-xs text-slate-500 mt-1 font-medium"><i class="ri-door-open-line text-teal-600"></i> Unit Ruangan Terdaftar</p>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-800 bg-slate-100 px-3 py-1 rounded-xl border border-slate-200 group-hover:border-teal-300 group-hover:text-teal-700 transition">
                        {{ $ruang->alkes_count }} Unit Alkes
                    </span>
                    <span class="text-xs font-bold text-teal-600 group-hover:translate-x-1 transition flex items-center gap-0.5">
                        Lihat Alkes <i class="ri-arrow-right-s-line"></i>
                    </span>
                </div>
            </a>
        @endforeach
    </div>

</div>
@endsection
