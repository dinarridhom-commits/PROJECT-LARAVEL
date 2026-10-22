<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua pengguna
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $usersQuery = User::query();

        if ($search) {
            $usersQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
            });
        }

        $users = $usersQuery->latest()->get();

        $totalUsers      = User::count();
        $totalSuperadmin = User::where('role', 'superadmin')->count();
        $totalAdmin      = User::where('role', 'admin')->count();
        $totalRegularUser = User::where('role', 'user')->count();

        $data = compact(
            'users', 'search',
            'totalUsers', 'totalSuperadmin',
            'totalAdmin', 'totalRegularUser'
        );

        // Arahkan ke view sesuai role yang sedang login
        if (Auth::user()->role === 'superadmin') {
            return view('superadmin.index', $data);
        }

        return view('user.admin', $data);
    }

    /**
     * Membuat akun staf/admin baru secara langsung oleh Super Admin
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|string|email|max:255|unique:users,email',
            'password'              => 'required|string|min:6|confirmed',
            'role'                  => 'required|in:superadmin,admin,user',
        ], [
            'name.required'         => 'Nama pengguna wajib diisi.',
            'name.max'              => 'Nama pengguna maksimal 255 karakter.',
            'email.required'        => 'Alamat email wajib diisi.',
            'email.email'           => 'Format email tidak valid.',
            'email.unique'          => 'Alamat email tersebut sudah terdaftar.',
            'password.required'     => 'Password wajib diisi.',
            'password.min'          => 'Password minimal 6 karakter.',
            'password.confirmed'    => 'Konfirmasi password tidak cocok.',
            'role.required'         => 'Role pengguna wajib dipilih.',
            'role.in'               => 'Role yang dipilih tidak valid.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        $roleLabels = [
            'superadmin' => 'Super Admin',
            'admin'      => 'Admin / Staf GIS',
            'user'       => 'User / Staf Biasa',
        ];
        $roleLabel = $roleLabels[$user->role] ?? $user->role;

        ActivityLog::record(
            'CREATE_USER',
            "Super Admin membuat akun pengguna baru: '{$user->name}' ({$user->email}) dengan Role: {$roleLabel}"
        );

        return redirect()
            ->route('users.index')
            ->with('success', "Akun '{$user->name}' ({$roleLabel}) berhasil dibuat.");
    }

    /**
     * Memperbarui nama, role, dan (opsional) password pengguna
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'                  => 'required|string|max:255',
            'role'                  => 'required|in:superadmin,admin,user',
            'password'              => 'nullable|string|min:6|confirmed',
        ], [
            'name.required'         => 'Nama pengguna wajib diisi.',
            'role.required'         => 'Role wajib dipilih.',
            'role.in'               => 'Role yang dipilih tidak valid.',
            'password.min'          => 'Password baru minimal 6 karakter.',
            'password.confirmed'    => 'Konfirmasi password baru tidak cocok.',
        ]);

        // Super Admin tidak boleh mengubah role dirinya sendiri
        if (Auth::id() === $user->id && $request->role !== 'superadmin') {
            return back()->with(
                'error',
                'Anda tidak dapat mengubah role akun Super Admin milik Anda sendiri.'
            );
        }

        $oldRole = $user->role;
        $updateData = [
            'name' => $request->name,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        $logDesc = "Memperbarui data user '{$user->name}' (Role: {$oldRole} -> {$request->role})";
        if ($request->filled('password')) {
            $logDesc .= " beserta reset password";
        }

        ActivityLog::record('UPDATE_USER', $logDesc);

        return redirect()
            ->route('users.index')
            ->with(
                'success',
                "Data user '{$user->name}' berhasil diperbarui."
            );
    }

    /**
     * Menghapus pengguna
     */
    public function destroy(User $user)
    {

        // Jangan izinkan superadmin menghapus akunnya sendiri
        if (Auth::id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $userName = $user->name;
        $userRole = $user->role;
        $user->delete();

        ActivityLog::record('DELETE_USER', "Menghapus akun user '{$userName}' (Role: {$userRole})");

        return redirect()->route('users.index')->with('success', "User '{$userName}' berhasil dihapus.");
    }
}
