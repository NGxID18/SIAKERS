@extends('layouts.app')

@section('title', 'Daftar Ruangan')

@section('content')
<div class="space-y-6">

    <div>
        <h3 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-3">
            <i class="ri-building-4-line text-emerald-600"></i>
            Daftar Ruangan Rumah Sakit
        </h3>
        <p class="text-sm text-slate-700 mt-1 font-medium">{{ $ruanganList->count() }} unit ruangan terdaftar di RSJKO Engku Haji Daud beserta jumlah alkes aktif</p>
    </div>

    @php
        $bgColors = [
            'bg-emerald-50 text-emerald-700 hover:bg-emerald-600',
            'bg-teal-50 text-teal-700 hover:bg-teal-600',
            'bg-indigo-50 text-indigo-700 hover:bg-indigo-600',
            'bg-amber-50 text-amber-700 hover:bg-amber-600',
            'bg-cyan-50 text-cyan-700 hover:bg-cyan-600',
            'bg-purple-50 text-purple-700 hover:bg-purple-600',
        ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach ($ruanganList as $index => $ruang)
            @php $colorClass = $bgColors[$index % count($bgColors)]; @endphp
            <a href="{{ route('alkes.index', ['ruangan_id' => $ruang->id]) }}" class="bg-white p-5 rounded-2xl border border-slate-200/90 hover:border-emerald-500 hover:shadow-md transition-all duration-200 group flex flex-col justify-between space-y-4">
                <div class="flex items-start justify-between gap-2">
                    <div class="w-10 h-10 rounded-xl {{ explode(' hover:', $colorClass)[0] }} flex items-center justify-center text-lg font-bold shrink-0 group-hover:text-white transition">
                        <i class="ri-hospital-line"></i>
                    </div>
                    <span class="px-2.5 py-1 bg-slate-100 font-mono text-slate-800 text-xs font-bold rounded-lg border border-slate-300 group-hover:bg-amber-100 group-hover:text-amber-900 group-hover:border-amber-300 transition">
                        {{ $ruang->kode_ruangan }}
                    </span>
                </div>

                <div>
                    <h4 class="font-extrabold text-slate-900 text-base group-hover:text-emerald-700 transition leading-snug">{{ $ruang->nama_ruangan }}</h4>
                    <p class="text-xs text-slate-600 font-semibold mt-1">Unit Ruangan Terdaftar</p>
                </div>

                <div class="pt-3 border-t border-slate-200 flex items-center justify-between">
                    <span class="text-xs font-black text-slate-900 bg-slate-100 px-3 py-1 rounded-lg border border-slate-300 group-hover:border-emerald-400 group-hover:text-emerald-700 transition">
                        {{ $ruang->alkes_count }} Unit Alkes
                    </span>
                    <span class="text-xs font-bold text-emerald-700 group-hover:translate-x-0.5 transition flex items-center gap-0.5">
                        Lihat <i class="ri-arrow-right-s-line text-sm"></i>
                    </span>
                </div>
            </a>
        @endforeach
    </div>

</div>
@endsection
