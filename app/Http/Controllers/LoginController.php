<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function index()
    {
        return view('login.index', [
            'title' => 'Login',
        ]);
    }

    /**
     * Refresh kode CAPTCHA via AJAX (fallback/backward-compatibility)
     */
    public function refreshCaptcha()
    {
        return response()->json([
            'status'  => 'success',
            'message' => 'Sistem telah menggunakan Google reCAPTCHA v3 otomatis.',
        ]);
    }

    public function authenticate(Request $request)
    {
        // Validasi input
        $request->validate([
            'email'    => 'required',
            'password' => 'required',
        ], [
            'email.required'    => 'Username atau Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Verifikasi Google reCAPTCHA v3 jika secret key dikonfigurasi
        $recaptchaSecret = config('services.recaptcha.secret_key');
        if (!empty($recaptchaSecret)) {
            $recaptchaToken = $request->input('g-recaptcha-response');
            if (empty($recaptchaToken)) {
                return back()
                    ->withInput()
                    ->with('loginError', 'Verifikasi Google reCAPTCHA v3 gagal (token kosong). Silakan refresh halaman.');
            }

            try {
                $response = Http::asForm()->timeout(6)->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret'   => $recaptchaSecret,
                    'response' => $recaptchaToken,
                    'remoteip' => $request->ip(),
                ]);

                $resData = $response->json();

                // Evaluasi score reCAPTCHA v3 (threshold standar 0.5)
                if (!($resData['success'] ?? false) || ($resData['score'] ?? 0) < 0.5) {
                    Log::warning('reCAPTCHA v3 verification failed', [
                        'ip'    => $request->ip(),
                        'email' => $request->input('email'),
                        'res'   => $resData,
                    ]);

                    return back()
                        ->withInput()
                        ->with('loginError', 'Sistem keamanan reCAPTCHA mendeteksi aktivitas mencurigakan. Silakan coba lagi.');
                }
            } catch (\Exception $e) {
                Log::error('reCAPTCHA v3 connection error: ' . $e->getMessage());
                return back()
                    ->withInput()
                    ->with('loginError', 'Gagal menghubungi server verifikasi Google reCAPTCHA. Periksa koneksi internet Anda.');
            }
        }

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