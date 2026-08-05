<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Sistem - SIAKERS RSJKO Engku Haji Daud</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,400&family=Source+Sans+3:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    <!-- Tom Select UI CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <style>
        body { font-family: 'Source Sans 3', 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; }

        .role-card {
            transition: all 0.2s ease-in-out;
        }
        .role-card:hover {
            transform: translateY(-2px);
        }

        .ts-control {
            background-color: #020617 !important;
            border: 1.5px solid #0d9488 !important;
            border-radius: 1rem !important;
            padding: 0.65rem 1rem !important;
            font-size: 0.9rem !important;
            font-weight: 600 !important;
            color: #ffffff !important;
        }
        .ts-wrapper.dropdown-active .ts-control {
            border-bottom-left-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }
        .ts-dropdown {
            background-color: #0f172a !important;
            border: 1.5px solid #0d9488 !important;
            border-radius: 1rem !important;
            color: #ffffff !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.9) !important;
            z-index: 999999 !important;
            max-height: 220px !important;
            overflow-y: auto !important;
        }
        .ts-dropdown .option {
            padding: 0.75rem 1rem !important;
            color: #f1f5f9 !important;
            font-size: 0.925rem !important;
            font-weight: 500 !important;
        }
        .ts-dropdown .option:hover, 
        .ts-dropdown .option.active {
            background-color: #0d9488 !important;
            color: #ffffff !important;
        }
        .ts-control input {
            color: #ffffff !important;
        }

        /* High Contrast Mode CSS Override */
        body.high-contrast {
            filter: contrast(140%) !important;
        }
        body.high-contrast * {
            border-color: #ffffff !important;
        }
    </style>
