@extends('layouts.app')

@section('title', 'System Audit Trail & Log Aktivitas')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight flex items-center gap-2.5">
                <i class="ri-history-line text-teal-600"></i>
                Audit Trail & Log Aktivitas Sistem
            </h3>
            <p class="text-sm text-slate-500">Pelacakan otomatis seluruh aktivitas pengguna, perubahan data, dan transaksi aset RS</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('activity-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Cari Deskripsi / Pengguna / Ruangan</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik kata kunci..." class="w-full pl-9 pr-3 h-[42px] bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-teal-500 shadow-xs">
                    <i class="ri-search-line absolute left-3 top-3 text-slate-400"></i>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Tipe Aktivitas</label>
                <select name="action" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 font-medium">
                    <option value="">-- Semua Aktivitas --</option>
                    @foreach ($actionTypes as $type)
                        <option value="{{ $type }}" {{ request('action') == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2 justify-end">
                <button type="submit" class="h-[42px] px-4 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center gap-1 shrink-0">
                    <i class="ri-search-line text-base"></i> Cari
                </button>
                @if (request()->hasAny(['search', 'action']))
                    <a href="{{ route('activity-logs.index') }}" class="h-[42px] w-[42px] bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-600 rounded-xl border border-slate-200 transition flex items-center justify-center shrink-0">
                        <i class="ri-refresh-line text-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs font-bold uppercase tracking-wider">
                        <th class="px-6 py-4">Waktu & Tanggal</th>
                        <th class="px-6 py-4">Peran & Ruangan Pengguna</th>
                        <th class="px-6 py-4">Aktivitas</th>
                        <th class="px-6 py-4">Deskripsi Perubahan Data</th>
                        <th class="px-6 py-4 text-center">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-slate-800">{{ $log->created_at->format('d M Y') }}</div>
                                <div class="text-xs text-slate-400">{{ $log->created_at->format('H:i:s') }} WIB</div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 text-xs">{{ $log->user_role }}</div>
                                <div class="text-xs text-slate-500">{{ $log->ruangan_name ?? 'RS Central' }}</div>
                            </td>

                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded text-xs font-extrabold bg-teal-100 text-teal-800 border border-teal-200">
                                    {{ $log->action }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <p class="text-xs text-slate-700 font-medium leading-relaxed">{{ $log->description }}</p>
                            </td>

                            <td class="px-6 py-4 text-center font-mono text-xs text-slate-400">
                                {{ $log->ip_address }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                                Belum ada log aktivitas tercatat di sistem.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
            {{ $logs->links() }}
        </div>
    </div>

</div>
@endsection
