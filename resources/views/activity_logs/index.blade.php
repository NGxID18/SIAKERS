@extends('layouts.app')

@section('title', 'Log Aktivitas System & Audit Trail')

@section('content')
<div class="space-y-6">

    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight flex items-center gap-2.5">
                <i class="ri-history-line text-teal-600"></i>
                Audit Trail Log Aktivitas Sistem
            </h3>
            <p class="text-sm text-slate-500">Memantau seluruh transaksi pergantian data inventaris, mutasi, dan perbaikan real-time</p>
        </div>
    </div>

    <!-- Filter Bar Card -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('activity-logs.index') }}" id="filterForm" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">

            <!-- Search -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Cari Deskripsi / Pengguna / Seksi</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik pencarian..." class="w-full pl-9 pr-3 h-[42px] bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-teal-500 shadow-xs">
                    <i class="ri-search-line absolute left-3 top-3 text-slate-400"></i>
                </div>
            </div>

            <!-- Filter Action Type -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Jenis Aksi Aktivitas</label>
                <select name="action" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 font-medium">
                    <option value="">-- Semua Jenis Aksi --</option>
                    @foreach ($actionTypes as $act)
                        <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>{{ $act }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Reset Button -->
            <div class="flex items-center gap-2">
                @if (request()->hasAny(['search', 'action']))
                    <a href="{{ route('activity-logs.index') }}" class="h-[42px] px-4 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-600 text-xs font-bold rounded-xl border border-slate-200 transition flex items-center justify-center gap-1.5" title="Reset Filter">
                        <i class="ri-refresh-line text-base"></i> Reset Filter
                    </a>
                @endif
            </div>

        </form>
    </div>

    <!-- Log Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs font-bold uppercase tracking-wider">
                        <th class="px-6 py-4">Waktu Transaksi</th>
                        <th class="px-6 py-4">Peran & Seksi Pengguna</th>
                        <th class="px-6 py-4">Jenis Aksi</th>
                        <th class="px-6 py-4">Deskripsi Aktivitas</th>
                        <th class="px-6 py-4">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($logs as $log)
                        @php
                            $actionBadge = 'bg-slate-100 text-slate-700 border-slate-200';
                            if ($log->action === 'Tambah Alkes') $actionBadge = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                            elseif ($log->action === 'Edit Alkes') $actionBadge = 'bg-blue-50 text-blue-700 border-blue-200';
                            elseif ($log->action === 'Hapus Alkes') $actionBadge = 'bg-rose-50 text-rose-700 border-rose-200';
                            elseif ($log->action === 'Mutasi Alkes') $actionBadge = 'bg-teal-50 text-teal-700 border-teal-200';
                            elseif ($log->action === 'Lapor Perbaikan') $actionBadge = 'bg-amber-50 text-amber-700 border-amber-200';
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- Waktu -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-slate-900 text-xs">{{ $log->created_at->format('d M Y, H:i') }}</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $log->created_at->diffForHumans() }}</div>
                            </td>

                            <!-- User & Seksi -->
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">{{ $log->user_role }}</div>
                                <div class="text-xs text-slate-500">{{ $log->seksi_name }}</div>
                            </td>

                            <!-- Jenis Aksi -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $actionBadge }}">
                                    {{ $log->action }}
                                </span>
                            </td>

                            <!-- Deskripsi -->
                            <td class="px-6 py-4">
                                <p class="text-slate-800 font-medium text-xs leading-relaxed">{{ $log->description }}</p>
                            </td>

                            <!-- IP Address -->
                            <td class="px-6 py-4 whitespace-nowrap text-xs font-mono text-slate-400">
                                {{ $log->ip_address }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                                <i class="ri-history-line text-4xl block mb-2 text-slate-300"></i>
                                Belum ada riwayat aktivitas sistem tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
            {{ $logs->links() }}
        </div>
    </div>

</div>
@endsection
