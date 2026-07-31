<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        $ruanganList = Ruangan::all();
        return view('auth.login', compact('ruanganList'));
    }

    public function login(Request $request)
    {
        $role = $request->input('role', 'ruangan');
        $ruanganId = (int) $request->input('ruangan_id', 1);

        if ($role === 'admin') {
            session([
                'is_admin' => true,
                'user_role' => 'Administrator System',
                'user_ruangan_id' => 0,
                'user_ruangan_name' => 'RS Central (Admin)',
            ]);
            $msg = 'Berhasil masuk sebagai Administrator System SIAKER ERP.';
        } elseif ($role === 'tata_usaha') {
            session([
                'is_admin' => false,
                'user_role' => 'Tata Usaha RS',
                'user_ruangan_id' => 0,
                'user_ruangan_name' => 'Tata Usaha / Direksi RS',
            ]);
            $msg = 'Berhasil masuk sebagai Tata Usaha RS (Read-Only).';
        } else {
            $ruangan = Ruangan::find($ruanganId);
            $namaRuangan = $ruangan->nama_ruangan ?? 'Ruangan RS';
            session([
                'is_admin' => false,
                'user_role' => 'Petugas Ruangan',
                'user_ruangan_id' => $ruanganId,
                'user_ruangan_name' => $namaRuangan,
            ]);
            $msg = "Berhasil masuk sebagai Petugas Ruangan {$namaRuangan}.";
        }

        return redirect()->route('dashboard')->with('success', $msg);
    }

    public function logout()
    {
        session()->forget(['is_admin', 'user_role', 'user_ruangan_id', 'user_ruangan_name']);
        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem SIAKER ERP.');
    }
}
