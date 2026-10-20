<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        // Membuat kode CAPTCHA
        $captcha = strtoupper(
            substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 5)
        );

        // Simpan CAPTCHA ke session
        session(['captcha_code' => $captcha]);

        return view('login.index', [
            'title' => 'Login',
            'captcha' => $captcha,
        ]);
    }

    public function authenticate(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required',
            'password' => 'required',
            'captcha' => 'required',
        ], [
            'email.required' => 'Username atau Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'captcha.required' => 'Kode CAPTCHA wajib diisi.',
        ]);

        // Cek CAPTCHA
        if (strtoupper($request->captcha) !== session('captcha_code')) {
            return back()
                ->withInput()
                ->with('loginError', 'Kode CAPTCHA salah.');
        }

        // Hapus CAPTCHA dari session setelah berhasil dicek
        session()->forget('captcha_code');

        // Dukung login dengan Username (name) atau Email
        $loginInput = $request->input('email');
        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        $credentials = [
            $field => $loginInput,
            'password' => $request->password,
        ];

        // Proses login (dengan remember me jika dicentang)
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            ActivityLog::record('LOGIN', 'Pengguna berhasil login ke sistem WebGIS.');

            return redirect()->intended('/peta');
        }

        // Jika username/email atau password salah
        return back()
            ->withInput()
            ->with('loginError', 'Login gagal! Periksa kembali username dan password Anda.');
    }
    public function logout(Request $request)
    {
        if (Auth::check()) {
            ActivityLog::record('LOGOUT', 'Pengguna keluar dari sistem WebGIS.');
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/peta')->with(
            'success',
            'Anda telah berhasil logout. Saat ini Anda berada dalam Mode Publik / Tamu.'
        );
    }
}