</head>
<body id="loginBody" class="h-screen w-screen overflow-hidden flex items-center justify-center p-4 relative bg-slate-950">

    <!-- Background Hospital Image with Blur Overlay -->
    <div class="fixed inset-0 z-0 bg-cover bg-center bg-no-repeat scale-105 filter blur-xs" style="background-image: url('{{ asset('images/RSJKO EHD.jpg') }}');"></div>
    <div id="bgOverlay" class="fixed inset-0 z-0 bg-gradient-to-tr from-slate-950/90 via-slate-950/80 to-teal-950/85 backdrop-blur-sm transition-colors duration-300"></div>

    <!-- Login Glassmorphic Container (Compact Height, No Page Scrollbar) -->
    <div id="loginCard" class="relative z-10 w-full max-w-md bg-slate-950/85 backdrop-blur-2xl border border-white/15 rounded-3xl p-6 sm:p-7 shadow-2xl space-y-4 my-auto transition-all duration-300">

        <div class="text-center space-y-1.5">
            <div class="w-14 h-14 bg-teal-500 rounded-2xl flex items-center justify-center text-white mx-auto shadow-lg shadow-teal-500/40">
                <i class="ri-hospital-line text-2xl"></i>
            </div>
            <h2 class="text-2xl font-extrabold text-white tracking-tight">SIAKERS</h2>
            <p class="text-[11px] text-teal-400 font-bold uppercase tracking-wider">RSJKO Engku Haji Daud</p>
            <p class="text-xs text-slate-300 font-medium">Sistem Inventaris Alat Kesehatan Rumah Sakit</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-3.5">
            @csrf

            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-200 uppercase tracking-wider">Pilih Otoritas Login Anda (Tanpa Password):</label>

                <div class="grid grid-cols-1 gap-2.5">
                    
                    <!-- Option 1: Ruangan Elektromedis (Admin SIAKERS) -->
                    <label class="role-card flex items-center gap-3 p-3.5 bg-slate-900/90 border border-teal-500/50 rounded-2xl cursor-pointer hover:border-teal-400 transition shadow-inner">
                        <input type="radio" name="role" value="elektromedis" checked onchange="toggleRuanganDropdown()" class="w-4 h-4 text-teal-500 focus:ring-teal-500 border-slate-600 bg-slate-950">
                        <div>
                            <p class="font-bold text-teal-300 text-sm sm:text-base">Ruangan Elektromedis (Admin)</p>
                            <p class="text-[11px] text-slate-300">Otoritas perbaikan, notifikasi, & pengembalian unit alkes</p>
                        </div>
                    </label>

                    <!-- Option 2: Petugas Ruangan Operasional -->
                    <label class="role-card flex items-center gap-3 p-3.5 bg-slate-900/90 border border-slate-700 rounded-2xl cursor-pointer hover:border-teal-500/50 transition shadow-inner">
                        <input type="radio" name="role" value="ruangan" onchange="toggleRuanganDropdown()" class="w-4 h-4 text-teal-500 focus:ring-teal-500 border-slate-600 bg-slate-950">
                        <div>
                            <p class="font-bold text-white text-sm sm:text-base">Petugas Ruangan Operasional</p>
                            <p class="text-[11px] text-slate-300">Pelaporan barang rusak & mutasi alkes ruangan</p>
                        </div>
                    </label>

                    <!-- Smooth Animated Ruangan Select Dropdown -->
                    <div id="ruanganDropdownContainer" class="overflow-hidden max-h-0 opacity-0 transition-all duration-300 ease-in-out">
                        <div class="pt-1 pb-1 space-y-1">
                            <label class="block text-[11px] font-bold text-teal-300 uppercase tracking-wider">Pilih Ruangan Operasional Anda:</label>
                            <select id="ruanganSelect" name="ruangan_id" class="w-full">
                                @foreach ($ruanganList as $ruang)
                                    @if ($ruang->nama_ruangan !== 'Elektromedis')
                                        <option value="{{ $ruang->id }}">{{ $ruang->nama_ruangan }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Option 3: Tata Usaha / Direksi -->
                    <label class="role-card flex items-center gap-3 p-3.5 bg-slate-900/90 border border-slate-700 rounded-2xl cursor-pointer hover:border-teal-500/50 transition shadow-inner">
                        <input type="radio" name="role" value="tata_usaha" onchange="toggleRuanganDropdown()" class="w-4 h-4 text-teal-500 focus:ring-teal-500 border-slate-600 bg-slate-950">
                        <div>
                            <p class="font-bold text-white text-sm sm:text-base">Tata Usaha / Direksi</p>
                            <p class="text-[11px] text-slate-300">Pengawasan rekapitulasi inventaris (Read-Only)</p>
                        </div>
                    </label>
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-sm sm:text-base rounded-2xl shadow-lg shadow-teal-600/40 transition flex items-center justify-center gap-2 mt-2">
                <i class="ri-login-box-line text-lg"></i>
                Masuk ke Aplikasi SIAKERS
            </button>
        </form>

        <div class="text-center pt-2 border-t border-slate-800">
            <p class="text-[11px] text-slate-400 font-medium">&copy; 2026 SIAKERS - RSJKO Engku Haji Daud</p>
        </div>

    </div>

    <!-- Floating Accessibility Button (Dengan Icon Orang Pinned di Kanan Bawah) -->
    <div class="fixed bottom-5 right-5 z-50">
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
        let tsControlInstance = null;

        document.addEventListener('DOMContentLoaded', function() {
            const selectEl = document.getElementById('ruanganSelect');
            if (selectEl) {
                tsControlInstance = new TomSelect('#ruanganSelect', {
                    create: false,
                    placeholder: 'Ketik nama ruangan...',
                    maxOptions: 50,
                    dropdownParent: 'body'
                });
            }
            applyStoredAccessibility();
        });

        function toggleRuanganDropdown() {
            const role = document.querySelector('input[name="role"]:checked')?.value;
            const container = document.getElementById('ruanganDropdownContainer');
            if (container) {
                if (role === 'ruangan') {
                    container.style.maxHeight = '90px';
                    container.style.opacity = '1';
                    setTimeout(() => {
                        if (document.querySelector('input[name="role"]:checked')?.value === 'ruangan') {
                            container.style.overflow = 'visible';
                        }
                    }, 300);
                } else {
                    container.style.overflow = 'hidden';
                    container.style.maxHeight = '0px';
                    container.style.opacity = '0';
                }
            }
        }

        function toggleAccessibilityMenu() {
            const menu = document.getElementById('accessibilityMenu');
            menu.classList.toggle('hidden');
        }

        function toggleDarkMode() {
            const isLight = document.body.classList.toggle('bg-slate-100');
            localStorage.setItem('siakers_theme', isLight ? 'light' : 'dark');
            updateToggleUI();
        }

        function toggleHighContrast() {
            const isHC = document.body.classList.toggle('high-contrast');
            localStorage.setItem('siakers_high_contrast', isHC ? 'enabled' : 'disabled');
            updateToggleUI();
        }

        function applyStoredAccessibility() {
            if (localStorage.getItem('siakers_high_contrast') === 'enabled') {
                document.body.classList.add('high-contrast');
            }
            updateToggleUI();
        }

        function updateToggleUI() {
            const isLight = document.body.classList.contains('bg-slate-100');
            const isHC = document.body.classList.contains('high-contrast');

            // Theme Toggle Animation Update
            const themePill = document.getElementById('themeTogglePill');
            const themeCircle = document.getElementById('themeToggleCircle');
            if (themePill && themeCircle) {
                if (isLight) {
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
