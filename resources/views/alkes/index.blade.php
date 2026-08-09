@extends('layouts.app')

@php
    $currentRuanganId = request('ruangan_id', 0);
    $selectedRuanganObj = $ruanganList->firstWhere('id', $currentRuanganId);
    $pageTitle = $selectedRuanganObj ? 'Inventaris Alkes Ruang ' . $selectedRuanganObj->nama_ruangan : 'Daftar Seluruh Inventaris Alkes';

    $sortBy = request('sort_by', 'nama_barang');
    $sortDir = strtolower(request('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';
    $currentRole = session('user_role', 'elektromedis');

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

    @if (session('error'))
        <div id="flashErrMsg" class="p-4 bg-rose-50 border border-rose-300 rounded-xl text-rose-900 font-bold text-sm flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2.5">
                <i class="ri-error-warning-fill text-rose-600 text-xl"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="document.getElementById('flashErrMsg').remove()" class="text-rose-600 hover:text-rose-900 text-xl">
                <i class="ri-close-line"></i>
            </button>
        </div>
    @endif

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                <i class="ri-stethoscope-line text-emerald-600"></i>
                {{ $pageTitle }}
            </h3>
            <p class="text-sm text-slate-700 mt-1 font-medium">
                @if ($selectedRuanganObj)
                    Menampilkan seluruh unit alat kesehatan milik <strong class="text-slate-900">RUANG {{ strtoupper($selectedRuanganObj->nama_ruangan) }}</strong>
                @else
                    Kelola dan tinjau seluruh rekapan data alat kesehatan, kondisi fisik, dan lokasi penempatan unit
                @endif
            </p>
        </div>

        @if ($currentRole === 'elektromedis')
            <a href="{{ route('alkes.create') }}" class="px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl shadow-md shadow-emerald-600/30 transition flex items-center gap-2 shrink-0">
                <i class="ri-add-line text-lg"></i>
                Tambah Alkes Baru
            </a>
        @endif
    </div>

    <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-sm space-y-3">
        <label class="block text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
            <i class="ri-search-2-line text-emerald-600 text-base"></i>
            Pencarian Universal Data
        </label>
        <form method="GET" action="{{ route('alkes.index') }}" class="flex items-center gap-3">
            @foreach (request()->except(['search', 'page']) as $k => $v)
                @if ($v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endif
            @endforeach

            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik kata kunci: nama barang, merk, tipe, serial number, atau ruangan..." class="w-full pl-11 pr-4 h-11 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 shadow-xs transition">
                <i class="ri-search-line absolute left-3.5 top-3 text-slate-400 text-lg"></i>
            </div>

            <button type="submit" class="h-11 px-6 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl shadow-xs transition flex items-center gap-2 shrink-0">
                <i class="ri-search-line text-base"></i> Cari Data
            </button>

            @if (request('search'))
                <a href="{{ route('alkes.index', request()->except('search')) }}" class="h-11 px-4 bg-slate-100 hover:bg-rose-50 hover:text-rose-700 text-slate-800 font-bold text-sm rounded-xl border border-slate-300 transition flex items-center justify-center shrink-0" title="Bersihkan Pencarian">
                    <i class="ri-close-line text-lg"></i> Reset
                </a>
            @endif
        </form>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-sm space-y-4">
        <label class="block text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2 pb-3 border-b border-slate-200">
            <i class="ri-filter-3-line text-emerald-600 text-base"></i>
            Filter Utama Inventaris
        </label>

        <form method="GET" action="{{ route('alkes.index') }}" id="filterForm" class="space-y-4">
            @if (request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            @if (request('sort_by'))
                <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
            @endif
            @if (request('sort_dir'))
                <input type="hidden" name="sort_dir" value="{{ request('sort_dir') }}">
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1.5">Ruangan Pemilik Aset</label>
                    <select id="selectRuangan" name="ruangan_id" class="w-full">
                        <option value="">-- Semua Ruangan Pemilik --</option>
                        @foreach ($ruanganList as $ruang)
                            <option value="{{ $ruang->id }}" {{ request('ruangan_id') == $ruang->id ? 'selected' : '' }}>
                                {{ $ruang->nama_ruangan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1.5">Lokasi Fisik Saat Ini</label>
                    <select id="selectLokasi" name="lokasi_ruangan_id" class="w-full">
                        <option value="">-- Semua Lokasi Fisik --</option>
                        @foreach ($ruanganList as $ruang)
                            <option value="{{ $ruang->id }}" {{ request('lokasi_ruangan_id') == $ruang->id ? 'selected' : '' }}>
                                {{ $ruang->nama_ruangan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1.5">Kondisi Alat</label>
                    <select id="selectKondisi" name="kondisi" class="w-full">
                        <option value="">-- Semua Kondisi Alat --</option>
                        @foreach ($kondisis as $kd)
                            <option value="{{ $kd->value }}" {{ strtolower(request('kondisi')) == strtolower($kd->value) ? 'selected' : '' }}>{{ $kd->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-3 justify-end pt-3 border-t border-slate-200">
                <a href="{{ route('alkes.index') }}" class="h-10 px-5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs rounded-xl border border-slate-300 transition flex items-center justify-center gap-1.5 shrink-0">
                    <i class="ri-refresh-line text-sm"></i> Reset Filter
                </a>
                <button type="submit" class="h-10 px-6 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center gap-1.5 shrink-0">
                    <i class="ri-filter-3-line text-sm"></i> Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-300 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-emerald-950 text-white border-b border-emerald-900 text-xs font-black uppercase tracking-wider select-none">
                        <th class="px-3.5 py-3.5 text-center border-r border-emerald-900 w-12">No</th>
                        <th class="px-4 py-3.5 border-r border-emerald-900 min-w-[200px]">
                            <a href="{{ makeSortUrl('nama_barang', $sortBy, $sortDir) }}" class="flex items-center justify-between hover:text-amber-300 transition" title="Urutkan A-Z / Z-A">
                                <span>Nama Barang</span>
                                <i class="ri-arrow-up-down-line text-sm {{ $sortBy == 'nama_barang' ? 'text-amber-300 opacity-100' : 'opacity-50' }}"></i>
                            </a>
                        </th>
                        <th class="px-3.5 py-3.5 border-r border-emerald-900 min-w-[120px]">Merk</th>
                        <th class="px-3.5 py-3.5 border-r border-emerald-900 min-w-[120px]">Tipe</th>
                        <th class="px-3.5 py-3.5 border-r border-emerald-900 min-w-[150px]">Serial Number</th>
                        <th class="px-3.5 py-3.5 text-center border-r border-emerald-900 w-20">Tahun</th>
                        <th class="px-3.5 py-3.5 text-center border-r border-emerald-900 w-20">Jumlah</th>
                        <th class="px-3.5 py-3.5 border-r border-emerald-900 min-w-[150px]">Ruangan Pemilik</th>
                        <th class="px-3.5 py-3.5 border-r border-emerald-900 min-w-[160px]">Lokasi Fisik</th>
                        <th class="px-3.5 py-3.5 border-r border-emerald-900 min-w-[130px]">
                            <a href="{{ makeSortUrl('kondisi', $sortBy, $sortDir) }}" class="flex items-center justify-between hover:text-amber-300 transition" title="Urutkan Kondisi">
                                <span>Kondisi</span>
                                <i class="ri-arrow-up-down-line text-sm {{ $sortBy == 'kondisi' ? 'text-amber-300 opacity-100' : 'opacity-50' }}"></i>
                            </a>
                        </th>
                        <th class="px-3.5 py-3.5 text-center border-r border-emerald-900 w-24">ASPAK</th>
                        <th class="px-3.5 py-3.5 text-center border-r border-emerald-900 w-20">KIB</th>
                        <th class="px-4 py-3.5 border-r border-emerald-900 min-w-[150px]">Keterangan</th>
                        <th class="px-3.5 py-3.5 text-center min-w-[130px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 font-medium text-slate-900 text-sm">
                    @forelse ($alkesList as $index => $alkes)
                        @php $rowNumber = $alkesList->firstItem() + $index; @endphp
                        <tr class="hover:bg-emerald-50/50 transition odd:bg-white even:bg-slate-50/70 border-b border-slate-200">
                            <td class="px-3.5 py-3 text-center font-bold text-slate-700 border-r border-slate-200">{{ $rowNumber }}</td>
                            <td class="px-4 py-3 font-extrabold text-slate-900 border-r border-slate-200">{{ $alkes->nama_barang }}</td>
                            <td class="px-3.5 py-3 font-semibold text-slate-800 border-r border-slate-200">{{ $alkes->merk ?: '-' }}</td>
                            <td class="px-3.5 py-3 text-slate-800 border-r border-slate-200">{{ $alkes->tipe ?: '-' }}</td>
                            <td class="px-3.5 py-3 font-mono font-bold text-slate-900 border-r border-slate-200">{{ $alkes->nomor_seri ?: '-' }}</td>
                            <td class="px-3.5 py-3 text-center font-bold text-slate-800 border-r border-slate-200">{{ $alkes->tahun_pengadaan ?: '-' }}</td>
                            <td class="px-3.5 py-3 text-center font-black text-slate-900 border-r border-slate-200">{{ $alkes->jumlah }}</td>
                            <td class="px-3.5 py-3 font-bold text-slate-900 border-r border-slate-200">{{ $alkes->ruangan->nama_ruangan ?? '-' }}</td>
                            <td class="px-3.5 py-3 border-r border-slate-200">
                                @if ($alkes->lokasi_saat_ini_note)
                                    <span class="font-bold text-amber-900 bg-amber-100 px-2 py-0.5 rounded border border-amber-300">{{ $alkes->lokasi_saat_ini_note }}</span>
                                @elseif ($alkes->ruangan_id != $alkes->lokasi_ruangan_id)
                                    <span class="font-bold text-emerald-900 bg-emerald-100 px-2 py-0.5 rounded border border-emerald-300">{{ $alkes->lokasiRuangan->nama_ruangan ?? '-' }}</span>
                                @else
                                    <span class="text-slate-800 font-semibold">{{ $alkes->ruangan->nama_ruangan ?? '-' }}</span>
                                @endif
                            </td>
                            <td class="px-3.5 py-3 border-r border-slate-200">
                                <span class="inline-block px-2.5 py-0.5 rounded text-xs font-black border {{ $alkes->kondisi_enum->warnaBadge() }}">{{ $alkes->kondisi_enum->label() }}</span>
                            </td>
                            <td class="px-3.5 py-3 text-center border-r border-slate-200">
                                <span class="px-2.5 py-0.5 rounded text-xs font-bold {{ $alkes->aspak_status == 'TERDATA' ? 'bg-emerald-100 text-emerald-900 border border-emerald-300' : 'bg-slate-100 text-slate-700 border border-slate-300' }}">{{ $alkes->aspak_status ?? 'TERDATA' }}</span>
                            </td>
                            <td class="px-3.5 py-3 text-center border-r border-slate-200">
                                <span class="px-2.5 py-0.5 rounded text-xs font-bold {{ $alkes->kib_status ? 'bg-blue-100 text-blue-900 border border-blue-300' : 'bg-slate-100 text-slate-700 border border-slate-300' }}">{{ $alkes->kib_status ? 'TRUE' : 'FALSE' }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-800 border-r border-slate-200 max-w-xs truncate font-medium" title="{{ $alkes->keterangan }}">{{ $alkes->keterangan ?: '-' }}</td>
                            <td class="px-3.5 py-3 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('alkes.show', $alkes->id) }}" class="p-1.5 text-emerald-700 hover:bg-emerald-100 rounded-lg transition" title="Lihat Detail">
                                        <i class="ri-eye-line text-lg"></i>
                                    </a>
                                    <a href="{{ route('mutasi.create', ['alkes_id' => $alkes->id]) }}" class="p-1.5 text-blue-700 hover:bg-blue-100 rounded-lg transition" title="Pindah Ruangan Alat">
                                        <i class="ri-arrow-left-right-line text-lg"></i>
                                    </a>
                                    <a href="{{ route('pemeliharaan.create', ['alkes_id' => $alkes->id]) }}" class="p-1.5 text-amber-700 hover:bg-amber-100 rounded-lg transition" title="Lapor Perbaikan">
                                        <i class="ri-tools-line text-lg"></i>
                                    </a>
                                    @if ($currentRole === 'elektromedis')
                                        <a href="{{ route('alkes.edit', $alkes->id) }}" class="p-1.5 text-slate-800 hover:bg-slate-200 rounded-lg transition" title="Edit Data">
                                            <i class="ri-edit-line text-lg"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="px-6 py-12 text-center text-slate-700 font-bold">
                                <i class="ri-inbox-line text-5xl block mb-3 text-slate-400"></i>
                                Tidak ada data alat kesehatan ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 bg-slate-100/70 border-t border-slate-200">
            {{ $alkesList->links('pagination.custom') }}
        </div>
    </div>

</div>

@endsection
