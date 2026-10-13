<?php

namespace App\Http\Controllers;

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
        'email' => 'required|email',
        'password' => 'required',
        'captcha' => 'required',
    ], [
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
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

    // Hanya ambil email dan password untuk Auth
    $credentials = $request->only('email', 'password');

    // Proses login
    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }

    // Jika email/password salah
    return back()
        ->withInput()
        ->with('loginError', 'Login gagal! Periksa kembali email dan password Anda.');
 }
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with(
            'success',
            'Anda telah berhasil logout.'
        );
    }
}