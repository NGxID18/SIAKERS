<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - SIAKERS RSJKO Engku Haji Daud</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,400&family=Source+Sans+3:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

    <!-- RemixIcon CDN -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <!-- Tom Select CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <style>
        body { font-family: 'Source Sans 3', 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; }

        /* Smooth Collapsible Sidebar Transitions */
        #sidebarDrawer {
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #mainContentWrapper {
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #collapseBtnIcon {
            transition: transform 0.3s ease-in-out;
        }

        /* Collapsed Mini Sidebar Styles (When body has .sidebar-collapsed) */
        @media (min-width: 768px) {
            .sidebar-collapsed #sidebarDrawer {
                width: 5rem !important; /* 80px */
            }
            .sidebar-collapsed #mainContentWrapper {
                margin-left: 5rem !important; /* 80px */
            }
            .sidebar-collapsed .sidebar-text {
                display: none !important;
            }
            .sidebar-collapsed .sidebar-item {
                justify-content: center !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
            .sidebar-collapsed .sidebar-brand {
                justify-content: center !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
            .sidebar-collapsed #collapseBtnIcon {
                transform: rotate(180deg);
            }
        }

        /* Premium Tom Select UI Styles */
        .ts-wrapper {
            border-radius: 0.75rem !important;
            width: 100% !important;
        }
        .ts-control {
            border-radius: 0.75rem !important;
            background-color: #f8fafc !important;
            border: 1px solid #cbd5e1 !important;
            padding: 0.5rem 0.9rem !important;
            min-height: 46px !important;
            height: 46px !important;
            display: flex !important;
            align-items: center !important;
            font-size: 0.9rem !important;
            font-weight: 600 !important;
            color: #1e293b !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03) !important;
            transition: all 0.2s ease !important;
        }
        .ts-control .item {
            font-size: 0.9rem !important;
            font-weight: 600 !important;
            color: #0f172a !important;
        }
        .ts-control > input {
            font-size: 0.9rem !important;
            font-weight: 500 !important;
            color: #1e293b !important;
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
            padding: 6px !important;
            background: #ffffff !important;
        }
        .ts-dropdown .option {
            padding: 10px 14px !important;
            border-radius: 0.5rem !important;
            font-size: 0.9rem !important;
            font-weight: 500 !important;
            color: #1e293b !important;
        }
        .ts-dropdown .option.active, .ts-dropdown .option:hover {
            background-color: #0d9488 !important;
            color: #ffffff !important;
        }

        /* Animated Notification Bell Swing Keyframes */
        @keyframes bellRing {
            0% { transform: rotate(0deg) scale(1); }
            15% { transform: rotate(20deg) scale(1.2); }
            30% { transform: rotate(-20deg) scale(1.2); }
            45% { transform: rotate(15deg) scale(1.15); }
            60% { transform: rotate(-15deg) scale(1.1); }
            75% { transform: rotate(8deg) scale(1.05); }
            100% { transform: rotate(0deg) scale(1); }
        }
        .animate-bell-ring {
            display: inline-block;
            animation: bellRing 0.55s ease-in-out;
        }

        /* Smooth Dropdown Popover Animations */
        #notificationDropdown {
            transition: opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1), transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.25s;
            opacity: 0;
            transform: translateY(-12px) scale(0.94);
            visibility: hidden;
            pointer-events: none;
        }
        #notificationDropdown.dropdown-active {
            opacity: 1;
            transform: translateY(0) scale(1);
            visibility: visible;
            pointer-events: auto;
        }
    </style>

    <script>
        // Restore Sidebar Fold/Expand State Immediately to Prevent FLC
        if (localStorage.getItem('siakers_sidebar_collapsed') === 'true' && window.innerWidth >= 768) {
            document.documentElement.classList.add('sidebar-collapsed');
        }
    </script>
