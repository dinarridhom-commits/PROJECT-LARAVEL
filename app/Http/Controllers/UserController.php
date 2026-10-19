<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * Memperbarui nama dan role pengguna
     */
   public function update(Request $request, User $user)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'role' => 'required|in:superadmin,admin,user',
    ], [
        'name.required' => 'Nama pengguna wajib diisi.',
        'role.required' => 'Role wajib dipilih.',
        'role.in' => 'Role yang dipilih tidak valid.',
    ]);

    // Super Admin tidak boleh mengubah role dirinya sendiri
    if (Auth::id() === $user->id && $request->role !== 'superadmin') {
        return back()->with(
            'error',
            'Anda tidak dapat mengubah role akun Super Admin milik Anda sendiri.'
        );
    }

    $oldRole = $user->role;
    $user->update([
        'name' => $request->name,
        'role' => $request->role,
    ]);

    ActivityLog::record('UPDATE_USER', "Memperbarui data user '{$user->name}' (Role: {$oldRole} -> {$request->role})");

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
