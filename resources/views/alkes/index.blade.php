@extends('layouts.app')

@php
    $currentRuanganId = request('ruangan_id', 0);
    $selectedRuanganObj = $ruanganList->firstWhere('id', $currentRuanganId);
    $pageTitle = $selectedRuanganObj ? 'Inventaris Alkes Ruang ' . $selectedRuanganObj->nama_ruangan : 'Daftar Seluruh Inventaris Alkes';

    // Current Sort Params
    $sortBy = request('sort_by', 'nama_barang');
    $sortDir = strtolower(request('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';

    // Helper function to generate column sort links
    function makeSortUrl($column, $currentSortBy, $currentSortDir) {
        $queryParams = request()->query();
        $queryParams['sort_by'] = $column;
        $queryParams['sort_dir'] = ($currentSortBy === $column && $currentSortDir === 'asc') ? 'desc' : 'asc';
        return route('alkes.index', $queryParams);
    }
@endphp

@section('title', $pageTitle)

@section('content')
<!-- Tom Select CSS & JS CDN -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

<style>
    .ts-wrapper {
        border-radius: 0.75rem !important;
    }
    .ts-control {
        border-radius: 0.75rem !important;
        background-color: #f8fafc !important;
        border: 1px solid #cbd5e1 !important;
        padding: 0.5rem 0.85rem !important;
        font-size: 0.875rem !important;
        font-weight: 600 !important;
        color: #0f172a !important;
        box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05) !important;
        transition: all 0.2s ease !important;
    }
    .ts-wrapper.focus .ts-control {
        border-color: #0d9488 !important;
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15) !important;
        background-color: #ffffff !important;
    }
    .ts-dropdown {
        border-radius: 0.85rem !important;
        border: 1px solid #0d9488 !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
        overflow: hidden !important;
        z-index: 9999 !important;
        padding: 4px !important;
        background: #ffffff !important;
    }
    .ts-dropdown .option {
        padding: 8px 12px !important;
        border-radius: 0.5rem !important;
        font-size: 0.875rem !important;
        font-weight: 600 !important;
        color: #334155 !important;
    }
    .ts-dropdown .option:hover, 
    .ts-dropdown .option.active {
        background-color: #0d9488 !important;
        color: #ffffff !important;
    }
</style>

