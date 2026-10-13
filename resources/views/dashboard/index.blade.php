@extends('layout.master')

@section('title', $title ?? 'Dashboard')

@section('konten')

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Dashboard Overview</h1>
        <p class="text-muted mb-0 small">Ringkasan data tabel produk dan tabel kategori toko Anda.</p>
    </div>
</div>

@if(session('message'))
<div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
    <i class="fas fa-check-circle mr-2"></i> {{ session('message') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif


<!-- Content Row - Stat Cards -->
<div class="row">

    <!-- Total Produk Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Produk</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalProduk }} Item</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-boxes fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Kategori Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Kategori</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalKategori }} Kategori</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tags fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Stok Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Stok Produk</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalStok, 0, ',', '.') }} Unit</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-cubes fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Nilai Inventaris Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Estimasi Nilai Stok</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalNilaiInventaris, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-coins fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="card shadow mb-4">
    <div class="card-body py-3">
        <form action="{{ url('/dashboard') }}" method="GET" class="form-inline justify-content-between">
            <div class="font-weight-bold text-gray-800 mb-2 mb-md-0">
                <i class="fas fa-filter text-primary mr-1"></i> Filter Data Dashboard
            </div>
            <div class="input-group w-100 w-md-auto" style="max-width: 450px;">
                <input type="text" name="search" class="form-control bg-light border-0 small" placeholder="Cari nama produk, kode, atau kategori..." value="{{ $search ?? '' }}">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search fa-sm"></i> Cari
                    </button>
                    @if(!empty($search))
                    <a href="{{ url('/dashboard') }}" class="btn btn-secondary" title="Reset pencarian">
                        <i class="fas fa-undo fa-sm"></i> Reset
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabel Produk Dashboard (Read Only) -->
@include('dashboard.produk')

<!-- Tabel Kategori Dashboard (Read Only) -->
@include('dashboard.kategori')

@endsection
