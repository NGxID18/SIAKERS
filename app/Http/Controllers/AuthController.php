<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        // Jika pengguna sudah login, langsung arahkan ke Dashboard
        if (session()->has('user_role')) {
            return redirect()->route('dashboard');
        }

        $ruanganList = Ruangan::orderBy('nama_ruangan', 'asc')->get();
        return view('auth.login', compact('ruanganList'));
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'role' => 'required|string|in:elektromedis,ruangan,tata_usaha',
            'ruangan_id' => 'nullable|integer|exists:ruangan,id',
        ]);

        $role = $validated['role'];

        if ($role === 'elektromedis') {
            $elektromedisRuang = Ruangan::where('nama_ruangan', 'Elektromedis')->first();
            session([
                'user_role' => 'elektromedis',
                'user_role_label' => 'Ruangan Elektromedis (Admin SIAKERS)',
                'user_ruangan_id' => $elektromedisRuang ? $elektromedisRuang->id : 1,
                'user_ruangan_name' => 'Elektromedis',
            ]);
            $msg = 'Berhasil masuk sebagai Ruangan Elektromedis (Admin Utama SIAKERS).';
        } elseif ($role === 'tata_usaha') {
            session([
                'user_role' => 'tata_usaha',
                'user_role_label' => 'Tata Usaha / Direksi (Read-Only)',
                'user_ruangan_id' => 0,
                'user_ruangan_name' => 'Direksi & Tata Usaha',
            ]);
            $msg = 'Berhasil masuk sebagai Tata Usaha / Direksi (Pengawasan Read-Only).';
        } else {
            $ruanganId = (int) ($validated['ruangan_id'] ?? 1);
            $ruangan = Ruangan::find($ruanganId);
            $namaRuangan = $ruangan ? $ruangan->nama_ruangan : 'Ruangan Operasional';
            session([
                'user_role' => 'ruangan',
                'user_role_label' => "Petugas Ruangan {$namaRuangan}",
                'user_ruangan_id' => $ruanganId,
                'user_ruangan_name' => $namaRuangan,
            ]);
            $msg = "Berhasil masuk sebagai Petugas Ruangan {$namaRuangan}.";
        }

        return redirect()->route('dashboard')->with('success', $msg);
    }

    public function logout()
    {
        session()->forget(['user_role', 'user_role_label', 'user_ruangan_id', 'user_ruangan_name']);
        return redirect()->route('login')->with('success', 'Anda telah keluar dari aplikasi SIAKERS.');
    }
}
