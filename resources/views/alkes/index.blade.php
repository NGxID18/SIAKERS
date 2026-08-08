@extends('layouts.app')

@php
    $currentRuanganId = request('ruangan_id', 0);
    $selectedRuanganObj = $ruanganList->firstWhere('id', $currentRuanganId);
    $pageTitle = $selectedRuanganObj ? 'Inventaris Alkes Ruang ' . $selectedRuanganObj->nama_ruangan : 'Daftar Seluruh Inventaris Alkes';

    // Current Sort Params
    $sortBy = request('sort_by', 'nama_barang');
    $sortDir = strtolower(request('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';
    $currentRole = session('user_role', 'elektromedis');

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


<div class="space-y-6">

    <!-- Flash Error Alert Jika Akses Ditolak -->
    @if (session('error'))
        <div id="flashErrMsg" class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-rose-800 font-semibold text-sm flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2.5">
                <i class="ri-error-warning-line text-xl text-rose-600"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="document.getElementById('flashErrMsg').remove()" class="text-rose-500 hover:text-rose-800 text-lg">
                <i class="ri-close-line"></i>
            </button>
        </div>
    @endif

    <!-- Header Page & Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <i class="ri-stethoscope-line text-teal-600"></i>
                {{ $pageTitle }}
            </h3>
            <p class="text-base text-slate-600 mt-1 font-normal">
                @if ($selectedRuanganObj)
                    Menampilkan daftar seluruh unit alat kesehatan milik <strong>RUANG {{ strtoupper($selectedRuanganObj->nama_ruangan) }}</strong>
                @else
                    Kelola dan tinjau seluruh rekapan data alat kesehatan, kondisi fisik, dan lokasi penempatan unit
                @endif
            </p>
        </div>

        <!-- Tombol Tambah Alkes (KHUSUS ELEKTROMEDIS ADMIN) -->
        @if ($currentRole === 'elektromedis')
            <div class="flex items-center gap-2">
                <a href="{{ route('alkes.create') }}" class="px-5 py-3 bg-teal-600 hover:bg-teal-700 text-white font-semibold text-base rounded-xl shadow-md shadow-teal-600/30 transition flex items-center gap-2">
                    <i class="ri-add-line text-xl"></i>
                    Tambah Alkes
                </a>
            </div>
        @endif
    </div>

    <!-- CARD 1: PENCARIAN UNIVERSAL DATA -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3">
        <label class="block text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
            <i class="ri-search-2-line text-teal-600 text-lg"></i>
            Pencarian Universal Data
        </label>
        <form method="GET" action="{{ route('alkes.index') }}" class="flex items-center gap-3">
            <!-- Retain current filters if searching -->
            @foreach (request()->except(['search', 'page']) as $k => $v)
                @if ($v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endif
            @endforeach

            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Masukkan kata kunci..." class="w-full pl-11 pr-4 h-[48px] bg-slate-50 border border-slate-300 rounded-xl text-base font-normal text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white shadow-xs transition">
                <i class="ri-search-line absolute left-4 top-3.5 text-slate-400 text-xl"></i>
            </div>

            <button type="submit" class="h-[48px] px-7 bg-teal-600 hover:bg-teal-700 text-white font-semibold text-base rounded-xl shadow-md shadow-teal-600/30 transition flex items-center gap-2 shrink-0">
                <i class="ri-search-line text-xl"></i> Cari Data
            </button>

            @if (request('search'))
                <a href="{{ route('alkes.index', request()->except('search')) }}" class="h-[48px] px-5 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-700 font-semibold text-base rounded-xl border border-slate-200 transition flex items-center justify-center shrink-0" title="Bersihkan Pencarian">
                    <i class="ri-close-circle-line text-xl"></i> Reset Cari
                </a>
            @endif
        </form>
    </div>

    <!-- CARD 2: PENYARINGAN RINGKAS -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <label class="block text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
            <i class="ri-filter-3-line text-teal-600 text-lg"></i>
            Filter Utama Inventaris
        </label>

        <form method="GET" action="{{ route('alkes.index') }}" id="filterForm" class="space-y-4">
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

            <!-- 3 Equal Width Spacious Dropdown Columns -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                
                <!-- Filter 1: Ruangan Pemilik / Penempatan -->
                <div class="w-full">
                    <label class="block text-sm font-semibold text-slate-800 mb-1.5" title="Ruangan pemilik aset inventaris">Ruangan Pemilik Aset</label>
                    <select id="selectRuangan" name="ruangan_id" class="w-full">
                        <option value="">-- Semua Ruangan Pemilik --</option>
                        @foreach ($ruanganList as $ruang)
                            <option value="{{ $ruang->id }}" {{ request('ruangan_id') == $ruang->id ? 'selected' : '' }}>
                                {{ $ruang->nama_ruangan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter 2: Lokasi Fisik Saat Ini -->
                <div class="w-full">
                    <label class="block text-sm font-semibold text-slate-800 mb-1.5" title="Lokasi fisik tempat alat berada saat ini">Lokasi Fisik Saat Ini</label>
                    <select id="selectLokasi" name="lokasi_ruangan_id" class="w-full">
                        <option value="">-- Semua Lokasi Fisik --</option>
                        @foreach ($ruanganList as $ruang)
                            <option value="{{ $ruang->id }}" {{ request('lokasi_ruangan_id') == $ruang->id ? 'selected' : '' }}>
                                {{ $ruang->nama_ruangan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter 3: Kondisi Alat -->
                <div class="w-full">
                    <label class="block text-sm font-semibold text-slate-800 mb-1.5">Kondisi Alat</label>
                    <select id="selectKondisi" name="kondisi" class="w-full">
                        <option value="">-- Semua Kondisi Alat --</option>
                        @foreach ($kondisis as $kd)
                            <option value="{{ $kd->value }}" {{ strtolower(request('kondisi')) == strtolower($kd->value) ? 'selected' : '' }}>{{ $kd->label() }}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            <!-- Action Buttons Bar -->
            <div class="flex items-center gap-3 justify-end pt-3 border-t border-slate-100">
                <a href="{{ route('alkes.index') }}" class="h-[44px] px-6 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-700 font-semibold text-sm rounded-xl border border-slate-300 transition flex items-center justify-center gap-2 shrink-0" title="Reset Semua Filter & Pencarian">
                    <i class="ri-refresh-line text-lg"></i> Reset Filter
                </a>

                <button type="submit" class="h-[44px] px-8 bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm rounded-xl shadow-xs transition flex items-center justify-center gap-2 shrink-0">
                    <i class="ri-filter-3-line text-lg"></i> Terapkan Filter
                </button>
            </div>

        </form>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xl shadow-slate-200/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-teal-950 via-teal-900 to-teal-950 text-white border-b border-teal-800 text-xs font-bold uppercase tracking-wider select-none">
                        <th class="px-3.5 py-3.5 text-center border-r border-teal-700/60 w-12">No</th>
                        
                        <!-- Clickable Column Headers with Sort Icons -->
                        <th class="px-4 py-3.5 border-r border-teal-700/60 min-w-[200px]">
                            <a href="{{ makeSortUrl('nama_barang', $sortBy, $sortDir) }}" class="flex items-center justify-between hover:text-teal-200 transition" title="Klik untuk mengurutkan A-Z / Z-A">
                                <span>Nama Barang</span>
                                <i class="ri-arrow-up-down-line text-sm opacity-75 {{ $sortBy == 'nama_barang' ? 'text-teal-300 font-black opacity-100' : '' }}"></i>
                            </a>
                        </th>

                        <th class="px-3.5 py-3.5 border-r border-teal-700/60 min-w-[120px]">Merk</th>
                        <th class="px-3.5 py-3.5 border-r border-teal-700/60 min-w-[120px]">Tipe</th>
                        <th class="px-3.5 py-3.5 border-r border-teal-700/60 min-w-[150px]">Serial Number</th>
                        <th class="px-3.5 py-3.5 text-center border-r border-teal-700/60 w-20">Tahun</th>
                        <th class="px-3.5 py-3.5 text-center border-r border-teal-700/60 w-20">Jumlah</th>

                        <!-- Clear Header 1: Ruangan Pemilik / Penempatan -->
                        <th class="px-3.5 py-3.5 border-r border-teal-700/60 min-w-[150px]" title="Ruangan penempatan / pemilik resmi aset inventaris">
                            Ruangan Pemilik
                        </th>

                        <!-- Clear Header 2: Lokasi Fisik Saat Ini -->
                        <th class="px-3.5 py-3.5 border-r border-teal-700/60 min-w-[160px]" title="Lokasi fisik tempat alat berada saat ini">
                            Lokasi Fisik Saat Ini
                        </th>

                        <th class="px-3.5 py-3.5 border-r border-teal-700/60 min-w-[120px]">
                            <a href="{{ makeSortUrl('kondisi', $sortBy, $sortDir) }}" class="flex items-center justify-between hover:text-teal-200 transition" title="Klik untuk mengurutkan Kondisi">
                                <span>Kondisi Alat</span>
                                <i class="ri-arrow-up-down-line text-sm opacity-75 {{ $sortBy == 'kondisi' ? 'text-teal-300 font-black opacity-100' : '' }}"></i>
                            </a>
                        </th>

                        <th class="px-3.5 py-3.5 text-center border-r border-teal-700/60 w-28">ASPAK</th>
                        <th class="px-3.5 py-3.5 text-center border-r border-teal-700/60 w-24">KIB</th>
                        <th class="px-4 py-3.5 border-r border-teal-700/60 min-w-[150px]">Keterangan</th>
                        <th class="px-3.5 py-3.5 text-center min-w-[130px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 font-normal text-slate-900 text-sm">
                    @forelse ($alkesList as $index => $alkes)
                        @php
                            $rowNumber = $alkesList->firstItem() + $index;
                        @endphp
                        <tr class="hover:bg-teal-50/60 transition odd:bg-white even:bg-slate-50/50 border-b border-slate-200">
                            <!-- No -->
                            <td class="px-3.5 py-3 text-center font-medium text-slate-500 border-r border-slate-200">
                                {{ $rowNumber }}
                            </td>

                            <!-- Nama Barang -->
                            <td class="px-4 py-3 font-semibold text-slate-900 border-r border-slate-200">
                                {{ $alkes->nama_barang }}
                            </td>

                            <!-- Merk -->
                            <td class="px-3.5 py-3 font-medium text-teal-800 border-r border-slate-200">
                                {{ $alkes->merk ?: '-' }}
                            </td>

                            <!-- Tipe -->
                            <td class="px-3.5 py-3 text-slate-800 border-r border-slate-200">
                                {{ $alkes->tipe ?: '-' }}
                            </td>

                            <!-- Serial Number -->
                            <td class="px-3.5 py-3 font-mono font-medium text-slate-900 border-r border-slate-200">
                                {{ $alkes->nomor_seri ?: '-' }}
                            </td>

                            <!-- Tahun -->
                            <td class="px-3.5 py-3 text-center font-medium text-blue-800 border-r border-slate-200">
                                {{ $alkes->tahun_pengadaan ?: '-' }}
                            </td>

                            <!-- Jumlah -->
                            <td class="px-3.5 py-3 text-center font-semibold text-slate-900 border-r border-slate-200">
                                {{ $alkes->jumlah }}
                            </td>

                            <!-- Ruangan Pemilik Aset -->
                            <td class="px-3.5 py-3 font-semibold text-slate-900 border-r border-slate-200" title="Ruangan penempatan / pemilik resmi aset inventaris">
                                {{ $alkes->ruangan->nama_ruangan ?? '-' }}
                            </td>

                            <!-- Lokasi Fisik Saat Ini -->
                            <td class="px-3.5 py-3 border-r border-slate-200" title="Lokasi fisik tempat alat berada saat ini">
                                @if ($alkes->lokasi_saat_ini_note)
                                    <span class="font-medium text-amber-900 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                        {{ $alkes->lokasi_saat_ini_note }}
                                    </span>
                                @elseif ($alkes->ruangan_id != $alkes->lokasi_ruangan_id)
                                    <span class="font-semibold text-teal-900 bg-teal-50 px-2 py-0.5 rounded border border-teal-200">
                                        {{ $alkes->lokasiRuangan->nama_ruangan ?? '-' }}
                                    </span>
                                @else
                                    <span class="text-slate-600">{{ $alkes->ruangan->nama_ruangan ?? '-' }}</span>
                                @endif
                            </td>

                            <!-- Kondisi Alat -->
                            <td class="px-3.5 py-3 border-r border-slate-200">
                                <span class="inline-block px-2.5 py-0.5 rounded text-xs font-semibold border {{ $alkes->kondisi_enum->warnaBadge() }}">
                                    {{ $alkes->kondisi_enum->label() }}
                                </span>
                            </td>

                            <!-- ASPAK -->
                            <td class="px-3.5 py-3 text-center border-r border-slate-200">
                                <span class="px-2.5 py-0.5 rounded text-xs font-semibold {{ $alkes->aspak_status == 'TERDATA' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                    {{ $alkes->aspak_status ?? 'TERDATA' }}
                                </span>
                            </td>

                            <!-- KIB -->
                            <td class="px-3.5 py-3 text-center border-r border-slate-200">
                                <span class="px-2.5 py-0.5 rounded text-xs font-semibold {{ $alkes->kib_status ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                    {{ $alkes->kib_status ? 'TRUE' : 'FALSE' }}
                                </span>
                            </td>

                            <!-- Keterangan -->
                            <td class="px-4 py-3 text-slate-700 border-r border-slate-200 max-w-xs truncate" title="{{ $alkes->keterangan }}">
                                {{ $alkes->keterangan ?: '-' }}
                            </td>

                            <!-- Aksi (Sesuai Peran User) -->
                            <td class="px-3.5 py-3 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Detail (Semua Ruangan Boleh Lihat) -->
                                    <a href="{{ route('alkes.show', $alkes->id) }}" class="p-1.5 text-teal-700 hover:bg-teal-100 rounded-lg transition" title="Lihat Detail">
                                        <i class="ri-eye-line text-lg"></i>
                                    </a>

                                    <!-- Pindah Ruangan Alat (Semua Ruangan Boleh Mutasi) -->
                                    <a href="{{ route('mutasi.create', ['alkes_id' => $alkes->id]) }}" class="p-1.5 text-blue-700 hover:bg-blue-100 rounded-lg transition" title="Pindah Ruangan Alat">
                                        <i class="ri-arrow-left-right-line text-lg"></i>
                                    </a>

                                    <!-- Lapor Perbaikan (Semua Ruangan Boleh Lapor Kerusakan) -->
                                    <a href="{{ route('pemeliharaan.create', ['alkes_id' => $alkes->id]) }}" class="p-1.5 text-amber-700 hover:bg-amber-100 rounded-lg transition" title="Lapor Perbaikan">
                                        <i class="ri-tools-line text-lg"></i>
                                    </a>

                                    <!-- Edit (KHUSUS ELEKTROMEDIS ADMIN) -->
                                    @if ($currentRole === 'elektromedis')
                                        <a href="{{ route('alkes.edit', $alkes->id) }}" class="p-1.5 text-slate-700 hover:bg-slate-200 rounded-lg transition" title="Edit Data (Elektromedis Only)">
                                            <i class="ri-edit-line text-lg"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="px-6 py-10 text-center text-slate-500 text-base">
                                <i class="ri-inbox-line text-5xl block mb-3 text-slate-300"></i>
                                Tidak ada data alat kesehatan ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- SIAKER Compact Pagination UI (3 Pertama ... 3 Terakhir) -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
            {{ $alkesList->links('pagination.custom') }}
        </div>
    </div>

</div>


@endsection
