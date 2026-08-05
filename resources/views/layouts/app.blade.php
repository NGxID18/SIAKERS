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

    <style>
        body { font-family: 'Source Sans 3', 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; }

        /* Dark Mode Theme Classes */
        body.dark-mode {
            background-color: #0f172a !important;
            color: #f8fafc !important;
        }
        body.dark-mode header,
        body.dark-mode main .bg-white {
            background-color: #1e293b !important;
            color: #f8fafc !important;
            border-color: #334155 !important;
        }
        body.dark-mode main .bg-slate-50 {
            background-color: #0f172a !important;
            color: #cbd5e1 !important;
            border-color: #334155 !important;
        }
        body.dark-mode h1, body.dark-mode h2, body.dark-mode h3, body.dark-mode h4, body.dark-mode .text-slate-900, body.dark-mode .text-slate-800 {
            color: #f8fafc !important;
        }
        body.dark-mode .text-slate-600, body.dark-mode .text-slate-500 {
            color: #94a3b8 !important;
        }

        /* High Contrast Mode CSS Override */
        body.high-contrast {
            filter: contrast(150%) !important;
        }
        body.high-contrast * {
            border-color: #000000 !important;
        }
    </style>
</head>
<body id="appBody" class="h-full text-slate-800 antialiased flex flex-col transition-colors duration-300">

    @php
        $currentRole = session('user_role', 'elektromedis');
        $userRoleLabel = session('user_role_label', 'Ruangan Elektromedis (Admin SIAKERS)');
        $unreadNotifCount = \App\Models\Notification::where('dibaca', false)->count();
        $recentNotifs = \App\Models\Notification::with('ruanganAsal')->latest()->take(5)->get();
    @endphp

    <!-- Mobile Drawer Overlay -->
    <div id="mobileSidebarOverlay" onclick="closeMobileSidebar()" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-40 hidden md:hidden transition-opacity duration-300"></div>

    <!-- Responsive Sidebar Drawer -->
    <aside id="sidebarDrawer" class="fixed top-0 left-0 bottom-0 w-72 bg-slate-900 text-white flex flex-col justify-between shadow-2xl z-50 overflow-y-auto transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
        <div>
            <div class="px-6 py-5 border-b border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-teal-500 flex items-center justify-center text-white shadow-lg shadow-teal-500/30 shrink-0">
                        <i class="ri-hospital-line text-2xl"></i>
                    </div>
                    <div class="min-w-0">
                        <h1 class="font-extrabold text-xl tracking-wide text-teal-400">SIAKERS</h1>
                        <p class="text-[11px] text-slate-400 leading-tight">Sistem Inventaris Alat Kesehatan Rumah Sakit RSJKO Engku Haji Daud</p>
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
                    <span>Dashboard Inventaris</span>
                </a>

                <a href="{{ route('mutasi.index') }}" onclick="closeMobileSidebar()" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('mutasi.index') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/30' : 'text-slate-300 hover:bg-slate-800' }}">
                    <i class="ri-arrow-left-right-line text-xl"></i>
                    <span>Pindah Ruangan Alkes</span>
                </a>

                <a href="{{ route('pemeliharaan.index') }}" onclick="closeMobileSidebar()" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('pemeliharaan.index') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/30' : 'text-slate-300 hover:bg-slate-800' }}">
                    <i class="ri-tools-line text-xl"></i>
                    <div class="flex items-center justify-between w-full">
                        <span>Perbaikan & Kalibrasi</span>
                        @if ($unreadNotifCount > 0)
                            <span class="px-2 py-0.5 bg-rose-500 text-white text-xs font-bold rounded-full animate-pulse">
                                {{ $unreadNotifCount }}
                            </span>
                        @endif
                    </div>
                </a>

                <!-- MASTER DATA -->
                <div class="px-3 pt-6 pb-2 text-xs font-bold text-slate-400 uppercase tracking-wider">Master Data</div>

                <a href="{{ route('alkes.index') }}" onclick="closeMobileSidebar()" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('alkes.*') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/30' : 'text-slate-300 hover:bg-slate-800' }}">
                    <i class="ri-stethoscope-line text-xl"></i>
                    <span>Inventaris Alkes</span>
                </a>

                <a href="{{ route('ruangan.index') }}" onclick="closeMobileSidebar()" class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('ruangan.*') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/30' : 'text-slate-300 hover:bg-slate-800' }}">
                    <i class="ri-building-4-line text-lg"></i>
                    <span>Daftar Ruangan</span>
                </a>

                <a href="{{ route('activity-logs.index') }}" onclick="closeMobileSidebar()" class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('activity-logs.*') ? 'bg-teal-600 text-white shadow-md shadow-teal-600/30' : 'text-slate-300 hover:bg-slate-800' }}">
                    <i class="ri-history-line text-lg text-teal-400"></i>
                    <span>Log Aktivitas System</span>
                </a>
            </nav>
        </div>

        <div class="p-4 border-t border-slate-800/60 text-center">
            <p class="text-[11px] text-slate-500">&copy; 2026 SIAKERS - RSJKO Engku Haji Daud</p>
        </div>
    </aside>

    <div class="md:ml-72 min-h-screen flex flex-col flex-1 bg-slate-50">
        <!-- Top Right Header -->
        <header class="bg-white border-b border-slate-200 px-4 sm:px-6 py-3 flex items-center justify-between shadow-sm sticky top-0 z-30">
            <div class="flex items-center gap-3">
                <button type="button" onclick="openMobileSidebar()" class="md:hidden p-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 transition focus:outline-none" title="Buka Menu Navigasi">
                    <i class="ri-menu-line text-xl"></i>
                </button>
                <h2 class="font-extrabold text-slate-800 text-base tracking-tight hidden sm:block">SISTEM INVENTARIS ALAT KESEHATAN RUMAH SAKIT RSJKO ENGKU HAJI DAUD (SIAKERS)</h2>
            </div>

            <!-- Header Action Controls: Active Role Badge, Notification Bell & Logout -->
            <div class="flex items-center gap-2.5">

                <!-- Active Role Badge (Static Info - Must Logout to Change Role) -->
                <div class="px-3 py-1.5 {{ $currentRole === 'elektromedis' ? 'bg-amber-50 text-amber-900 border-amber-300' : 'bg-teal-50 text-teal-900 border-teal-300' }} border rounded-xl text-xs font-bold flex items-center gap-1.5">
                    <i class="{{ $currentRole === 'elektromedis' ? 'ri-shield-user-fill text-amber-600' : 'ri-hospital-line text-teal-600' }} text-sm"></i>
                    <span>{{ $userRoleLabel }}</span>
                </div>

                <!-- Notification Bell Center -->
                <div class="relative">
                    <button type="button" onclick="toggleNotificationDropdown()" class="relative p-2.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-teal-50 hover:text-teal-700 transition focus:outline-none">
                        <i class="ri-notification-3-line text-xl"></i>
                        @if ($unreadNotifCount > 0)
                            <span class="absolute -top-1 -right-1 w-5 h-5 bg-rose-500 text-white text-[10px] font-black rounded-full flex items-center justify-center border-2 border-white animate-bounce">
                                {{ $unreadNotifCount }}
                            </span>
                        @endif
                    </button>

                    <!-- Dropdown Box -->
                    <div id="notificationDropdown" class="hidden absolute right-0 mt-3 w-80 sm:w-96 bg-white rounded-2xl border border-slate-200 shadow-2xl overflow-hidden z-50">
                        <div class="p-4 bg-slate-900 text-white flex items-center justify-between">
                            <h4 class="font-bold text-sm flex items-center gap-2">
                                <i class="ri-notification-3-line text-teal-400"></i>
                                Notifikasi Laporan Perbaikan
                            </h4>
                            @if ($unreadNotifCount > 0)
                                <form method="POST" action="{{ route('notifications.read-all') }}">
                                    @csrf
                                    <button type="submit" class="text-xs text-teal-400 hover:underline">Tandai Dibaca</button>
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
                            <a href="{{ route('pemeliharaan.index') }}" class="text-xs font-bold text-teal-600 hover:underline">Lihat Semua Laporan Perbaikan &rarr;</a>
                        </div>
                    </div>
                </div>

                <!-- Keluar / Logout Button -->
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="p-2.5 rounded-xl bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 hover:text-rose-800 transition flex items-center gap-1" title="Keluar / Ganti Akun">
                        <i class="ri-logout-box-r-line text-lg"></i>
                        <span class="text-xs font-bold hidden md:inline">Keluar</span>
                    </button>
                </form>

            </div>
        </header>

        <!-- Main Workspace Content -->
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

    <!-- Floating Accessibility Button (Dengan Icon Orang Pinned di Kanan Bawah) -->
    <div class="fixed bottom-6 right-6 z-50">
        <button type="button" onclick="toggleAccessibilityMenu()" class="w-13 h-13 p-3.5 rounded-full bg-teal-600 hover:bg-teal-500 text-white shadow-2xl flex items-center justify-center text-2xl transition hover:scale-110 focus:outline-none border-2 border-white/40 shadow-teal-600/40" title="Menu Aksesibilitas Tampilan">
            <i class="ri-accessibility-fill text-2xl"></i>
        </button>

        <!-- Popover Accessibility Options Menu -->
        <div id="accessibilityMenu" class="hidden absolute bottom-16 right-0 w-72 bg-slate-900 border border-slate-700 text-white rounded-2xl p-4 shadow-2xl space-y-3.5 z-50">
            <h4 class="font-bold text-xs uppercase tracking-wider text-teal-400 border-b border-slate-800 pb-2.5 flex items-center gap-2">
                <i class="ri-accessibility-line text-base text-teal-400"></i> Aksesibilitas Tampilan
            </h4>

            <!-- Option 1: Dark Mode / Light Mode -->
            <button type="button" onclick="toggleDarkMode()" class="w-full px-3.5 py-2.5 bg-slate-800 hover:bg-slate-700/80 rounded-xl text-xs font-semibold flex items-center justify-between transition group">
                <span class="flex items-center gap-2 text-slate-200">
                    <i id="themeIcon" class="ri-moon-line text-amber-400 text-sm"></i>
                    <span>Mode Terang / Gelap</span>
                </span>
                <!-- Interactive Animated Pill Switch -->
                <div id="themeTogglePill" class="w-9 h-5 bg-slate-700 rounded-full p-0.5 transition-colors duration-300 flex items-center">
                    <div id="themeToggleCircle" class="w-4 h-4 bg-white rounded-full shadow-md transform translate-x-0 transition-transform duration-300"></div>
                </div>
            </button>

            <!-- Option 2: High Contrast Mode -->
            <button type="button" onclick="toggleHighContrast()" class="w-full px-3.5 py-2.5 bg-slate-800 hover:bg-slate-700/80 rounded-xl text-xs font-semibold flex items-center justify-between transition group">
                <span class="flex items-center gap-2 text-slate-200">
                    <i class="ri-contrast-2-line text-teal-400 text-sm"></i>
                    <span>Kontras Tinggi</span>
                </span>
                <!-- Interactive Animated Pill Switch -->
                <div id="hcTogglePill" class="w-9 h-5 bg-slate-700 rounded-full p-0.5 transition-colors duration-300 flex items-center">
                    <div id="hcToggleCircle" class="w-4 h-4 bg-white rounded-full shadow-md transform translate-x-0 transition-transform duration-300"></div>
                </div>
            </button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            applyStoredAccessibility();
        });

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
            dropdown.classList.toggle('hidden');
        }

        function toggleAccessibilityMenu() {
            const menu = document.getElementById('accessibilityMenu');
            menu.classList.toggle('hidden');
        }

        function toggleDarkMode() {
            const isDark = document.body.classList.toggle('dark-mode');
            localStorage.setItem('siakers_theme', isDark ? 'dark' : 'light');
            updateToggleUI();
        }

        function toggleHighContrast() {
            const isHC = document.body.classList.toggle('high-contrast');
            localStorage.setItem('siakers_high_contrast', isHC ? 'enabled' : 'disabled');
            updateToggleUI();
        }

        function applyStoredAccessibility() {
            if (localStorage.getItem('siakers_theme') === 'dark') {
                document.body.classList.add('dark-mode');
            }
            if (localStorage.getItem('siakers_high_contrast') === 'enabled') {
                document.body.classList.add('high-contrast');
            }
            updateToggleUI();
        }

        function updateToggleUI() {
            const isDark = document.body.classList.contains('dark-mode');
            const isHC = document.body.classList.contains('high-contrast');

            // Theme Toggle Animation Update
            const themePill = document.getElementById('themeTogglePill');
            const themeCircle = document.getElementById('themeToggleCircle');
            if (themePill && themeCircle) {
                if (isDark) {
                    themePill.classList.remove('bg-slate-700');
                    themePill.classList.add('bg-teal-500');
                    themeCircle.classList.remove('translate-x-0');
                    themeCircle.classList.add('translate-x-4');
                } else {
                    themePill.classList.remove('bg-teal-500');
                    themePill.classList.add('bg-slate-700');
                    themeCircle.classList.remove('translate-x-4');
                    themeCircle.classList.add('translate-x-0');
                }
            }

            // High Contrast Toggle Animation Update
            const hcPill = document.getElementById('hcTogglePill');
            const hcCircle = document.getElementById('hcToggleCircle');
            if (hcPill && hcCircle) {
                if (isHC) {
                    hcPill.classList.remove('bg-slate-700');
                    hcPill.classList.add('bg-teal-500');
                    hcCircle.classList.remove('translate-x-0');
                    hcCircle.classList.add('translate-x-4');
                } else {
                    hcPill.classList.remove('bg-teal-500');
                    hcPill.classList.add('bg-slate-700');
                    hcCircle.classList.remove('translate-x-4');
                    hcCircle.classList.add('translate-x-0');
                }
            }
        }
    </script>
</body>
</html>
