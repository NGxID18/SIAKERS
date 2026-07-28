@extends('layouts.app')

@php
    $currentSeksiId = request('seksi_id', 0);
    $selectedSeksiObj = $seksiList->firstWhere('id', $currentSeksiId);
    $pageTitle = $selectedSeksiObj ? 'Inventaris Alkes ' . $selectedSeksiObj->nama_seksi : 'Daftar Seluruh Inventaris Alkes RS';
@endphp

@section('title', $pageTitle)

@section('content')
<div class="space-y-6">

    <!-- Header Page & Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight flex items-center gap-2.5">
                <i class="ri-stethoscope-line text-teal-600"></i>
                {{ $pageTitle }}
            </h3>
            <p class="text-sm text-slate-500">
                @if ($selectedSeksiObj)
                    Menampilkan daftar seluruh unit alat kesehatan <strong>MILIK {{ strtoupper($selectedSeksiObj->nama_seksi) }}</strong>
                @else
                    Menampilkan seluruh data alat kesehatan terdaftar di Rumah Sakit
                @endif
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if ($userSeksiId)
                <a href="{{ route('alkes.create') }}" class="px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm rounded-xl shadow-md shadow-teal-600/30 transition flex items-center gap-2">
                    <i class="ri-add-line text-lg"></i>
                    Tambah Alkes
                </a>
            @endif
        </div>
    </div>

    <!-- Integrated Dynamic Auto-Apply Filter Bar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('alkes.index') }}" id="filterForm" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            
            <input type="hidden" name="seksi_id" value="{{ $currentSeksiId }}">

            <!-- Search Field -->
            <div class="lg:col-span-1">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Cari Kode / Nama / SN / Merk</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik kata kunci..." class="w-full pl-9 pr-3 h-[42px] bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-teal-500 shadow-xs">
                    <i class="ri-search-line absolute left-3 top-3 text-slate-400"></i>
                </div>
            </div>

            <!-- Filter Lokasi Keberadaan Fisik Alkes (Menampilkan SELURUH Ruangan RS) -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Lokasi Fisik Alkes</label>
                <select name="ruangan_id" id="filter_ruangan_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 font-medium">
                    <option value="">-- Semua Lokasi Ruangan RS --</option>
                    @foreach ($ruanganList as $ruang)
                        <option value="{{ $ruang->id }}" {{ request('ruangan_id') == $ruang->id ? 'selected' : '' }}>
                            {{ $ruang->nama_ruangan }} ({{ $ruang->seksi->nama_seksi ?? 'Seksi' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status Penggunaan -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Status Penggunaan</label>
                <select name="status" id="filter_status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 font-medium">
                    <option value="">-- Semua Status --</option>
                    @foreach ($statuses as $st)
                        <option value="{{ $st->value }}" {{ request('status') == $st->value ? 'selected' : '' }}>{{ $st->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Kondisi Fisik -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Kondisi Fisik</label>
                <select name="kondisi" id="filter_kondisi" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 font-medium">
                    <option value="">-- Semua Kondisi --</option>
                    @foreach ($kondisis as $kd)
                        <option value="{{ $kd->value }}" {{ request('kondisi') == $kd->value ? 'selected' : '' }}>{{ $kd->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2 justify-end">
                <button type="submit" class="h-[42px] px-4 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center gap-1 shrink-0" title="Cari">
                    <i class="ri-search-line text-base"></i> Cari
                </button>

                @if (request()->hasAny(['search', 'ruangan_id', 'status', 'kondisi']))
                    <a href="{{ route('alkes.index', ['seksi_id' => $currentSeksiId]) }}" class="h-[42px] w-[42px] bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-600 rounded-xl border border-slate-200 transition flex items-center justify-center shrink-0" title="Reset Filter">
                        <i class="ri-refresh-line text-lg"></i>
                    </a>
                @endif
            </div>

        </form>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs font-bold uppercase tracking-wider">
                        <th class="px-6 py-4">Kode & Nama Alat</th>
                        <th class="px-6 py-4">Merk / Tipe / No Seri</th>
                        <th class="px-6 py-4">Seksi Pemilik Aset</th>
                        <th class="px-6 py-4">Lokasi Fisik Saat Ini</th>
                        <th class="px-6 py-4">Status Penggunaan</th>
                        <th class="px-6 py-4">Kondisi Fisik</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($alkesList as $alkes)
                        @php
                            $isMyItem = ($alkes->seksi_pemilik_id == $userSeksiId);
                            $isMovedOut = ($alkes->seksi_pemilik_id != $alkes->lokasi_seksi_id);
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- Kode & Nama -->
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $alkes->nomenklatur->nama_alat ?? 'Nomenklatur Unmapped' }}</div>
                                <div class="text-xs text-slate-400 font-mono mt-0.5">{{ $alkes->kode_inventaris }}</div>
                            </td>

                            <!-- Merk & Tipe -->
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800">{{ $alkes->merk ?? '-' }} <span class="text-slate-500 font-normal">({{ $alkes->tipe ?? '-' }})</span></div>
                                <div class="text-xs text-slate-400">SN: {{ $alkes->nomor_seri ?? '-' }}</div>
                            </td>

                            <!-- Seksi Pemilik Permanen -->
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800 flex items-center gap-1.5">
                                    <i class="ri-shield-user-line text-teal-600"></i>
                                    {{ $alkes->seksiPemilik->nama_seksi ?? '-' }}
                                </div>
                            </td>

                            <!-- Lokasi Keberadaan Fisik Saat Ini -->
                            <td class="px-6 py-4">
                                @if ($isMovedOut)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-extrabold bg-amber-100 text-amber-900 border border-amber-300 shadow-2xs" title="Barang milik {{ $alkes->seksiPemilik->nama_seksi }} yang dipindahkan lokasi fisiknya">
                                        <i class="ri-map-pin-user-line text-amber-700 text-sm"></i>
                                        Dipindahkan ke {{ $alkes->lokasiSeksi->nama_seksi }} ({{ $alkes->ruangan->nama_ruangan ?? '-' }})
                                    </span>
                                @else
                                    <div class="font-semibold text-slate-800 flex items-center gap-1.5">
                                        <i class="ri-building-line text-slate-400"></i>
                                        {{ $alkes->ruangan->nama_ruangan ?? 'Ruangan Seksi' }}
                                    </div>
                                    <div class="text-[11px] text-slate-400">Di Seksi Pemilik</div>
                                @endif
                            </td>

                            <!-- Status Penggunaan -->
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $alkes->status_enum->warnaBadge() }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                    {{ $alkes->status_enum->label() }}
                                </span>
                            </td>

                            <!-- Kondisi Fisik -->
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold border {{ $alkes->kondisi_enum->warnaBadge() }}">
                                    {{ $alkes->kondisi_enum->label() }}
                                </span>
                            </td>

                            <!-- Aksi -->
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    <!-- Detail -->
                                    <a href="{{ route('alkes.show', $alkes->id) }}" class="p-2 text-teal-600 hover:bg-teal-50 rounded-lg transition" title="Lihat Detail">
                                        <i class="ri-eye-line text-lg"></i>
                                    </a>

                                    @if ($isMyItem || session('is_admin'))
                                        <!-- Pindah Lokasi Alat -->
                                        <a href="{{ route('mutasi.create', ['alkes_id' => $alkes->id]) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Pindah Lokasi Alat">
                                            <i class="ri-arrow-left-right-line text-lg"></i>
                                        </a>

                                        <!-- Lapor Perbaikan -->
                                        <a href="{{ route('pemeliharaan.create', ['alkes_id' => $alkes->id]) }}" class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition" title="Lapor Perbaikan">
                                            <i class="ri-tools-line text-lg"></i>
                                        </a>

                                        <!-- Edit -->
                                        <a href="{{ route('alkes.edit', $alkes->id) }}" class="p-2 text-slate-600 hover:bg-slate-100 rounded-lg transition" title="Edit Data">
                                            <i class="ri-edit-line text-lg"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-400">
                                <i class="ri-inbox-line text-4xl block mb-2 text-slate-300"></i>
                                Tidak ada data alat kesehatan ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- SIAKER Custom Pagination UI (30 items per page with Direct Page Jump) -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
            {{ $alkesList->links() }}
        </div>
    </div>

</div>
@endsection
