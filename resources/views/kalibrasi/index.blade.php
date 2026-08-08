@extends('layouts.app')

@section('title', 'Kalibrasi Alat Kesehatan')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-teal-900 via-teal-800 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-teal-300 font-semibold text-xs tracking-wider uppercase mb-1">
                <i class="ri-verified-badge-line text-lg"></i>
                <span>Manajemen Mutu & Kelayakan Alat Kesehatan</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Kalibrasi & Pengujian Berkala Alkes</h1>
            <p class="text-slate-300 text-sm mt-1 max-w-2xl">
                Setiap alat kesehatan (termasuk yang berstatus **BAIK**) wajib dikalibrasi secara berkala sesuai standar Kemenkes RI untuk menjamin akurasi dan keselamatan operasional pasien.
            </p>
        </div>

        @if (session('user_role') === 'elektromedis')
            <div class="shrink-0">
                <span class="px-4 py-2 bg-amber-400/20 text-amber-200 border border-amber-400/30 rounded-2xl text-xs font-bold flex items-center gap-2 backdrop-blur-xs">
                    <i class="ri-shield-check-line text-lg text-amber-300"></i>
                    <span>Otoritas Pengujian Elektromedis</span>
                </span>
            </div>
        @endif
    </div>

    <!-- Summary Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Alkes -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-700 flex items-center justify-center text-2xl font-bold shrink-0">
                <i class="ri-stethoscope-line"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Alkes Terdaftar</p>
                <h3 class="text-2xl font-extrabold text-slate-800 mt-0.5">{{ number_format($totalAlkes) }} <span class="text-xs font-normal text-slate-500">Unit</span></h3>
            </div>
        </div>

        <!-- Terkalibrasi Valid -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-2xl font-bold shrink-0">
                <i class="ri-checkbox-circle-line"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Terkalibrasi & Valid</p>
                <h3 class="text-2xl font-extrabold text-emerald-700 mt-0.5">{{ number_format($totalTerkalibrasi) }} <span class="text-xs font-normal text-slate-500">Unit</span></h3>
            </div>
        </div>

        <!-- Expired / Perlu Kalibrasi Ulang -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-700 flex items-center justify-center text-2xl font-bold shrink-0">
                <i class="ri-alarm-warning-line"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kadaluarsa / Perlu Kalibrasi</p>
                <h3 class="text-2xl font-extrabold text-rose-700 mt-0.5">{{ number_format($totalExpired) }} <span class="text-xs font-normal text-slate-500">Unit</span></h3>
            </div>
        </div>

        <!-- Belum Pernah Dikalibrasi -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center text-2xl font-bold shrink-0">
                <i class="ri-time-line"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Belum Dikalibrasi</p>
                <h3 class="text-2xl font-extrabold text-amber-700 mt-0.5">{{ number_format($totalBelum) }} <span class="text-xs font-normal text-slate-500">Unit</span></h3>
            </div>
        </div>

    </div>

    <!-- Filter & Search Bar Card -->
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <form method="GET" action="{{ route('kalibrasi.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 items-end">
            
            <!-- Search Bar -->
            <div class="lg:col-span-5">
                <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase">Cari Alat / Serial Number</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama alkes, merk, tipe, atau nomor seri..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 transition">
                    <i class="ri-search-line absolute left-3.5 top-3 text-slate-400"></i>
                </div>
            </div>

            <!-- Filter Ruangan Pemilik -->
            <div class="lg:col-span-3">
                <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase">Filter Ruangan Pemilik</label>
                <select name="ruangan_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 transition">
                    <option value="">-- Semua Ruangan --</option>
                    @foreach ($ruanganList as $r)
                        <option value="{{ $r->id }}" {{ request('ruangan_id') == $r->id ? 'selected' : '' }}>
                            {{ $r->nama_ruangan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status Kalibrasi -->
            <div class="lg:col-span-3">
                <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase">Status Masa Berlaku</label>
                <select name="status_kalibrasi" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 transition">
                    <option value="">-- Semua Status --</option>
                    <option value="TERKALIBRASI" {{ request('status_kalibrasi') == 'TERKALIBRASI' ? 'selected' : '' }}>Terkalibrasi (Aktif)</option>
                    <option value="EXPIRED" {{ request('status_kalibrasi') == 'EXPIRED' ? 'selected' : '' }}>Kadaluarsa / Overdue</option>
                    <option value="BELUM" {{ request('status_kalibrasi') == 'BELUM' ? 'selected' : '' }}>Belum Pernah Dikalibrasi</option>
                </select>
            </div>

            <!-- Submit Filter Button -->
            <div class="lg:col-span-1 flex gap-2">
                <button type="submit" class="w-full py-2.5 px-4 bg-teal-600 hover:bg-teal-700 text-white rounded-xl font-bold text-sm transition shadow-sm flex items-center justify-center gap-1">
                    <i class="ri-filter-3-line"></i>
                </button>
            </div>

        </form>
    </div>

    <!-- Data Table Card (Se-color Palette: Header Teal Dark) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                <i class="ri-list-check-2 text-teal-600"></i>
                Daftar Status Kalibrasi Seluruh Alat Kesehatan
            </h3>
            <span class="text-xs font-medium text-slate-500">Menampilkan {{ $alkesList->firstItem() ?? 0 }} - {{ $alkesList->lastItem() ?? 0 }} dari {{ $alkesList->total() }} data</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <!-- Color Palette Header (Teal Dark Theme) -->
                <thead>
                    <tr class="bg-gradient-to-r from-teal-950 via-teal-900 to-teal-950 text-white text-xs uppercase tracking-wider font-bold">
                        <th class="py-3.5 px-4 text-center w-12">No</th>
                        <th class="py-3.5 px-4">Kode & Nama Alkes</th>
                        <th class="py-3.5 px-4">Merk / Tipe / Serial Number</th>
                        <th class="py-3.5 px-4">Ruangan Pemilik</th>
                        <th class="py-3.5 px-4">Kondisi Fisik</th>
                        <th class="py-3.5 px-4">Kalibrasi Terakhir</th>
                        <th class="py-3.5 px-4">Jadwal Kalibrasi Ulang</th>
                        <th class="py-3.5 px-4 text-center">Status Kalibrasi</th>
                        <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($alkesList as $index => $item)
                        @php
                            $today = \Carbon\Carbon::today();
                            $tglTerakhir = $item->tanggal_kalibrasi_terakhir;
                            $tglBerikutnya = $item->tanggal_kalibrasi_berikutnya;

                            $isTerkalibrasi = $tglTerakhir && $tglBerikutnya && $tglBerikutnya->isAfter($today);
                            $isExpired = $tglBerikutnya && $tglBerikutnya->isBefore($today);
                            $isBelum = !$tglTerakhir;
                        @endphp

                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 text-center text-slate-400 font-bold text-xs">
                                {{ $alkesList->firstItem() + $index }}
                            </td>

                            <td class="py-3.5 px-4">
                                <span class="text-[11px] font-mono font-bold text-slate-400 block">{{ $item->kode_inventaris ?: 'N/A' }}</span>
                                <span class="font-bold text-slate-800 text-sm block hover:text-teal-600 transition">{{ $item->nama_barang }}</span>
                            </td>

                            <td class="py-3.5 px-4 text-slate-600">
                                <span class="font-semibold block">{{ $item->merk ?: '-' }} {{ $item->tipe ? '('.$item->tipe.')' : '' }}</span>
                                <span class="text-xs text-slate-400 font-mono">SN: {{ $item->nomor_seri ?: '-' }}</span>
                            </td>

                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-semibold inline-block border border-slate-200">
                                    <i class="ri-building-4-line text-teal-600 mr-1"></i>
                                    {{ $item->ruangan->nama_ruangan ?? 'RS' }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4">
                                @if ($item->kondisiEnum->value === 'Baik')
                                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-lg text-xs font-bold inline-flex items-center gap-1">
                                        <i class="ri-checkbox-circle-fill text-emerald-500"></i> Baik (Operasional)
                                    </span>
                                @elseif ($item->kondisiEnum->value === 'Rusak Ringan')
                                    <span class="px-2.5 py-1 bg-amber-50 text-amber-800 border border-amber-200 rounded-lg text-xs font-bold inline-flex items-center gap-1">
                                        <i class="ri-error-warning-fill text-amber-500"></i> Rusak Ringan
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 bg-rose-50 text-rose-800 border border-rose-200 rounded-lg text-xs font-bold inline-flex items-center gap-1">
                                        <i class="ri-close-circle-fill text-rose-500"></i> Rusak Berat
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 font-medium text-slate-700">
                                @if ($tglTerakhir)
                                    <span class="flex items-center gap-1.5 text-xs font-bold text-slate-800">
                                        <i class="ri-calendar-check-line text-teal-600"></i>
                                        {{ $tglTerakhir->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400 italic">Belum Ada Data</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 font-medium">
                                @if ($tglBerikutnya)
                                    <span class="flex items-center gap-1.5 text-xs font-bold {{ $isExpired ? 'text-rose-600' : 'text-slate-800' }}">
                                        <i class="ri-calendar-event-line text-amber-500"></i>
                                        {{ $tglBerikutnya->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400 italic">Belum Dijadwalkan</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                @if ($isTerkalibrasi)
                                    <span class="px-3 py-1 bg-emerald-100 text-emerald-800 border border-emerald-300 rounded-xl text-xs font-bold inline-flex items-center gap-1">
                                        <i class="ri-verified-badge-fill text-emerald-600"></i> Terkalibrasi
                                    </span>
                                @elseif ($isExpired)
                                    <span class="px-3 py-1 bg-rose-100 text-rose-800 border border-rose-300 rounded-xl text-xs font-bold inline-flex items-center gap-1 animate-pulse">
                                        <i class="ri-alarm-warning-fill text-rose-600"></i> Expired / Perlu Kalibrasi
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-slate-100 text-slate-600 border border-slate-300 rounded-xl text-xs font-bold inline-flex items-center gap-1">
                                        <i class="ri-time-line text-slate-400"></i> Belum Dikalibrasi
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                <button type="button" onclick="openUpdateModal({{ $item->id }}, '{{ addslashes($item->nama_barang) }}', '{{ $tglTerakhir ? $tglTerakhir->format('Y-m-d') : '' }}', '{{ $tglBerikutnya ? $tglBerikutnya->format('Y-m-d') : '' }}')" class="px-3 py-1.5 bg-teal-50 text-teal-700 hover:bg-teal-600 hover:text-white border border-teal-200 rounded-xl font-bold text-xs transition flex items-center justify-center gap-1 mx-auto shadow-xs">
                                    <i class="ri-edit-box-line"></i> Update
                                </button>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400">
                                <i class="ri-file-search-line text-4xl block mb-2"></i>
                                Tidak ada data alat kesehatan yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($alkesList->hasPages())
            <div class="p-4 bg-slate-50 border-t border-slate-100">
                {{ $alkesList->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal Update Kalibrasi Alkes -->
<div id="updateKalibrasiModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
        <div class="p-5 bg-gradient-to-r from-teal-950 to-teal-900 text-white flex items-center justify-between">
            <h4 class="font-bold text-base flex items-center gap-2">
                <i class="ri-verified-badge-line text-teal-400"></i>
                Update Sertifikat & Jadwal Kalibrasi
            </h4>
            <button type="button" onclick="closeUpdateModal()" class="text-slate-400 hover:text-white p-1 rounded-lg">
                <i class="ri-close-line text-xl"></i>
            </button>
        </div>

        <form id="updateKalibrasiForm" method="POST" action="" class="p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama Alat Kesehatan</label>
                <input type="text" id="modalNamaAlkes" class="w-full px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm font-bold text-slate-700" readonly>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Tanggal Kalibrasi Terakhir <span class="text-rose-500">*</span></label>
                    <input type="date" name="tanggal_kalibrasi_terakhir" id="modalTglTerakhir" onchange="autoCalculateNextDate(this.value)" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-teal-500" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Jadwal Ulang Berikutnya <span class="text-rose-500">*</span></label>
                    <input type="date" name="tanggal_kalibrasi_berikutnya" id="modalTglBerikutnya" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-teal-500" required>
                    <span class="text-[10px] text-slate-400 mt-1 block">*Otomatis +1 Tahun (365 hari)</span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Nomor Sertifikat / Catatan Pengujian</label>
                <textarea name="keterangan" rows="3" placeholder="Masukkan nomor sertifikat kalibrasi atau catatan pengujian dari Balai Kalibrasi..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeUpdateModal()" class="px-4 py-2.5 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-xl text-xs font-bold transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold shadow-md shadow-teal-600/30 transition flex items-center gap-1.5">
                    <i class="ri-save-line"></i> Simpan Hasil Kalibrasi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openUpdateModal(id, namaAlkes, tglTerakhir, tglBerikutnya) {
        document.getElementById('updateKalibrasiForm').action = '/kalibrasi/' + id;
        document.getElementById('modalNamaAlkes').value = namaAlkes;
        document.getElementById('modalTglTerakhir').value = tglTerakhir || '';
        document.getElementById('modalTglBerikutnya').value = tglBerikutnya || '';
        document.getElementById('updateKalibrasiModal').classList.remove('hidden');
    }

    function closeUpdateModal() {
        document.getElementById('updateKalibrasiModal').classList.add('hidden');
    }

    function autoCalculateNextDate(lastDateStr) {
        if (!lastDateStr) return;
        const lastDate = new Date(lastDateStr);
        lastDate.setFullYear(lastDate.getFullYear() + 1);
        const yyyy = lastDate.getFullYear();
        const mm = String(lastDate.getMonth() + 1).padStart(2, '0');
        const dd = String(lastDate.getDate()).padStart(2, '0');
        document.getElementById('modalTglBerikutnya').value = `${yyyy}-${mm}-${dd}`;
    }
</script>
@endsection
