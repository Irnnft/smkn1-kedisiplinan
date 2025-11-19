<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * 1. Menampilkan halaman formulir login.
     * (Menangani: GET / )
     */
    public function showLoginForm(): View
    {
        // 'auth.login' adalah file view yang akan kita buat
        // di resources/views/auth/login.blade.php
        return view('auth.login');
    }

    /**
     * 2. Memproses upaya login.
     * (Menangani: POST / )
     */
    public function login(Request $request): RedirectResponse
    {
        // --- Validasi Input ---
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Cek apakah user mencentang "Ingat Saya"
        $remember = $request->has('remember');

        // --- Coba Login ---
        // Kita login menggunakan 'username', BUKAN 'email'
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password], $remember)) {
            
            // --- BERHASIL LOGIN ---
            
            // 1. Regenerasi session untuk keamanan
            $request->session()->regenerate();

            // 2. Ambil data user yang login
            $user = Auth::user();

            // 3. LOGIKA PENGALIHAN (REDIRECT) BERDASARKAN PERAN
            // Ini menerjemahkan Use Case Diagram kita menjadi kode
            
            $role = $user->role->nama_role;

            if ($role == 'Waka Kesiswaan' || $role == 'Operator Sekolah') {
                // Waka & Operator adalah admin, kita arahkan ke dashboard admin
                return redirect()->intended('/dashboard/admin');
            
            } elseif ($role == 'Kepala Sekolah') {
                return redirect()->intended('/dashboard/kepsek');
            
            } elseif ($role == 'Kaprodi') {
                return redirect()->intended('/dashboard/kaprodi');
            
            } elseif ($role == 'Wali Kelas') {
                return redirect()->intended('/dashboard/walikelas');
            
            } elseif ($role == 'Guru') {
                // Guru tidak punya dashboard, kita arahkan ke halaman utamanya
                return redirect()->intended('/pelanggaran/catat');
            
            } elseif ($role == 'Orang Tua') {
                return redirect()->intended('/dashboard/ortu');
            
            } else {
                // Jika rolenya tidak dikenal, logout saja
                Auth::logout();
                return redirect('/')->withErrors(['username' => 'Role tidak valid.']);
            }

        }

        // --- GAGAL LOGIN ---
        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username'); // Kembalikan ke form dengan data username
    }

    /**
     * 3. Memproses logout.
     * (Menangani: POST /logout )
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Kembali ke halaman login
        return redirect('/');
    }
}