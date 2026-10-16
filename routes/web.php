<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegistrasiController;
use App\Http\Controllers\UserController;


    Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate']);
    Route::get('/register', [RegistrasiController::class, 'index'])->name('register');
    Route::post('/register', [RegistrasiController::class, 'store']);
  
});

// Route Logout (Hanya untuk yang sudah login)

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Route Halaman Publik (Opsional)
    Route::get('/contact', function () {
    return view('contact');
});
    Route::get('/about', function () {
    return view('About');
});

//  Route yang Memerlukan Login (Auth Middleware)
    Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'peta'])->name('dashboard');
    Route::get('/home', [DashboardController::class, 'peta'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'peta']);
    Route::get('/peta', [DashboardController::class, 'peta'])->name('peta');
    Route::post('/peta/upload-geojson', [DashboardController::class, 'uploadGeojson'])->name('peta.upload-geojson');
    Route::delete('/peta/delete-geojson', [DashboardController::class, 'deleteGeojson'])->name('peta.delete-geojson');
    Route::delete('/peta/delete-geojson-bulk', [DashboardController::class, 'deleteGeojsonBulk'])->name('peta.delete-geojson-bulk');

    Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
    Route::get('/produk/create', [ProdukController::class, 'create'])->name('produk.create');
    Route::post('/produk', [ProdukController::class, 'store'])->name('produk.store');
    Route::get('/produk/{id}', [ProdukController::class, 'show'])->name('produk.show');
    Route::get('/produk/{id}/edit', [ProdukController::class, 'edit'])->name('produk.edit');
    Route::put('/produk/{id}', [ProdukController::class, 'update'])->name('produk.update');
    Route::delete('/produk/{id}', [ProdukController::class, 'destroy'])->name('produk.destroy');
    Route::resource('kategori', KategoriController::class);

    // Route Manajemen User
    // GET /users: bisa diakses superadmin DAN admin (tampilan berbeda)
    Route::middleware('role:superadmin,admin')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    });

    // Update & Delete: hanya superadmin
    Route::middleware('role:superadmin')->group(function () {
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});

