@extends('layout.master')

<!-- @section('title', 'Daftar Pengguna - Admin Panel') -->

@section('konten')

<!-- Header Halaman Admin -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
            <i class="fas fa-users text-warning mr-2"></i>Daftar Pengguna (Admin View)
        </h1>
        <p class="text-muted mb-0 small">Halaman informasi pengguna sistem untuk Admin (Akses Read-Only / Terbatas).</p>
    </div>
    <span class="badge badge-warning text-dark px-3 py-2 text-uppercase shadow-sm">
        <i class="fas fa-user-shield mr-1"></i> Area Admin
    </span>
</div>

<!-- Alert Notifikasi -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
    <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<!-- Row Kartu Statistik Ringkas -->
<div class="row">
    <!-- Total Semua User -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Pengguna Terdaftar</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers }} Akun</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Admin</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAdmin }} Akun</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Regular User -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Pengguna Biasa</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalRegularUser }} Akun</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection
