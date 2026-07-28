<!DOCTYPE html>
<html lang="id" class="h-full bg-gradient-to-br from-slate-950 via-slate-900 to-teal-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login SIAKER ERP RS</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    <!-- Tom Select UI CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }

        .role-card {
            transition: all 0.2s ease-in-out;
        }
        .role-card:hover {
            transform: translateY(-2px);
        }

        /* Seamless Single-Box Dark Mode Theme for Login Dropdown */
        .ts-control {
            background-color: #020617 !important;
            border: 1.5px solid #0d9488 !important;
            border-radius: 1rem !important;
            padding: 0.75rem 1rem !important;
            font-size: 0.875rem !important;
            font-weight: 700 !important;
            color: #ffffff !important;
        }
        .ts-wrapper.dropdown-active .ts-control {
            border-bottom-left-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
            border-color: #2dd4bf !important;
        }
        .ts-dropdown {
            background-color: #0f172a !important;
            border: 1.5px solid #2dd4bf !important;
            border-top: none !important;
            border-bottom-left-radius: 1rem !important;
            border-bottom-right-radius: 1rem !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important;
            padding: 0.5rem !important;
            margin-top: 0 !important;
            color: #ffffff !important;
        }
        .ts-dropdown .option {
            padding: 0.75rem 1rem !important;
            border-radius: 0.625rem !important;
            color: #cbd5e1 !important;
            font-weight: 600 !important;
        }
        .ts-dropdown .option.active, .ts-dropdown .option:hover {
            background-color: #134e4a !important;
            color: #2dd4bf !important;
        }
        .ts-dropdown .option.selected {
            background-color: #0d9488 !important;
            color: #ffffff !important;
        }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4 sm:p-6 text-slate-100 antialiased min-h-screen">
    <div class="max-w-lg w-full space-y-6 my-auto">
        <!-- Header Brand -->
        <div class="text-center space-y-3">
            <div class="inline-flex w-16 h-16 rounded-3xl bg-gradient-to-tr from-teal-600 to-teal-400 text-white items-center justify-center shadow-xl shadow-teal-500/20 border border-teal-300/30 mb-1">
                <i class="ri-hospital-line text-3xl"></i>
            </div>
            <h1 class="text-3xl font-extrabold tracking-tight text-white">SIAKER ERP RS</h1>
            <p class="text-slate-300 text-sm sm:text-base max-w-sm mx-auto font-medium">Sistem Pengelolaan & Monitoring Inventaris Alat Kesehatan Rumah Sakit</p>
        </div>

        <!-- Login Form Container -->
        <form method="POST" action="/login" class="bg-slate-900/90 backdrop-blur-xl p-6 sm:p-8 rounded-3xl border border-slate-800 shadow-2xl space-y-5">
            @csrf

            <div class="border-b border-slate-800 pb-3">
                <h3 class="font-bold text-slate-200 text-base">Pilih Hak Akses Pengguna</h3>
                <p class="text-xs text-slate-400 mt-0.5">Silakan pilih peran login Anda untuk mengakses data alkes</p>
            </div>

            <div class="space-y-3.5">
                <!-- Option 1: Administrator System -->
                <label class="role-card flex items-center justify-between p-4 rounded-2xl border-2 border-slate-800 bg-slate-800/40 hover:bg-slate-800/90 hover:border-amber-500/60 cursor-pointer transition-all duration-200 group">
                    <div class="flex items-center gap-3.5">
                        <input type="radio" name="role" value="admin" onchange="toggleSeksiDropdown()" class="w-5 h-5 text-teal-500 focus:ring-teal-500 border-slate-600 bg-slate-950">
                        <div>
                            <p class="font-bold text-amber-400 text-sm sm:text-base group-hover:text-amber-300">Administrator System</p>
                            <p class="text-xs text-slate-400 mt-0.5">Akses penuh edit seluruh inventaris RS</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center text-xl shrink-0">
                        <i class="ri-shield-user-line"></i>
                    </div>
                </label>

                <!-- Option 2: Seksi Operasional -->
                <label class="role-card flex items-center justify-between p-4 rounded-2xl border-2 border-teal-500/80 bg-teal-950/30 hover:bg-slate-800/90 hover:border-teal-400 cursor-pointer transition-all duration-200 group">
                    <div class="flex items-center gap-3.5">
                        <input type="radio" name="role" value="seksi" checked onchange="toggleSeksiDropdown()" class="w-5 h-5 text-teal-500 focus:ring-teal-500 border-slate-600 bg-slate-950">
                        <div>
                            <p class="font-bold text-teal-300 text-sm sm:text-base group-hover:text-teal-200">Seksi Operasional</p>
                            <p class="text-xs text-slate-400 mt-0.5">Kelola & edit data alkes seksi sendiri</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/20 text-teal-400 flex items-center justify-center text-xl shrink-0">
                        <i class="ri-building-line"></i>
                    </div>
                </label>

                <!-- Dynamic Seksi Select Dropdown -->
                <div id="seksiDropdownContainer" class="pl-2 pt-1 pb-1 space-y-1.5">
                    <label class="block text-xs font-bold text-teal-300 uppercase tracking-wider">Pilih Seksi Operasional RS:</label>
                    <select id="seksiSelect" name="seksi_id" class="w-full px-4 py-3.5 bg-slate-950 border border-teal-500/50 rounded-2xl text-sm font-semibold text-white focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition-all shadow-inner">
                        @foreach ($seksiList as $seksi)
                            <option value="{{ $seksi->id }}">{{ $seksi->nama_seksi }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Option 3: Tata Usaha -->
                <label class="role-card flex items-center justify-between p-4 rounded-2xl border-2 border-slate-800 bg-slate-800/40 hover:bg-slate-800/90 hover:border-slate-600 cursor-pointer transition-all duration-200 group">
                    <div class="flex items-center gap-3.5">
                        <input type="radio" name="role" value="tata_usaha" onchange="toggleSeksiDropdown()" class="w-5 h-5 text-teal-500 focus:ring-teal-500 border-slate-600 bg-slate-950">
                        <div>
                            <p class="font-bold text-slate-200 text-sm sm:text-base group-hover:text-white">Tata Usaha</p>
                            <p class="text-xs text-slate-400 mt-0.5">Akses pengawasan (hanya lihat data)</p>
                        </div>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-slate-700/20 border border-slate-700/40 text-slate-300 flex items-center justify-center text-xl shrink-0">
                        <i class="ri-eye-line"></i>
                    </div>
                </label>
            </div>

            <button type="submit" class="w-full py-4 bg-teal-500 hover:bg-teal-400 text-slate-950 font-extrabold text-base rounded-2xl shadow-xl shadow-teal-500/25 transition-all duration-200 flex items-center justify-center gap-2 mt-6 active:scale-[0.99]">
                <i class="ri-login-circle-line text-xl"></i> Masuk Sistem SIAKER
            </button>
        </form>
        
        <p class="text-center text-xs text-slate-500">&copy; 2026 Rumah Sakit SIAKER ERP System. All rights reserved.</p>
    </div>

    <script>
        let tsInstance = null;
        document.addEventListener('DOMContentLoaded', function() {
            const selectEl = document.getElementById('seksiSelect');
            if (selectEl) {
                tsInstance = new TomSelect(selectEl, {
                    create: false,
                    placeholder: 'Ketik nama seksi...'
                });
            }
        });

        function toggleSeksiDropdown() {
            const role = document.querySelector('input[name="role"]:checked').value;
            const container = document.getElementById('seksiDropdownContainer');
            container.style.display = (role === 'seksi') ? 'block' : 'none';
        }
        toggleSeksiDropdown();
    </script>
</body>
</html>
