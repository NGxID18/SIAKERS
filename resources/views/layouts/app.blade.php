<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 preload">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>@yield('title', 'SIAKER') - Inventaris & ERP Alkes Rumah Sakit</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    <!-- Tom Select UI CDN (Custom Searchable Dropdown UI for Seniors) -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <!-- Chart.js CDN for Analytics Charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        
        .preload *, .preload .submenu-accordion, .preload #seksiChevron {
            transition: none !important;
            animation: none !important;
        }

        .submenu-accordion {
            transition: max-height 0.35s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.25s ease-in-out;
            overflow: hidden;
            max-height: 400px;
            opacity: 1;
        }
        .submenu-accordion.collapsed {
            max-height: 0px !important;
            opacity: 0 !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }

        .fade-in {
            animation: fadeIn 0.2s ease-in-out forwards;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .custom-card {
            transition: all 0.2s ease-in-out;
        }
        .custom-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08);
        }

        /* Seamless Single-Box Integrated Tom Select Styling (Fixed Height & Single Line) */
        .ts-control {
            background-color: #f8fafc !important;
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 0.75rem !important;
            padding: 0 0.85rem !important;
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            color: #0f172a !important;
            height: 42px !important;
            min-height: 42px !important;
            max-height: 42px !important;
            display: flex !important;
            align-items: center !important;
            overflow: hidden !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
        }
        .ts-control > .item {
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            max-width: calc(100% - 20px) !important;
            display: inline-block !important;
        }
        .ts-control > input {
            height: 24px !important;
            min-height: 24px !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .ts-wrapper.dropdown-active .ts-control {
            border-color: #0d9488 !important;
            border-bottom-left-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
            box-shadow: 0 4px 12px rgba(13, 148, 136, 0.15) !important;
            background-color: #ffffff !important;
        }
        .ts-dropdown {
            border: 1.5px solid #0d9488 !important;
            border-top: none !important;
            border-bottom-left-radius: 0.75rem !important;
            border-bottom-right-radius: 0.75rem !important;
            box-shadow: 0 12px 20px -3px rgba(15, 23, 42, 0.15) !important;
            padding: 0.35rem !important;
            margin-top: 0 !important;
            font-size: 0.875rem !important;
            z-index: 9999 !important;
            background-color: #ffffff !important;
        }
        .ts-dropdown .option {
            padding: 0.7rem 1rem !important;
            border-radius: 0.5rem !important;
            color: #1e293b !important;
            font-weight: 500 !important;
            margin-bottom: 2px !important;
            transition: all 0.15s ease-in-out !important;
        }
        .ts-dropdown .option.active, .ts-dropdown .option:hover {
            background-color: #ccfbf1 !important;
            color: #0f766e !important;
            font-weight: 700 !important;
        }
        .ts-dropdown .option.selected {
            background-color: #0d9488 !important;
            color: #ffffff !important;
            font-weight: 700 !important;
        }
    </style>
</head>
<body class="h-full text-slate-800 antialiased bg-slate-50 min-h-screen">

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div id="mobileOverlay" onclick="closeMobileSidebar()" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-40 hidden md:hidden transition-opacity duration-300"></div>

    @php
        $userSeksiId = session('user_seksi_id', 1);
        $currentSeksiParam = request('seksi_id');
        $activeSeksiId = $currentSeksiParam ? (int)$currentSeksiParam : $userSeksiId;
        $isCreatingMutation = request()->routeIs('mutasi.create');
        $isCreatingRepair = request()->routeIs('pemeliharaan.create');
    @endphp

    <!-- Responsive Sidebar Drawer -->
    <aside id="sidebarDrawer" class="fixed top-0 left-0 bottom-0 w-72 bg-slate-900 text-white flex flex-col justify-between shadow-2xl z-50 overflow-y-auto transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
        <div>
            <div class="px-6 py-5 border-b border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-teal-500 flex items-center justify-center text-white shadow-lg shadow-teal-500/30">
                        <i class="ri-hospital-line text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="font-extrabold text-lg tracking-wide text-teal-400">SIAKER ERP</h1>
                        <p class="text-xs text-slate-400">Inventaris Alkes RS</p>
                    </div>
                </div>
                <button type="button" onclick="closeMobileSidebar()" class="md:hidden p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800">
                    <i class="ri-close-line text-xl"></i>
                </button>
            </div>

            <nav class="p-4 space-y-1.5">
                <div class="px-3 py-2 text-xs font-bold text-slate-400 uppercase tracking-wider">Menu Utama</div>

                <a href="{{ route('dashboard') }}" onclick="closeMobileSidebar()" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/30' : 'text-slate-300 hover:bg-slate-800' }}">
                    <i class="ri-dashboard-3-line text-xl"></i>
                    <span>Dashboard ERP</span>
                </a>

                <a href="{{ route('mutasi.index') }}" onclick="closeMobileSidebar()" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('mutasi.index') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/30' : 'text-slate-300 hover:bg-slate-800' }}">
                    <i class="ri-arrow-left-right-line text-xl"></i>
                    <span>Pindah Lokasi & Transfer</span>
                </a>

                <a href="{{ route('pemeliharaan.index') }}" onclick="closeMobileSidebar()" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('pemeliharaan.index') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/30' : 'text-slate-300 hover:bg-slate-800' }}">
                    <i class="ri-tools-line text-xl"></i>
                    <span>Perbaikan & Kalibrasi</span>
                </a>

                <!-- MASTER DATA -->
                <div class="px-3 pt-6 pb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">Master Data</div>

                <!-- Parent Menu: Inventaris Alkes -->
                <div class="space-y-1">
                    <div class="flex items-center justify-between px-3 py-2.5 rounded-xl transition-all duration-200 {{ (request()->routeIs('alkes.index') && ($currentSeksiParam == 0 || $currentSeksiParam == null)) ? 'bg-teal-600 text-white font-bold shadow-md shadow-teal-600/30' : 'text-slate-200 hover:bg-slate-800' }}">
                        <a href="/alkes?seksi_id=0" onclick="closeMobileSidebar()" class="flex items-center gap-3 flex-1 font-semibold text-sm">
                            <i class="ri-stethoscope-line text-xl"></i>
                            <span>Inventaris Alkes</span>
                        </a>
                        
                        <button type="button" onclick="toggleSeksiSubmenu(event)" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-slate-700 text-teal-400 flex items-center justify-center transition-all duration-200 border border-slate-700 shadow-sm shrink-0" title="Buka / Tutup Daftar Seksi">
                            <i id="seksiChevron" class="ri-arrow-down-s-line text-xl font-extrabold text-teal-400 transition-transform duration-300"></i>
                        </button>
                    </div>

                    <!-- Collapsible Accordion Sub-menu Tree -->
                    <div id="seksiSubmenu" class="submenu-accordion pl-3 pr-1 py-1 border-l-2 border-slate-800 ml-5 space-y-1 mt-1">
                        @php
                            $isSeksi1Active = ($activeSeksiId == 1 && ($isCreatingMutation || $isCreatingRepair || request('seksi_id') == 1));
                            $isSeksi2Active = ($activeSeksiId == 2 && ($isCreatingMutation || $isCreatingRepair || request('seksi_id') == 2));
                            $isSeksi3Active = ($activeSeksiId == 3 && ($isCreatingMutation || $isCreatingRepair || request('seksi_id') == 3));
                            $isSeksi4Active = ($activeSeksiId == 4 && ($isCreatingMutation || $isCreatingRepair || request('seksi_id') == 4));
                            $isSeksi5Active = ($activeSeksiId == 5 && ($isCreatingMutation || $isCreatingRepair || request('seksi_id') == 5));
                            $isSeksi6Active = ($activeSeksiId == 6 && ($isCreatingMutation || $isCreatingRepair || request('seksi_id') == 6));
                        @endphp

                        <a href="/alkes?seksi_id=1" onclick="closeMobileSidebar()" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs sm:text-sm transition-all duration-200 {{ $isSeksi1Active ? 'bg-teal-600 text-white font-bold shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <i class="ri-checkbox-blank-circle-line text-[10px] opacity-70"></i>
                            <span class="truncate">Seksi Penunjang Medis</span>
                        </a>

                        <a href="/alkes?seksi_id=2" onclick="closeMobileSidebar()" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs sm:text-sm transition-all duration-200 {{ $isSeksi2Active ? 'bg-teal-600 text-white font-bold shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <i class="ri-checkbox-blank-circle-line text-[10px] opacity-70"></i>
                            <span class="truncate">Seksi Pelayanan Medis</span>
                        </a>

                        <a href="/alkes?seksi_id=3" onclick="closeMobileSidebar()" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs sm:text-sm transition-all duration-200 {{ $isSeksi3Active ? 'bg-teal-600 text-white font-bold shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <i class="ri-checkbox-blank-circle-line text-[10px] opacity-70"></i>
                            <span class="truncate">Seksi Keperawatan & Rawat Inap</span>
                        </a>

                        <a href="/alkes?seksi_id=4" onclick="closeMobileSidebar()" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs sm:text-sm transition-all duration-200 {{ $isSeksi4Active ? 'bg-teal-600 text-white font-bold shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <i class="ri-checkbox-blank-circle-line text-[10px] opacity-70"></i>
                            <span class="truncate">Seksi Intensive Care (ICU)</span>
                        </a>

                        <a href="/alkes?seksi_id=5" onclick="closeMobileSidebar()" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs sm:text-sm transition-all duration-200 {{ $isSeksi5Active ? 'bg-teal-600 text-white font-bold shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <i class="ri-checkbox-blank-circle-line text-[10px] opacity-70"></i>
                            <span class="truncate">Seksi Rehabilitasi Medis</span>
                        </a>

                        <a href="/alkes?seksi_id=6" onclick="closeMobileSidebar()" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs sm:text-sm transition-all duration-200 {{ $isSeksi6Active ? 'bg-teal-600 text-white font-bold shadow-sm' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                            <i class="ri-checkbox-blank-circle-line text-[10px] opacity-70"></i>
                            <span class="truncate">Gudang Alkes & ATEM</span>
                        </a>
                    </div>
                </div>

                <!-- Menu Master Lainnya -->
                <div class="pt-2 space-y-1">
                    <a href="{{ route('seksi.index') }}" onclick="closeMobileSidebar()" class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('seksi.*') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/30' : 'text-slate-300 hover:bg-slate-800' }}">
                        <i class="ri-building-4-line text-lg"></i>
                        <span>Seksi & Ruangan RS</span>
                    </a>

                    <a href="{{ route('activity-logs.index') }}" onclick="closeMobileSidebar()" class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('activity-logs.*') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/30' : 'text-slate-300 hover:bg-slate-800' }}">
                        <i class="ri-history-line text-lg text-teal-400"></i>
                        <span>Log Aktivitas System</span>
                    </a>
                </div>
            </nav>
        </div>

        <div class="p-4 border-t border-slate-800/60 text-center">
            <p class="text-[11px] text-slate-500">&copy; 2026 SIAKER ERP RS</p>
        </div>
    </aside>

    <div class="md:ml-72 min-h-screen flex flex-col flex-1 bg-slate-50">
        <!-- Top Right Header -->
        <header class="bg-white border-b border-slate-200 px-4 sm:px-6 py-3.5 flex items-center justify-between shadow-sm sticky top-0 z-20">
            <div class="flex items-center gap-3">
                <button type="button" onclick="openMobileSidebar()" class="md:hidden p-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 transition focus:outline-none" title="Buka Menu Navigasi">
                    <i class="ri-menu-line text-2xl"></i>
                </button>
                <h2 class="text-lg sm:text-xl font-extrabold text-slate-800 truncate">@yield('title', 'Dashboard ERP')</h2>
            </div>

            <div class="flex items-center gap-3 sm:gap-4 shrink-0">
                <div class="hidden sm:block text-right">
                    @if (session('is_admin'))
                        <p class="font-bold text-xs text-amber-600 flex items-center gap-1 justify-end"><i class="ri-shield-user-line"></i> Admin System</p>
                        <p class="text-[11px] text-slate-500 font-semibold">Akses Penuh</p>
                    @elseif (session('user_role') === 'tata_usaha')
                        <p class="font-bold text-xs text-slate-800">Tata Usaha RS</p>
                        <p class="text-[11px] text-slate-500 font-semibold">Pengawas (Read-Only)</p>
                    @else
                        @php
                            $userSeksiObj = \App\Models\Seksi::find(session('user_seksi_id', 1));
                        @endphp
                        <p class="font-bold text-xs text-slate-800">{{ $userSeksiObj->nama_seksi ?? 'Seksi Penunjang Medis' }}</p>
                        <p class="text-[11px] text-teal-600 font-semibold">Pengguna Aktif</p>
                    @endif
                </div>

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="px-3 sm:px-3.5 py-2 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 transition flex items-center gap-1.5 shadow-sm">
                        <i class="ri-logout-circle-r-line"></i> <span class="hidden sm:inline">Logout</span>
                    </button>
                </form>
            </div>
        </header>

        <main class="p-4 sm:p-6 flex-1 fade-in">
            @if (session('success'))
                <div class="mb-5 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm font-semibold flex items-center justify-between shadow-sm">
                    <span class="flex items-center gap-2"><i class="ri-checkbox-circle-line text-lg text-emerald-600"></i> {{ session('success') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700"><i class="ri-close-line text-lg"></i></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        function openMobileSidebar() {
            const drawer = document.getElementById('sidebarDrawer');
            const overlay = document.getElementById('mobileOverlay');
            if (drawer) drawer.classList.remove('-translate-x-full');
            if (overlay) overlay.classList.remove('hidden');
        }

        function closeMobileSidebar() {
            const drawer = document.getElementById('sidebarDrawer');
            const overlay = document.getElementById('mobileOverlay');
            if (drawer) drawer.classList.add('-translate-x-full');
            if (overlay) overlay.classList.add('hidden');
        }

        (function applyInitialSubmenuState() {
            const savedState = localStorage.getItem('userSeksiSubmenuOpen');
            const submenu = document.getElementById('seksiSubmenu');
            const chevron = document.getElementById('seksiChevron');
            
            const shouldBeOpen = (savedState !== 'closed');
            
            if (submenu) {
                if (shouldBeOpen) {
                    submenu.classList.remove('collapsed');
                } else {
                    submenu.classList.add('collapsed');
                }
            }
            if (chevron) {
                chevron.style.transform = shouldBeOpen ? 'rotate(180deg)' : 'rotate(0deg)';
            }
        })();

        // Inisialisasi Otomatis Seamless Single-Box Integrated Tom Select & Auto-Submit Filter
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('select').forEach(function(el) {
                if (!el.classList.contains('no-ts') && !el.tomselect) {
                    var ts = new TomSelect(el, {
                        create: false,
                        maxOptions: 100,
                        placeholder: 'Pilih atau ketik...',
                    });
                    
                    // Instant Auto-Apply Filter ketika opsi diklik
                    if (el.hasAttribute('onchange') || (el.form && el.form.id === 'filterForm')) {
                        ts.on('change', function() {
                            if (el.form) el.form.submit();
                        });
                    }
                }
            });
        });

        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                document.documentElement.classList.remove('preload');
            }, 60);
        });

        function toggleSeksiSubmenu(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            const submenu = document.getElementById('seksiSubmenu');
            const chevron = document.getElementById('seksiChevron');
            
            if (submenu.classList.contains('collapsed')) {
                submenu.classList.remove('collapsed');
                if (chevron) chevron.style.transform = 'rotate(180deg)';
                localStorage.setItem('userSeksiSubmenuOpen', 'open');
            } else {
                submenu.classList.add('collapsed');
                if (chevron) chevron.style.transform = 'rotate(0deg)';
                localStorage.setItem('userSeksiSubmenuOpen', 'closed');
            }
        }
    </script>

    @yield('scripts')
</body>
</html>
