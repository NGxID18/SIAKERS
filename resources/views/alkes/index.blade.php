@extends('layouts.app')

@section('title', 'Daftar Inventaris Alkes')

@section('content')
<div class="space-y-6">

    @php
        $currentSeksiId = request('seksi_id');
        $isMasterPage = ($currentSeksiId == 0 || $currentSeksiId == null);
        $isMySeksiPage = ($currentSeksiId == $userSeksiId);
        $canAddAlkes = ($isMasterPage || $isMySeksiPage);
        $activeSeksiObj = \App\Models\Seksi::find($currentSeksiId);
    @endphp

    <!-- Header Page & Action Button -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight">
                @if ($activeSeksiObj)
                    Inventaris {{ $activeSeksiObj->nama_seksi }}
                @else
                    Inventaris Alat Kesehatan (Seluruh RS)
                @endif
            </h3>
            <p class="text-sm text-slate-500">Kelola dan pantau aset alat medis terdaftar dalam sistem ERP</p>
        </div>

        {{-- Tombol Tambah HANYA muncul di Master Inventaris atau di Halaman Seksi Milik Sendiri --}}
        @if ($canAddAlkes)
            <a href="{{ route('alkes.create') }}" class="px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm rounded-xl shadow-lg shadow-teal-600/30 transition flex items-center gap-2">
                <i class="ri-add-line text-lg"></i>
                Tambah Alkes Baru
            </a>
        @endif
    </div>

    <!-- Filter Bar Card (Equal 42px Height & Instant Auto-Apply) -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('alkes.index') }}" id="filterForm" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">

            {{-- Tetap mempertahankan Seksi yang sedang aktif --}}
            @if ($currentSeksiId !== null)
                <input type="hidden" name="seksi_id" value="{{ $currentSeksiId }}">
            @endif

            <!-- Search (Fixed 42px Height) -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Cari Kode / Merk / Nama</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik & tekan Enter untuk cari..." class="w-full pl-9 pr-3 h-[42px] bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-teal-500 shadow-xs">
                    <i class="ri-search-line absolute left-3 top-3 text-slate-400"></i>
                </div>
            </div>

            <!-- Filter Lokasi Ruangan Alkes (Menampilkan Seluruh Lokasi RS) -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Lokasi Ruangan Alkes</label>
                <select name="ruangan_id" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 font-medium">
                    <option value="">-- Semua Lokasi Ruangan RS --</option>
                    @foreach ($ruanganList as $ruang)
                        <option value="{{ $ruang->id }}" {{ request('ruangan_id') == $ruang->id ? 'selected' : '' }}>
                            {{ $ruang->nama_ruangan }} ({{ $ruang->seksi->nama_seksi ?? 'RS' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status & Reset Button -->
            <div class="flex items-center gap-2">
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Status Penggunaan</label>
                    <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 font-medium">
                        <option value="">-- Semua Status --</option>
                        @foreach ($statuses as $st)
                            <option value="{{ $st->value }}" {{ request('status') == $st->value ? 'selected' : '' }}>{{ $st->label() }}</option>
                        @endforeach
                    </select>
                </div>

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
                        <th class="px-6 py-4">Lokasi Seksi & Ruangan</th>
                        <th class="px-6 py-4">Status Penggunaan</th>
                        <th class="px-6 py-4">Kondisi Fisik</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($alkesList as $alkes)
                        @php
                            $isMyItem = ($alkes->seksi_id == $userSeksiId);
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

                            <!-- Seksi & Ruangan -->
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800">{{ $alkes->seksi->nama_seksi ?? '-' }}</div>
                                <div class="text-xs text-slate-500">{{ $alkes->ruangan->nama_ruangan ?? 'Tanpa Spesifikasi Ruangan' }}</div>
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

                            <!-- Action -->
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('alkes.show', $alkes->id) }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition" title="Lihat Detail">
                                        <i class="ri-eye-line"></i>
                                    </a>

                                    {{-- Tombol CRUD (Edit, Mutasi, Perbaikan) HANYA ada untuk alkes seksi pengguna sendiri --}}
                                    @if ($isMyItem)
                                        <a href="{{ route('alkes.edit', $alkes->id) }}" class="p-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition" title="Edit Inventaris">
                                            <i class="ri-pencil-line"></i>
                                        </a>
                                        <a href="{{ route('mutasi.create', ['alkes_id' => $alkes->id]) }}" class="p-2 bg-teal-50 hover:bg-teal-100 text-teal-700 rounded-lg transition" title="Mutasi Seksi">
                                            <i class="ri-arrow-left-right-line"></i>
                                        </a>
                                        <a href="{{ route('pemeliharaan.create', ['alkes_id' => $alkes->id]) }}" class="p-2 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg transition" title="Lapor Perbaikan">
                                            <i class="ri-tools-line"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                                <i class="ri-inbox-line text-4xl block mb-2 text-slate-300"></i>
                                Tidak ditemukan data alat kesehatan yang sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
            {{ $alkesList->links() }}
        </div>
    </div>

</div>
@endsection