<div class="space-y-6">

    <!-- Header Page & Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 tracking-tight flex items-center gap-2.5">
                <i class="ri-stethoscope-line text-teal-600"></i>
                {{ $pageTitle }}
            </h3>
            <p class="text-sm text-slate-500">
                @if ($selectedRuanganObj)
                    Menampilkan daftar seluruh unit alat kesehatan <strong>RUANG {{ strtoupper($selectedRuanganObj->nama_ruangan) }}</strong>
                @else
                    Kelola dan tinjau seluruh rekapan data alat kesehatan, kondisi fisik, dan lokasi penempatan unit
                @endif
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('alkes.create') }}" class="px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm rounded-xl shadow-md shadow-teal-600/30 transition flex items-center gap-2">
                <i class="ri-add-line text-lg"></i>
                Tambah Alkes
            </a>
        </div>
    </div>

    <!-- CARD 1: PENCARIAN UNIVERSAL DATA -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-2">
        <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider flex items-center gap-2">
            <i class="ri-search-2-line text-teal-600 text-base"></i>
            Pencarian Universal Data
        </label>
        <form method="GET" action="{{ route('alkes.index') }}" class="flex items-center gap-2">
            <!-- Retain current filters if searching -->
            @foreach (request()->except(['search', 'page']) as $k => $v)
                @if ($v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endif
            @endforeach

            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Masukkan kata kunci..." class="w-full pl-10 pr-4 h-[44px] bg-slate-50 border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white shadow-xs transition">
                <i class="ri-search-line absolute left-3.5 top-3 text-slate-400 text-lg"></i>
            </div>

            <button type="submit" class="h-[44px] px-6 bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm rounded-xl shadow-md shadow-teal-600/30 transition flex items-center gap-2 shrink-0">
                <i class="ri-search-line text-lg"></i> Cari Data
            </button>

            @if (request('search'))
                <a href="{{ route('alkes.index', request()->except('search')) }}" class="h-[44px] px-4 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-600 font-bold text-sm rounded-xl border border-slate-200 transition flex items-center justify-center shrink-0" title="Bersihkan Pencarian">
                    <i class="ri-close-circle-line text-lg"></i> Reset Cari
                </a>
            @endif
        </form>
    </div>

    <!-- CARD 2: PENYARINGAN RINGKAS (RUANGAN, LOKASI, KONDISI) -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
        <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-2">
            <i class="ri-filter-3-line text-teal-600 text-base"></i>
            Filter Utama Inventaris
        </label>

        <form method="GET" action="{{ route('alkes.index') }}" id="filterForm">
            <!-- Retain search and sorting when filtering -->
            @if (request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            @if (request('sort_by'))
                <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
            @endif
            @if (request('sort_dir'))
                <input type="hidden" name="sort_dir" value="{{ request('sort_dir') }}">
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-4 items-end">
                
                <!-- Filter 1: Ruangan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Ruangan</label>
                    <select id="selectRuangan" name="ruangan_id">
                        <option value="">-- Semua Ruangan --</option>
                        @foreach ($ruanganList as $ruang)
                            <option value="{{ $ruang->id }}" {{ request('ruangan_id') == $ruang->id ? 'selected' : '' }}>
                                {{ $ruang->nama_ruangan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter 2: Lokasi Saat Ini -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Lokasi Saat Ini</label>
                    <select id="selectLokasi" name="lokasi_ruangan_id">
                        <option value="">-- Semua Lokasi Fisik --</option>
                        @foreach ($ruanganList as $ruang)
                            <option value="{{ $ruang->id }}" {{ request('lokasi_ruangan_id') == $ruang->id ? 'selected' : '' }}>
                                {{ $ruang->nama_ruangan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter 3: Kondisi Alat -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kondisi Alat</label>
                    <select id="selectKondisi" name="kondisi">
                        <option value="">-- Semua Kondisi Alat --</option>
                        @foreach ($kondisis as $kd)
                            <option value="{{ $kd->value }}" {{ request('kondisi') == $kd->value ? 'selected' : '' }}>{{ $kd->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Action Buttons Bar: RESET FILTER DI SEBELAH KIRI TERAPKAN FILTER -->
                <div class="flex items-center gap-2 justify-end">
                    <a href="{{ route('alkes.index') }}" class="h-[42px] px-4 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-600 font-bold text-xs rounded-xl border border-slate-200 transition flex items-center justify-center gap-1.5 shrink-0" title="Reset Semua Filter & Pencarian">
                        <i class="ri-refresh-line text-base"></i> Reset Filter
                    </a>

                    <button type="submit" class="h-[42px] px-6 bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center gap-2 shrink-0">
                        <i class="ri-filter-3-line text-base"></i> Terapkan Filter
                    </button>
                </div>

            </div>

        </form>
    </div>

    <!-- Excel-Style Interactive Data Table Card -->
    <div class="bg-white rounded-2xl border border-slate-300 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse border border-slate-300 text-xs">
                <thead>
                    <tr class="bg-emerald-900 text-white border-b border-emerald-950 text-[11px] font-extrabold uppercase tracking-wider select-none">
                        <th class="px-3 py-3 text-center border-r border-emerald-800 w-12">No</th>
                        
                        <!-- Clickable Column Headers with Sort Icons -->
                        <th class="px-4 py-3 border-r border-emerald-800 min-w-[180px]">
                            <a href="{{ makeSortUrl('nama_barang', $sortBy, $sortDir) }}" class="flex items-center justify-between hover:text-teal-200 transition" title="Klik untuk mengurutkan A-Z / Z-A">
                                <span>Nama Barang</span>
                                <i class="ri-arrow-up-down-line text-xs opacity-75 {{ $sortBy == 'nama_barang' ? 'text-teal-300 font-black opacity-100' : '' }}"></i>
                            </a>
                        </th>

                        <th class="px-3 py-3 border-r border-emerald-800 min-w-[110px]">
                            <a href="{{ makeSortUrl('merk', $sortBy, $sortDir) }}" class="flex items-center justify-between hover:text-teal-200 transition" title="Klik untuk mengurutkan A-Z / Z-A">
                                <span>Merk</span>
                                <i class="ri-arrow-up-down-line text-xs opacity-75 {{ $sortBy == 'merk' ? 'text-teal-300 font-black opacity-100' : '' }}"></i>
                            </a>
                        </th>

                        <th class="px-3 py-3 border-r border-emerald-800 min-w-[110px]">
                            <a href="{{ makeSortUrl('tipe', $sortBy, $sortDir) }}" class="flex items-center justify-between hover:text-teal-200 transition" title="Klik untuk mengurutkan A-Z / Z-A">
                                <span>Tipe</span>
                                <i class="ri-arrow-up-down-line text-xs opacity-75 {{ $sortBy == 'tipe' ? 'text-teal-300 font-black opacity-100' : '' }}"></i>
                            </a>
                        </th>

                        <th class="px-3 py-3 border-r border-emerald-800 min-w-[140px]">
                            <a href="{{ makeSortUrl('nomor_seri', $sortBy, $sortDir) }}" class="flex items-center justify-between hover:text-teal-200 transition" title="Klik untuk mengurutkan A-Z / Z-A">
                                <span>Serial Number</span>
                                <i class="ri-arrow-up-down-line text-xs opacity-75 {{ $sortBy == 'nomor_seri' ? 'text-teal-300 font-black opacity-100' : '' }}"></i>
                            </a>
                        </th>

                        <th class="px-3 py-3 text-center border-r border-emerald-800 w-16">
                            <a href="{{ makeSortUrl('tahun_pengadaan', $sortBy, $sortDir) }}" class="flex items-center justify-center gap-1 hover:text-teal-200 transition" title="Klik untuk mengurutkan Tahun">
                                <span>Tahun</span>
                                <i class="ri-arrow-up-down-line text-xs opacity-75 {{ $sortBy == 'tahun_pengadaan' ? 'text-teal-300 font-black opacity-100' : '' }}"></i>
                            </a>
                        </th>

                        <th class="px-3 py-3 text-center border-r border-emerald-800 w-16">
                            <a href="{{ makeSortUrl('jumlah', $sortBy, $sortDir) }}" class="flex items-center justify-center gap-1 hover:text-teal-200 transition" title="Klik untuk mengurutkan Jumlah">
                                <span>Jumlah</span>
                                <i class="ri-arrow-up-down-line text-xs opacity-75 {{ $sortBy == 'jumlah' ? 'text-teal-300 font-black opacity-100' : '' }}"></i>
                            </a>
                        </th>

                        <th class="px-3 py-3 border-r border-emerald-800 min-w-[130px]">
                            <a href="{{ makeSortUrl('cara_perolehan', $sortBy, $sortDir) }}" class="flex items-center justify-between hover:text-teal-200 transition" title="Klik untuk mengurutkan Perolehan">
                                <span>Cara Perolehan</span>
                                <i class="ri-arrow-up-down-line text-xs opacity-75 {{ $sortBy == 'cara_perolehan' ? 'text-teal-300 font-black opacity-100' : '' }}"></i>
                            </a>
                        </th>

                        <th class="px-3 py-3 border-r border-emerald-800 min-w-[130px]">
                            <a href="{{ makeSortUrl('nilai_perolehan', $sortBy, $sortDir) }}" class="flex items-center justify-between hover:text-teal-200 transition" title="Klik untuk mengurutkan Harga">
                                <span>Nilai Perolehan</span>
                                <i class="ri-arrow-up-down-line text-xs opacity-75 {{ $sortBy == 'nilai_perolehan' ? 'text-teal-300 font-black opacity-100' : '' }}"></i>
                            </a>
                        </th>

                        <th class="px-3 py-3 border-r border-emerald-800 min-w-[110px]">
                            <a href="{{ makeSortUrl('ruangan', $sortBy, $sortDir) }}" class="flex items-center justify-between hover:text-teal-200 transition" title="Klik untuk mengurutkan Ruangan">
                                <span>Ruangan</span>
                                <i class="ri-arrow-up-down-line text-xs opacity-75 {{ $sortBy == 'ruangan' ? 'text-teal-300 font-black opacity-100' : '' }}"></i>
                            </a>
                        </th>

                        <th class="px-3 py-3 border-r border-emerald-800 min-w-[130px]">Lokasi Saat Ini</th>

                        <th class="px-3 py-3 border-r border-emerald-800 min-w-[110px]">
                            <a href="{{ makeSortUrl('kondisi', $sortBy, $sortDir) }}" class="flex items-center justify-between hover:text-teal-200 transition" title="Klik untuk mengurutkan Kondisi">
                                <span>Kondisi Alat</span>
                                <i class="ri-arrow-up-down-line text-xs opacity-75 {{ $sortBy == 'kondisi' ? 'text-teal-300 font-black opacity-100' : '' }}"></i>
                            </a>
                        </th>

                        <th class="px-3 py-3 text-center border-r border-emerald-800 w-24">ASPAK</th>
                        <th class="px-3 py-3 text-center border-r border-emerald-800 w-20">KIB</th>
                        <th class="px-4 py-3 border-r border-emerald-800 min-w-[140px]">Keterangan</th>
                        <th class="px-3 py-3 text-center min-w-[120px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 font-medium text-slate-800">
                    @forelse ($alkesList as $index => $alkes)
                        @php
                            $rowNumber = $alkesList->firstItem() + $index;
                        @endphp
                        <tr class="hover:bg-teal-50/60 transition odd:bg-white even:bg-slate-50/50 border-b border-slate-200">
                            <!-- No -->
                            <td class="px-3 py-2.5 text-center font-bold text-slate-500 border-r border-slate-200">
                                {{ $rowNumber }}
                            </td>

                            <!-- Nama Barang -->
                            <td class="px-4 py-2.5 font-extrabold text-slate-900 border-r border-slate-200">
                                {{ $alkes->nama_barang }}
                            </td>

                            <!-- Merk -->
                            <td class="px-3 py-2.5 font-bold text-teal-800 border-r border-slate-200">
                                {{ $alkes->merk ?: '-' }}
                            </td>

                            <!-- Tipe -->
                            <td class="px-3 py-2.5 text-slate-700 border-r border-slate-200">
                                {{ $alkes->tipe ?: '-' }}
                            </td>

                            <!-- Serial Number -->
                            <td class="px-3 py-2.5 font-mono font-bold text-slate-800 border-r border-slate-200">
                                {{ $alkes->nomor_seri ?: '-' }}
                            </td>

                            <!-- Tahun -->
                            <td class="px-3 py-2.5 text-center font-semibold text-blue-700 border-r border-slate-200">
                                {{ $alkes->tahun_pengadaan ?: '-' }}
                            </td>

                            <!-- Jumlah -->
                            <td class="px-3 py-2.5 text-center font-extrabold text-slate-900 border-r border-slate-200">
                                {{ $alkes->jumlah }}
                            </td>

                            <!-- Cara Perolehan -->
                            <td class="px-3 py-2.5 text-slate-700 border-r border-slate-200">
                                {{ $alkes->cara_perolehan ?: '-' }}
                            </td>

                            <!-- Nilai Perolehan -->
                            <td class="px-3 py-2.5 font-mono font-semibold text-emerald-700 border-r border-slate-200">
                                {{ $alkes->nilai_perolehan > 0 ? 'Rp ' . number_format($alkes->nilai_perolehan, 0, ',', '.') : '-' }}
                            </td>

                            <!-- Ruangan -->
                            <td class="px-3 py-2.5 font-bold text-slate-800 border-r border-slate-200">
                                {{ $alkes->ruangan->nama_ruangan ?? '-' }}
                            </td>

                            <!-- Lokasi Saat Ini -->
                            <td class="px-3 py-2.5 border-r border-slate-200">
                                @if ($alkes->lokasi_saat_ini_note)
                                    <span class="font-bold text-amber-800 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200">
                                        {{ $alkes->lokasi_saat_ini_note }}
                                    </span>
                                @elseif ($alkes->ruangan_id != $alkes->lokasi_ruangan_id)
                                    <span class="font-bold text-teal-800">
                                        {{ $alkes->lokasiRuangan->nama_ruangan ?? '-' }}
                                    </span>
                                @else
                                    <span class="text-slate-500">{{ $alkes->ruangan->nama_ruangan ?? '-' }}</span>
                                @endif
                            </td>

                            <!-- Kondisi Alat -->
                            <td class="px-3 py-2.5 border-r border-slate-200">
                                <span class="inline-block px-2 py-0.5 rounded text-[11px] font-extrabold border {{ $alkes->kondisi_enum->warnaBadge() }}">
                                    {{ $alkes->kondisi_enum->label() }}
                                </span>
                            </td>

                            <!-- ASPAK -->
                            <td class="px-3 py-2.5 text-center border-r border-slate-200">
                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold {{ $alkes->aspak_status == 'TERDATA' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                    {{ $alkes->aspak_status ?? 'TERDATA' }}
                                </span>
                            </td>

                            <!-- KIB -->
                            <td class="px-3 py-2.5 text-center border-r border-slate-200">
                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold {{ $alkes->kib_status ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                    {{ $alkes->kib_status ? 'TRUE' : 'FALSE' }}
                                </span>
                            </td>

                            <!-- Keterangan -->
                            <td class="px-4 py-2.5 text-slate-600 border-r border-slate-200 max-w-xs truncate" title="{{ $alkes->keterangan }}">
                                {{ $alkes->keterangan ?: '-' }}
                            </td>

                            <!-- Aksi -->
                            <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    <!-- Detail -->
                                    <a href="{{ route('alkes.show', $alkes->id) }}" class="p-1.5 text-teal-700 hover:bg-teal-100 rounded transition" title="Lihat Detail">
                                        <i class="ri-eye-line text-base"></i>
                                    </a>

                                    <!-- Pindah Ruangan Alat -->
                                    <a href="{{ route('mutasi.create', ['alkes_id' => $alkes->id]) }}" class="p-1.5 text-blue-700 hover:bg-blue-100 rounded transition" title="Pindah Ruangan Alat">
                                        <i class="ri-arrow-left-right-line text-base"></i>
                                    </a>

                                    <!-- Lapor Perbaikan -->
                                    <a href="{{ route('pemeliharaan.create', ['alkes_id' => $alkes->id]) }}" class="p-1.5 text-amber-700 hover:bg-amber-100 rounded transition" title="Lapor Perbaikan">
                                        <i class="ri-tools-line text-base"></i>
                                    </a>

                                    <!-- Edit -->
                                    <a href="{{ route('alkes.edit', $alkes->id) }}" class="p-1.5 text-slate-700 hover:bg-slate-200 rounded transition" title="Edit Data">
                                        <i class="ri-edit-line text-base"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="16" class="px-6 py-8 text-center text-slate-400">
                                <i class="ri-inbox-line text-4xl block mb-2 text-slate-300"></i>
                                Tidak ada data alat kesehatan ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- SIAKER Custom Pagination UI -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
            {{ $alkesList->links() }}
        </div>
    </div>

</div>

<!-- Interactive TomSelect Initializer Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new TomSelect('#selectRuangan', {
            create: false,
            placeholder: '-- Semua Ruangan --',
            maxOptions: 50,
        });

        new TomSelect('#selectLokasi', {
            create: false,
            placeholder: '-- Semua Lokasi Fisik --',
            maxOptions: 50,
        });

        new TomSelect('#selectKondisi', {
            create: false,
            placeholder: '-- Semua Kondisi Alat --',
            maxOptions: 10,
        });
    });
</script>
@endsection
