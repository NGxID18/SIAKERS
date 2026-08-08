@extends('layouts.app')

@section('title', 'Riwayat Aktivitas Sistem')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <i class="ri-history-line text-teal-600"></i>
                Riwayat Aktivitas Sistem
            </h3>
            <p class="text-base text-slate-600 mt-1 font-normal">Pelacakan otomatis seluruh aktivitas pengguna, perubahan data, dan transaksi aset di Rumah Sakit</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('activity-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-sm font-semibold text-slate-800 mb-1.5">Cari Deskripsi / Pengguna / Ruangan</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Masukkan kata kunci..." class="w-full pl-10 pr-4 h-[46px] bg-slate-50 border border-slate-300 rounded-xl text-base font-normal focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <i class="ri-search-line absolute left-3.5 top-3.5 text-slate-400 text-lg"></i>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-800 mb-1.5">Tipe Aktivitas</label>
                <select name="action" class="w-full px-4 h-[46px] bg-slate-50 border border-slate-300 rounded-xl text-base font-medium focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <option value="">-- Semua Aktivitas --</option>
                    @foreach ($actionTypes as $type)
                        <option value="{{ $type }}" {{ request('action') == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2.5 justify-end">
                <button type="submit" class="h-[46px] px-6 bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm rounded-xl shadow-xs transition flex items-center justify-center gap-2 shrink-0">
                    <i class="ri-search-line text-lg"></i> Cari
                </button>
                @if (request()->hasAny(['search', 'action']))
                    <a href="{{ route('activity-logs.index') }}" class="h-[46px] w-[46px] bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-700 rounded-xl border border-slate-300 transition flex items-center justify-center shrink-0">
                        <i class="ri-refresh-line text-xl"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-teal-800 text-white border-b border-teal-900 text-xs font-bold uppercase tracking-wider">
                        <th class="px-6 py-4 border-r border-teal-700/60">Waktu & Tanggal (WIB)</th>
                        <th class="px-6 py-4 border-r border-teal-700/60">Peran & Ruangan Pengguna</th>
                        <th class="px-6 py-4 border-r border-teal-700/60">Aktivitas</th>
                        <th class="px-6 py-4 border-r border-teal-700/60">Deskripsi Perubahan Data</th>
                        <th class="px-6 py-4 text-center">Koneksi Perangkat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-sm font-normal text-slate-900">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-teal-50/50 transition odd:bg-white even:bg-slate-50/50">
                            <td class="px-6 py-4 whitespace-nowrap border-r border-slate-200">
                                <div class="font-semibold text-slate-900 text-sm">{{ $log->created_at->timezone('Asia/Jakarta')->format('d M Y') }}</div>
                                <div class="text-xs text-slate-500 font-mono mt-0.5">{{ $log->created_at->timezone('Asia/Jakarta')->format('H:i:s') }} WIB</div>
                            </td>

                            <td class="px-6 py-4 border-r border-slate-200">
                                <div class="font-semibold text-slate-900 text-sm">{{ $log->user_role }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">{{ $log->ruangan_name ?? 'Pusat' }}</div>
                            </td>

                            <td class="px-6 py-4 border-r border-slate-200">
                                <span class="px-3 py-1 rounded text-xs font-semibold bg-teal-100 text-teal-800 border border-teal-200">
                                    {{ $log->action }}
                                </span>
                            </td>

                            <td class="px-6 py-4 border-r border-slate-200">
                                <p class="text-sm text-slate-800 font-normal leading-relaxed">{{ $log->description }}</p>
                            </td>

                            <td class="px-6 py-4 text-center font-mono text-xs text-slate-500" title="Alamat IP Koneksi Perangkat">
                                {{ $log->ip_address }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-500 text-base">
                                <i class="ri-history-line text-5xl block mb-3 text-slate-300"></i>
                                Belum ada catatan aktivitas tercatat di sistem.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
            {{ $logs->links('pagination.custom') }}
        </div>
    </div>

</div>
@endsection
