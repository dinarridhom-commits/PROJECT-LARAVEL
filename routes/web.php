<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegistrasiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActivityLogController;

/* ============================================================
   1. ROUTE PUBLIK / TAMU
   Halaman yang bisa diakses oleh siapa saja tanpa harus login:
   - Melihat peta WebGIS, menyaring data, dan unduh data tabel
   - Halaman About & Contact
   ============================================================ */
Route::get('/', [DashboardController::class, 'peta'])->name('dashboard');
Route::get('/home', [DashboardController::class, 'peta'])->name('home');
Route::get('/peta', [DashboardController::class, 'peta'])->name('peta');

// Export & Unduh Data Spasial GIS (File Tunggal & Arsip ZIP per Kategori)
Route::get('/peta/download-category/{category?}', [DashboardController::class, 'downloadCategory'])->name('peta.download-category');
Route::get('/peta/download-file/{filename}', [DashboardController::class, 'downloadFile'])->name('peta.download-file');

Route::get('/contact', function () { return view('contact'); })->name('contact');
Route::get('/about', function () { return view('About'); })->name('about');

/* ============================================================
   2. AUTHENTICATION (LOGIN & REGISTER)
   Hanya untuk guest (belum login)
   ============================================================ */
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate']);
    Route::get('/register', [RegistrasiController::class, 'index'])->name('register');
    Route::post('/register', [RegistrasiController::class, 'store']);
});

/* ============================================================
   3. ROUTE LOGOUT (Hanya yang sudah login)
   ============================================================ */
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/* ============================================================
   4. ROUTE YANG MEMERLUKAN LOGIN (AUTH)
   ============================================================ */
Route::middleware('auth')->group(function () {

    /* --- HAK AKSES: ADMIN & SUPER ADMIN ---
       Memiliki akses untuk upload GeoJSON, hapus file GIS, dan edit data */
    Route::middleware('role:superadmin,admin')->group(function () {
        // Operasi GIS
        Route::post('/peta/upload-geojson', [DashboardController::class, 'uploadGeojson'])->name('peta.upload-geojson');
        Route::delete('/peta/delete-geojson', [DashboardController::class, 'deleteGeojson'])->name('peta.delete-geojson');
        Route::delete('/peta/delete-geojson-bulk', [DashboardController::class, 'deleteGeojsonBulk'])->name('peta.delete-geojson-bulk');

        // Master Data Produk & Kategori
        Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
        Route::get('/produk/create', [ProdukController::class, 'create'])->name('produk.create');
        Route::post('/produk', [ProdukController::class, 'store'])->name('produk.store');
        Route::get('/produk/{id}', [ProdukController::class, 'show'])->name('produk.show');
        Route::get('/produk/{id}/edit', [ProdukController::class, 'edit'])->name('produk.edit');
        Route::put('/produk/{id}', [ProdukController::class, 'update'])->name('produk.update');
        Route::delete('/produk/{id}', [ProdukController::class, 'destroy'])->name('produk.destroy');
        Route::resource('kategori', KategoriController::class);

        // Akses Utama Admin Panel
        Route::get('/admin', function() {
            return redirect()->route('users.index');
        })->name('admin.panel');

        // Melihat daftar pengguna
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
    });

    /* --- HAK AKSES KHUSUS: SUPER ADMIN ---
       Mengatur manajemen pengguna dan log aktivitas sistem */
    Route::middleware('role:superadmin')->group(function () {
        // Ubah role & hapus akun pengguna
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Log Aktivitas Sistem
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::delete('/activity-logs/clear', [ActivityLogController::class, 'clear'])->name('activity-logs.clear');
    });

});
