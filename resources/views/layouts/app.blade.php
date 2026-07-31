<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Sistem Inventaris Alat Kesehatan Rumah Sakit</title>

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
    </style>
</head>
<body class="h-full text-slate-800 antialiased flex flex-col">

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
                        <h1 class="font-extrabold text-xl tracking-wide text-teal-400">SIAKER</h1>
                        <p class="text-[11px] text-slate-400 leading-tight">Sistem Inventaris Alat Kesehatan Rumah Sakit</p>
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
                    <span>Perbaikan & Kalibrasi</span>
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
            <p class="text-[11px] text-slate-500">&copy; 2026 SIAKER</p>
        </div>
    </aside>

    <div class="md:ml-72 min-h-screen flex flex-col flex-1 bg-slate-50">
        <!-- Top Right Header -->
        <header class="bg-white border-b border-slate-200 px-4 sm:px-6 py-3.5 flex items-center justify-between shadow-sm sticky top-0 z-20">
            <div class="flex items-center gap-3">
                <button type="button" onclick="openMobileSidebar()" class="md:hidden p-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 transition focus:outline-none" title="Buka Menu Navigasi">
                    <i class="ri-menu-line text-xl"></i>
                </button>
                <h2 class="font-extrabold text-slate-800 text-base tracking-tight">SISTEM INVENTARIS ALAT KESEHATAN RUMAH SAKIT (SIAKER)</h2>
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

    <script>
        function openMobileSidebar() {
            document.getElementById('sidebarDrawer').classList.remove('-translate-x-full');
            document.getElementById('mobileSidebarOverlay').classList.remove('hidden');
        }

        function closeMobileSidebar() {
            document.getElementById('sidebarDrawer').classList.add('-translate-x-full');
            document.getElementById('mobileSidebarOverlay').classList.add('hidden');
        }
    </script>
</body>
</html>
