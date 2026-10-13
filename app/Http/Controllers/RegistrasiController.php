<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegistrasiController extends Controller
{
    public function index()
    {
        return view('login.register', [
            'title' => 'Registrasi',
        ]);
    }

    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|max:255|confirmed',
        ],
        [
            'email.unique' => 'Email sudah terdaftar',
            'first_name.required' => 'Nama depan wajib diisi',
            'last_name.required' => 'Nama belakang wajib diisi',
            'email.required' => 'Email wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]
    );

        // Gabungkan nama depan dan nama belakang
        $fullname = trim(
            $request->first_name . ' ' . $request->last_name
        );

        // Simpan data ke database
        User::create([
            'name' => $fullname,
            'email' => $validatedData['email'],
            'password' =>Hash::make ( $validatedData['password']),
        ],
    [   'email.unique' => 'Email sudah terdaftar',
        'password.confirmed' => 'Password tidak cocok',
    ]);

        // Kembali ke halaman login
        return redirect('/login')
            ->with('success', 'Registrasi berhasil! Silahkan login.');
    }
}