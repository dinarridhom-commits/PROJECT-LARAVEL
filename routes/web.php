<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegistrasiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\GisLayerController;
use App\Http\Controllers\NotificationController;

/* ============================================================
   1. ROUTE UTAMA (Publik & Auth)
   Satu route tunggal untuk /, /home, /peta.
   Controller petaRouter() yang menentukan view berdasarkan status login:
   - Guest (belum login) → peta-publik.blade.php
   - Auth (sudah login)  → peta.blade.php
   ============================================================ */

Route::get('/', [DashboardController::class, 'petaRouter'])->name('home');
Route::get('/peta', [DashboardController::class, 'petaRouter'])->name('peta');

Route::get('/contact', function () { return view('contact'); })->name('contact');
Route::get('/about', function () { return view('About'); })->name('about');

/* ============================================================
   2. AUTHENTICATION (LOGIN)
   Hanya untuk guest (belum login)
   ============================================================ */
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate']);
    Route::get('/refresh-captcha', [LoginController::class, 'refreshCaptcha'])->name('captcha.refresh');
});

// Registrasi mandiri dinonaktifkan (dialihkan ke halaman login)
Route::get('/register', function () {
    return redirect()->route('login')->with('info', 'Registrasi mandiri dinonaktifkan. Pembuatan akun staf/admin dilakukan langsung oleh Super Admin melalui menu Manajemen User.');
});
Route::post('/register', function () {
    return redirect()->route('login')->with('loginError', 'Registrasi mandiri dinonaktifkan.');
});

/* ============================================================
   3. ROUTE LOGOUT (Hanya yang sudah login)
   ============================================================ */
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/* ============================================================
   4. ROUTE YANG MEMERLUKAN LOGIN (AUTH)
   ============================================================ */
Route::middleware('auth')->group(function () {

    // Dashboard Utama: Grafik & Analisis Data Website
    Route::get('/dashboard', [DashboardController::class, 'analyticsDashboard'])->name('dashboard');

    // Edit Profil Pengguna (Nama, Email, Password) - untuk semua pengguna yang login
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');

    // Notifikasi In-App
    Route::get('/notifications', [NotificationController::class, 'getNotifications'])->name('notifications.get');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    /* --- HAK AKSES: ADMIN & SUPER ADMIN ---
       Memiliki akses untuk upload GeoJSON, hapus file GIS, download data, dan edit data */
    Route::middleware('role:superadmin,admin')->group(function () {
        // Operasi GIS & Unduh Data Spasial
        Route::get('/peta/download-category/{category?}', [DashboardController::class, 'downloadCategory'])->name('peta.download-category');
        Route::get('/peta/download-file/{filename}', [DashboardController::class, 'downloadFile'])->name('peta.download-file');
        Route::post('/peta/upload-geojson', [DashboardController::class, 'uploadGeojson'])->name('peta.upload-geojson');
        Route::delete('/peta/delete-geojson', [DashboardController::class, 'deleteGeojson'])->name('peta.delete-geojson');
        Route::delete('/peta/delete-geojson-bulk', [DashboardController::class, 'deleteGeojsonBulk'])->name('peta.delete-geojson-bulk');

        // Akses Utama Admin Panel (dialihkan ke Dashboard Analisis)
        Route::get('/admin', function() {
            return redirect()->route('dashboard');
        })->name('admin.panel');

        // Melihat daftar pengguna
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
    });

    /* --- HAK AKSES KHUSUS: SUPER ADMIN ---
       Mengatur manajemen pengguna, verifikasi file GIS, tong sampah, dan log aktivitas sistem */
    Route::middleware('role:superadmin')->group(function () {
        // Tong Sampah & Pemulihan Akun Pengguna (Soft Delete User)
        Route::get('/users/trash', [UserController::class, 'trash'])->name('users.trash');
        Route::put('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
        Route::delete('/users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.forceDelete');

        // Pembuatan akun staf/admin, ubah role/password, & hapus akun pengguna
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Verifikasi File GIS Unggahan Admin (Approval Workflow)
        Route::get('/gis/verification', [GisLayerController::class, 'verificationIndex'])->name('gis.verification');
        Route::post('/gis/verification/{id}/approve', [GisLayerController::class, 'approve'])->name('gis.approve');
        Route::post('/gis/verification/{id}/reject', [GisLayerController::class, 'reject'])->name('gis.reject');

        // Tong Sampah & Pemulihan Berkas GIS (Soft Delete File GIS)
        Route::get('/gis/trash', [GisLayerController::class, 'trash'])->name('gis.trash');
        Route::put('/gis/trash/{id}/restore', [GisLayerController::class, 'restore'])->name('gis.restore');
        Route::delete('/gis/trash/{id}/force-delete', [GisLayerController::class, 'forceDelete'])->name('gis.forceDelete');

        // Log Aktivitas Sistem
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::delete('/activity-logs/clear', [ActivityLogController::class, 'clear'])->name('activity-logs.clear');
    });

});
