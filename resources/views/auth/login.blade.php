<!DOCTYPE html>
<html lang="id" class="h-full bg-gradient-to-br from-slate-950 via-slate-900 to-teal-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Sistem - SIAKER RSJKO Engku Haji Daud</title>
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
            padding: 0.75rem 1rem !important;
            font-size: 0.95rem !important;
            font-weight: 600 !important;
            color: #ffffff !important;
        }
        .ts-wrapper.dropdown-active .ts-control {
            border-bottom-left-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }
        .ts-dropdown {
            background-color: #020617 !important;
            border: 1.5px solid #0d9488 !important;
            border-top: none !important;
            border-bottom-left-radius: 1rem !important;
            border-bottom-right-radius: 1rem !important;
            color: #ffffff !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.7) !important;
            z-index: 9999 !important;
        }
        .ts-dropdown .option {
            padding: 0.65rem 1rem !important;
            color: #e2e8f0 !important;
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
    </style>
</head>
<body class="h-full flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-slate-900/85 backdrop-blur-xl border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">

        <div class="text-center space-y-2">
            <div class="w-16 h-16 bg-teal-500 rounded-2xl flex items-center justify-center text-white mx-auto shadow-lg shadow-teal-500/30">
                <i class="ri-hospital-line text-3xl"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-white tracking-tight">SIAKER</h2>
            <p class="text-xs text-teal-400 font-semibold uppercase tracking-wider">RSJKO Engku Haji Daud</p>
            <p class="text-xs text-slate-400">Sistem Inventaris Alat Kesehatan Rumah Sakit</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">Pilih Otoritas Login Anda (Tanpa Password):</label>

                <div class="grid grid-cols-1 gap-3">
                    
                    <!-- Option 1: Ruangan Elektromedis (Admin SIAKER) -->
                    <label class="role-card flex items-center gap-3 p-4 bg-slate-950 border border-teal-500/40 rounded-2xl cursor-pointer hover:border-teal-400 transition">
                        <input type="radio" name="role" value="elektromedis" checked onchange="toggleRuanganDropdown()" class="w-5 h-5 text-teal-500 focus:ring-teal-500 border-slate-600 bg-slate-950">
                        <div>
                            <p class="font-bold text-teal-300 text-base">Ruangan Elektromedis (Admin)</p>
                            <p class="text-xs text-slate-400 mt-0.5">Otoritas perbaikan, notifikasi, & pengembalian unit alkes</p>
                        </div>
                    </label>

                    <!-- Option 2: Petugas Ruangan Operasional -->
                    <label class="role-card flex items-center gap-3 p-4 bg-slate-950 border border-slate-800 rounded-2xl cursor-pointer hover:border-teal-500/50 transition">
                        <input type="radio" name="role" value="ruangan" onchange="toggleRuanganDropdown()" class="w-5 h-5 text-teal-500 focus:ring-teal-500 border-slate-600 bg-slate-950">
                        <div>
                            <p class="font-bold text-white text-base">Petugas Ruangan Operasional</p>
                            <p class="text-xs text-slate-400 mt-0.5">Pelaporan barang rusak & mutasi alkes ruangan</p>
                        </div>
                    </label>

                    <!-- Dynamic Ruangan Select Dropdown -->
                    <div id="ruanganDropdownContainer" class="pl-2 pt-1 pb-1 space-y-1.5 hidden">
                        <label class="block text-xs font-bold text-teal-300 uppercase tracking-wider">Pilih Ruangan Operasional Anda:</label>
                        <select id="ruanganSelect" name="ruangan_id" class="w-full">
                            @foreach ($ruanganList as $ruang)
                                @if ($ruang->nama_ruangan !== 'Elektromedis')
                                    <option value="{{ $ruang->id }}">{{ $ruang->nama_ruangan }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <!-- Option 3: Tata Usaha / Direksi -->
                    <label class="role-card flex items-center gap-3 p-4 bg-slate-950 border border-slate-800 rounded-2xl cursor-pointer hover:border-teal-500/50 transition">
                        <input type="radio" name="role" value="tata_usaha" onchange="toggleRuanganDropdown()" class="w-5 h-5 text-teal-500 focus:ring-teal-500 border-slate-600 bg-slate-950">
                        <div>
                            <p class="font-bold text-white text-base">Tata Usaha / Direksi</p>
                            <p class="text-xs text-slate-400 mt-0.5">Pengawasan rekapitulasi inventaris (Read-Only)</p>
                        </div>
                    </label>
                </div>
            </div>

            <button type="submit" class="w-full py-4 bg-teal-600 hover:bg-teal-700 text-white font-extrabold text-base rounded-2xl shadow-lg shadow-teal-600/30 transition flex items-center justify-center gap-2 mt-4">
                <i class="ri-login-box-line text-xl"></i>
                Masuk ke Aplikasi SIAKER
            </button>
        </form>

        <div class="text-center pt-2 border-t border-slate-800/80">
            <p class="text-xs text-slate-500">&copy; 2026 SIAKER - RSJKO Engku Haji Daud</p>
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
                });
            }
        });

        function toggleRuanganDropdown() {
            const role = document.querySelector('input[name="role"]:checked')?.value;
            const container = document.getElementById('ruanganDropdownContainer');
            if (container) {
                container.style.display = (role === 'ruangan') ? 'block' : 'none';
            }
        }
        toggleRuanganDropdown();
    </script>
</body>
</html>