</head>
<body id="appBody" class="h-full text-slate-800 antialiased flex flex-col min-h-screen">

    <script>
        // Sync class from html to body if present
        if (document.documentElement.classList.contains('sidebar-collapsed')) {
            document.body.classList.add('sidebar-collapsed');
        }
    </script>

    @php
        $currentRole = session('user_role', 'elektromedis');
        $userRoleLabel = session('user_role_label', 'Instalasi Elektromedis');
        $unreadNotifCount = \App\Models\Notification::where('dibaca', false)->count();
        $recentNotifs = \App\Models\Notification::with('ruanganAsal')->latest()->take(5)->get();
    @endphp

    <!-- Mobile Drawer Overlay -->
    <div id="mobileSidebarOverlay" onclick="closeMobileSidebar()" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-40 hidden md:hidden transition-opacity duration-300"></div>

    <!-- FULL-WIDTH TOP NAVBAR (Teal Dark Palette - Memanjang 100% Left to Right) -->
    <header class="w-full h-16 bg-gradient-to-r from-teal-950 via-teal-900 to-teal-950 text-white border-b border-teal-800/80 px-4 sm:px-6 flex items-center justify-between shadow-md sticky top-0 z-40 shrink-0 relative">
        
        <!-- Left: SIAKERS Logo & RSJKO Engku Haji Daud Subtitle (Level with Top Bar) -->
        <div class="flex items-center gap-3 z-10">
            <button type="button" onclick="openMobileSidebar()" class="md:hidden p-2 rounded-xl bg-teal-800/80 text-white hover:bg-teal-700 transition focus:outline-none" title="Buka Menu Navigasi">
                <i class="ri-menu-line text-xl"></i>
            </button>

            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-2xl bg-teal-500 text-white flex items-center justify-center shadow-lg shadow-teal-500/30 shrink-0 group-hover:scale-105 transition-transform" title="SIAKERS - RSJKO Engku Haji Daud">
                    <i class="ri-hospital-line text-2xl"></i>
                </div>
                <div class="hidden sm:block">
                    <h2 class="font-black text-white text-base tracking-wide leading-tight group-hover:text-teal-200 transition-colors">SIAKERS</h2>
                    <p class="text-[10px] text-teal-300 font-semibold tracking-normal leading-none mt-0.5">RSJKO Engku Haji Daud</p>
                </div>
            </a>
        </div>

        <!-- Center: Prominent SIAKERS Title (Centered with Title Font Size) -->
        <div class="absolute left-1/2 -translate-x-1/2 text-center pointer-events-none z-10">
            <h1 class="text-xl sm:text-2xl md:text-3xl font-black tracking-[0.2em] text-white drop-shadow-md">
                <span class="bg-gradient-to-r from-teal-200 via-white to-teal-200 bg-clip-text text-transparent">SIAKERS</span>
            </h1>
        </div>

        <!-- Header Action Controls: Active Role Badge, Notification Bell (Admin Only) & Logout -->
        <div class="flex items-center gap-2.5 z-10 ml-auto">

            <!-- Active Role Badge (Static Info - Must Logout to Change Role) -->
            <div class="h-9 px-3 py-1.5 {{ $currentRole === 'elektromedis' ? 'bg-amber-500/20 text-amber-200 border-amber-400/40' : 'bg-teal-800/80 text-teal-100 border-teal-600/60' }} border rounded-xl text-xs font-bold flex items-center gap-1.5 backdrop-blur-xs shrink-0">
                <i class="{{ $currentRole === 'elektromedis' ? 'ri-shield-user-fill text-amber-300' : 'ri-hospital-line text-teal-300' }} text-sm"></i>
                <span>{{ $userRoleLabel }}</span>
            </div>

            <!-- Notification Bell Center (KHUSUS ELEKTROMEDIS ADMIN) -->
            @if ($currentRole === 'elektromedis')
                <div class="relative">
                    <button type="button" id="notifBellBtn" onclick="toggleNotificationDropdown()" class="h-9 px-3 py-1.5 rounded-xl bg-teal-800/80 text-white hover:bg-teal-700 transition focus:outline-none border border-teal-600/60 flex items-center justify-center relative group shrink-0" title="Notifikasi Laporan Perbaikan Masuk">
                        <i id="notifBellIcon" class="ri-notification-3-line text-base block transition-transform group-hover:scale-110"></i>
                        @if ($unreadNotifCount > 0)
                            <span class="absolute -top-1 -right-1 w-4 h-4 bg-rose-500 text-white text-[9px] font-black rounded-full flex items-center justify-center border border-teal-950 animate-bounce">
                                {{ $unreadNotifCount }}
                            </span>
                        @endif
                    </button>

                    <!-- Animated Dropdown Box -->
                    <div id="notificationDropdown" class="absolute right-0 mt-3 w-80 sm:w-96 bg-white text-slate-800 rounded-2xl border border-slate-200 shadow-2xl overflow-hidden z-50">
                        <div class="p-4 bg-teal-950 text-white flex items-center justify-between">
                            <h4 class="font-bold text-sm flex items-center gap-2">
                                <i class="ri-notification-3-line text-teal-400"></i>
                                Notifikasi Laporan Perbaikan
                            </h4>
                            @if ($unreadNotifCount > 0)
                                <form method="POST" action="{{ route('notifications.read-all') }}">
                                    @csrf
                                    <button type="submit" class="text-xs text-teal-300 hover:underline">Tandai Dibaca</button>
                                </form>
                            @endif
                        </div>

                        <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
                            @forelse ($recentNotifs as $n)
                                <div class="p-3.5 hover:bg-slate-50 transition {{ !$n->dibaca ? 'bg-amber-50/50' : '' }}">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="font-bold text-xs text-slate-900 truncate">{{ $n->judul }}</span>
                                        <span class="text-[10px] text-slate-400 shrink-0">{{ $n->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-xs text-slate-600 mt-1 leading-relaxed">{{ $n->pesan }}</p>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 text-center py-6">Belum ada notifikasi laporan perbaikan.</p>
                            @endforelse
                        </div>

                        <div class="p-3 bg-slate-50 border-t border-slate-100 text-center">
                            <a href="{{ route('pemeliharaan.index') }}" class="text-xs font-bold text-teal-700 hover:underline">Lihat Semua Laporan Perbaikan &rarr;</a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Keluar / Logout Button -->
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="h-9 px-3 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-sm border border-rose-500 shrink-0" title="Keluar / Ganti Akun">
                    <i class="ri-logout-box-r-line text-base"></i>
                    <span class="hidden md:inline">Keluar</span>
                </button>
            </form>

        </div>
    </header>

    <!-- BODY CONTAINER BELOW TOP HEADER -->
    <div class="flex flex-1 relative">

        <!-- Responsive Sidebar Drawer (Collapsible) -->
        <aside id="sidebarDrawer" class="fixed top-16 left-0 bottom-0 w-72 bg-slate-900 text-white z-30 transform -translate-x-full md:translate-x-0">
            
            <!-- Animated Collapse/Expand Floating Button (Layer Paling Atas z-50, Unclipped) -->
            <button type="button" id="sidebarCollapseBtn" onclick="toggleSidebarCollapse()" class="hidden md:flex absolute -right-3.5 top-1/2 -translate-y-1/2 w-7 h-7 bg-teal-600 hover:bg-teal-500 text-white rounded-full items-center justify-center shadow-xl border-2 border-slate-900 transition-all duration-300 hover:scale-110 z-50 focus:outline-none" title="Ciutkan / Perluas Sidebar">
                <i id="collapseBtnIcon" class="ri-arrow-left-s-line text-lg"></i>
            </button>

            <!-- Inner Scrollable Sidebar Container -->
            <div class="w-full h-full flex flex-col justify-between overflow-y-auto overflow-x-hidden">
                <div>
                    <!-- Mobile Close Sidebar Header (Mobile Only) -->
                    <div class="px-4 py-3 flex items-center justify-between md:hidden border-b border-slate-800">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Navigasi System</span>
                        <button type="button" onclick="closeMobileSidebar()" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800">
                            <i class="ri-close-line text-xl"></i>
                        </button>
                    </div>

                    <nav class="p-3 pt-4 space-y-1.5">
                        <div class="px-3 py-2 text-xs font-bold text-slate-400 uppercase tracking-wider sidebar-text">Menu Utama</div>

                        <!-- Menu 1: Dashboard Inventaris -->
                        <a href="{{ route('dashboard') }}" onclick="closeMobileSidebar()" class="sidebar-item flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/30' : 'text-slate-300 hover:bg-slate-800' }}" title="Dashboard Inventaris">
                            <i class="ri-dashboard-3-line text-xl text-sky-400 shrink-0"></i>
                            <span class="sidebar-text">Dashboard Inventaris</span>
                        </a>

                        <!-- Menu 2: Pindah Ruangan Alkes -->
                        <a href="{{ route('mutasi.index') }}" onclick="closeMobileSidebar()" class="sidebar-item flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('mutasi.index') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/30' : 'text-slate-300 hover:bg-slate-800' }}" title="Pindah Ruangan Alkes">
                            <i class="ri-arrow-left-right-line text-xl text-indigo-400 shrink-0"></i>
                            <span class="sidebar-text">Pindah Ruangan Alkes</span>
                        </a>

                        <!-- Menu 3: Perbaikan Alkes -->
                        <a href="{{ route('pemeliharaan.index') }}" onclick="closeMobileSidebar()" class="sidebar-item flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('pemeliharaan.*') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/30' : 'text-slate-300 hover:bg-slate-800' }}" title="Perbaikan Alkes">
                            <i class="ri-tools-line text-xl text-amber-400 shrink-0"></i>
                            <div class="flex items-center justify-between w-full sidebar-text">
                                <span>Perbaikan Alkes</span>
                                @if ($currentRole === 'elektromedis' && $unreadNotifCount > 0)
                                    <span class="px-2 py-0.5 bg-rose-500 text-white text-xs font-bold rounded-full animate-pulse">
                                        {{ $unreadNotifCount }}
                                    </span>
                                @endif
                            </div>
                        </a>

                        <!-- Menu 4: Kalibrasi Alkes -->
                        <a href="{{ route('kalibrasi.index') }}" onclick="closeMobileSidebar()" class="sidebar-item flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('kalibrasi.*') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/30' : 'text-slate-300 hover:bg-slate-800' }}" title="Kalibrasi Alkes">
                            <i class="ri-verified-badge-line text-xl text-emerald-400 shrink-0"></i>
                            <span class="sidebar-text">Kalibrasi Alkes</span>
                        </a>

                        <!-- MASTER DATA HEADER -->
                        <div class="px-3 pt-5 pb-1 text-xs font-bold text-slate-400 uppercase tracking-wider sidebar-text">Master Data</div>

                        <!-- Menu 5: Inventaris Alkes -->
                        <a href="{{ route('alkes.index') }}" onclick="closeMobileSidebar()" class="sidebar-item flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('alkes.*') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/30' : 'text-slate-300 hover:bg-slate-800' }}" title="Inventaris Alkes">
                            <i class="ri-stethoscope-line text-xl text-teal-300 shrink-0"></i>
                            <span class="sidebar-text">Inventaris Alkes</span>
                        </a>

                        <!-- Menu 6: Daftar Ruangan -->
                        <a href="{{ route('ruangan.index') }}" onclick="closeMobileSidebar()" class="sidebar-item flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('ruangan.*') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/30' : 'text-slate-300 hover:bg-slate-800' }}" title="Daftar Ruangan">
                            <i class="ri-building-4-line text-xl text-purple-400 shrink-0"></i>
                            <span class="sidebar-text">Daftar Ruangan</span>
                        </a>

                        <!-- Menu 7: Riwayat Aktivitas Sistem -->
                        <a href="{{ route('activity-logs.index') }}" onclick="closeMobileSidebar()" class="sidebar-item flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('activity-logs.*') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/30' : 'text-slate-300 hover:bg-slate-800' }}" title="Riwayat Aktivitas Sistem">
                            <i class="ri-history-line text-xl text-cyan-400 shrink-0"></i>
                            <span class="sidebar-text">Riwayat Aktivitas Sistem</span>
                        </a>
                    </nav>
                </div>

                <!-- Sidebar Footer -->
                <div class="p-4 border-t border-slate-800/60 text-center sidebar-text">
                    <p class="text-[11px] text-slate-500">&copy; 2026 SIAKERS - RSJKO Engku Haji Daud</p>
                </div>
            </div>

        </aside>

        <!-- Main Workspace Content (Shifted for Sidebar on Desktop) -->
        <div id="mainContentWrapper" class="md:ml-72 flex-1 min-h-[calc(100vh-64px)] flex flex-col bg-slate-50">
            <main class="p-4 sm:p-6 lg:p-8 flex-1">
                @if (session('success'))
                    <div id="flashSuccessMsg" class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 font-semibold text-sm flex items-center justify-between shadow-sm">
                        <div class="flex items-center gap-2.5">
                            <i class="ri-checkbox-circle-line text-xl text-emerald-600"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button type="button" onclick="document.getElementById('flashSuccessMsg').remove()" class="text-emerald-500 hover:text-emerald-800 text-lg">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>

    </div>

    <script>
        function openMobileSidebar() {
            document.getElementById('sidebarDrawer').classList.remove('-translate-x-full');
            document.getElementById('mobileSidebarOverlay').classList.remove('hidden');
        }

        function closeMobileSidebar() {
            document.getElementById('sidebarDrawer').classList.add('-translate-x-full');
            document.getElementById('mobileSidebarOverlay').classList.add('hidden');
        }

        function toggleNotificationDropdown() {
            const dropdown = document.getElementById('notificationDropdown');
            const bellIcon = document.getElementById('notifBellIcon');

            // Trigger physical bell ring animation
            if (bellIcon) {
                bellIcon.classList.remove('animate-bell-ring');
                void bellIcon.offsetWidth; // Trigger DOM reflow
                bellIcon.classList.add('animate-bell-ring');
            }

            // Toggle dropdown with smooth scale-fade transition
            if (dropdown) {
                dropdown.classList.toggle('dropdown-active');
            }
        }

        // Close notification dropdown smoothly when clicking outside
        document.addEventListener('click', function(event) {
            const bellBtn = document.getElementById('notifBellBtn');
            const dropdown = document.getElementById('notificationDropdown');

            if (bellBtn && dropdown && !bellBtn.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.remove('dropdown-active');
            }
        });

        function toggleSidebarCollapse() {
            const isCollapsed = document.body.classList.toggle('sidebar-collapsed');
            document.documentElement.classList.toggle('sidebar-collapsed', isCollapsed);
            localStorage.setItem('siakers_sidebar_collapsed', isCollapsed ? 'true' : 'false');
        }

        // Global Auto-initialize Tom Select UI for all select elements
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('select').forEach(function(selectEl) {
                if (!selectEl.tomselect && !selectEl.classList.contains('no-tomselect')) {
                    new TomSelect(selectEl, {
                        create: false,
                        maxOptions: 100
                    });
                }
            });
        });
    </script>
</body>
</html>
