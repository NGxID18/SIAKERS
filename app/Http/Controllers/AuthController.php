<?php

namespace App\Http\Controllers;

use App\Models\Seksi;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        $seksiList = Seksi::all();
        return view('auth.login', compact('seksiList'));
    }

    public function login(Request $request)
    {
        $role = $request->input('role', 'seksi');
        $seksiId = (int) $request->input('seksi_id', 1);

        if ($role === 'admin') {
            session([
                'is_admin' => true,
                'user_role' => 'admin',
                'user_seksi_id' => 0,
            ]);
            $msg = 'Berhasil masuk sebagai Administrator System (Akses Penuh).';
        } elseif ($role === 'tata_usaha') {
            session([
                'is_admin' => false,
                'user_role' => 'tata_usaha',
                'user_seksi_id' => 0,
            ]);
            $msg = 'Berhasil masuk sebagai Tata Usaha (Pengawasan Read-Only).';
        } else {
            $seksi = Seksi::find($seksiId);
            $namaSeksi = $seksi->nama_seksi ?? 'Seksi Operasional';
            session([
                'is_admin' => false,
                'user_role' => 'seksi',
                'user_seksi_id' => $seksiId,
            ]);
            $msg = "Berhasil masuk sebagai Pengguna {$namaSeksi}.";
        }

        return redirect()->route('dashboard')->with('success', $msg);
    }

    public function logout()
    {
        session()->forget(['is_admin', 'user_role', 'user_seksi_id']);
        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem SIAKER.');
    }
}
