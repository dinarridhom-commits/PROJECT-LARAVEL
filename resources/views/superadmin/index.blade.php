@extends('layout.master')

@section('title', 'Manajemen Pengguna - Super Admin')

@section('konten')

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 font-weight-bold text-gray-800">
            <i class="fas fa-users-cog text-danger mr-2"></i>
            Manajemen User
        </h1>
        <p class="text-muted small mb-0">
            Pembuatan akun staf/admin langsung oleh Super Admin dan pengelolaan hak akses.
        </p>
    </div>

    <div class="d-flex align-items-center">
        <button type="button" class="btn btn-primary shadow-sm mr-2" data-toggle="modal" data-target="#modalCreateUser">
            <i class="fas fa-user-plus mr-1"></i> Tambah Pengguna
        </button>
        <span class="badge badge-danger px-3 py-2">
            <i class="fas fa-crown mr-1"></i>
            Super Admin
        </span>
    </div>
</div>


<!-- Notifikasi -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle mr-2"></i>
        {{ session('success') }}

        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        {{ session('error') }}

        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
@endif


@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">

        <strong>
            <i class="fas fa-exclamation-circle mr-2"></i>
            Terjadi kesalahan:
        </strong>

        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>

        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>

    </div>
@endif


<!-- Statistik -->
@php
    $stats = [
        ['Total Pengguna', $totalUsers, 'primary', 'fa-users'],
        ['Super Admin', $totalSuperadmin, 'danger', 'fa-crown'],
        ['Admin', $totalAdmin, 'warning', 'fa-user-shield'],
        ['User Biasa', $totalRegularUser, 'info', 'fa-user'],
    ];
@endphp

<div class="row">

    @foreach($stats as $stat)

        <div class="col-xl-3 col-md-6 mb-4">

            <div class="card border-left-{{ $stat[2] }} shadow h-100 py-2">

                <div class="card-body">

                    <div class="row no-gutters align-items-center">

                        <div class="col mr-2">

                            <div class="text-xs font-weight-bold
                                        text-{{ $stat[2] }}
                                        text-uppercase mb-1">

                                {{ $stat[0] }}

                            </div>

                            <div class="h5 mb-0 font-weight-bold text-gray-800">

                                {{ $stat[1] }} Akun

                            </div>

                        </div>

                        <div class="col-auto">

                            <i class="fas {{ $stat[3] }}
                                      fa-2x text-gray-300"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    @endforeach

</div>


<!-- Tabel Pengguna -->
<div class="card shadow mb-4">

    <!-- Header Tabel -->
    <div class="card-header py-3 d-flex flex-wrap justify-content-between align-items-center">

        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-list mr-1"></i>
            Data Seluruh Pengguna
        </h6>

        <div class="d-flex align-items-center mt-2 mt-sm-0">
            <!-- Search -->
            <form action="{{ route('users.index') }}" method="GET" class="mr-2">
                <div class="input-group">
                    <input type="text"
                           name="search"
                           class="form-control form-control-sm"
                           placeholder="Cari nama, email, role..."
                           value="{{ $search ?? '' }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary btn-sm" type="submit" title="Cari">
                            <i class="fas fa-search"></i>
                        </button>
                        @if(!empty($search))
                            <a href="{{ route('users.index') }}"
                               class="btn btn-secondary btn-sm" title="Reset Pencarian">
                                <i class="fas fa-undo"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            <button type="button" class="btn btn-success btn-sm font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalCreateUser">
                <i class="fas fa-plus mr-1"></i> Buat Akun
            </button>
        </div>

    </div>


    <!-- Isi Tabel -->
    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered table-hover">

                <thead class="thead-light">

                    <tr>

                        <th class="text-center">No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th class="text-center">Role</th>
                        <th class="text-center">Terdaftar</th>
                        <th class="text-center">Aksi</th>

                    </tr>

                </thead>


                <tbody>

                @forelse($users as $index => $u)

                    <tr>

                        <td class="text-center">
                            {{ $index + 1 }}
                        </td>


                        <!-- Nama -->
                        <td class="font-weight-bold">

                            <i class="fas fa-user-circle
                                      text-gray-400 mr-2"></i>

                            {{ $u->name }}

                            @if(Auth::id() == $u->id)

                                <span class="badge badge-info">
                                    Akun Anda
                                </span>

                            @endif

                        </td>


                        <!-- Email -->
                        <td>
                            {{ $u->email }}
                        </td>


                        <!-- Role -->
                        <td class="text-center">

                            @if($u->role == 'superadmin')

                                <span class="badge badge-danger">
                                    <i class="fas fa-crown"></i>
                                    Super Admin
                                </span>

                            @elseif($u->role == 'admin')

                                <span class="badge badge-warning">
                                    <i class="fas fa-user-shield"></i>
                                    Admin
                                </span>

                            @else

                                <span class="badge badge-secondary">
                                    <i class="fas fa-user"></i>
                                    User
                                </span>

                            @endif

                        </td>


                        <!-- Tanggal -->
                        <td class="text-center">

                            <small class="text-muted">
                                {{ $u->created_at
                                    ? $u->created_at->format('d M Y')
                                    : '-' }}
                            </small>

                        </td>


                        <!-- Aksi -->
                        <td class="text-center">

                            <!-- Edit -->
                            <button type="button"
                                    class="btn btn-info btn-sm"
                                    data-toggle="modal"
                                    data-target="#editUser{{ $u->id }}">

                                <i class="fas fa-edit"></i>
                                Edit

                            </button>


                            <!-- Hapus -->
                            @if(Auth::id() != $u->id)

                                <button type="button"
                                        class="btn btn-danger btn-sm"
                                        data-toggle="modal"
                                        data-target="#deleteUser{{ $u->id }}">

                                    <i class="fas fa-trash"></i>
                                    Hapus

                                </button>

                            @else

                                <button class="btn btn-secondary btn-sm"
                                        disabled>

                                    <i class="fas fa-trash"></i>
                                    Hapus

                                </button>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="6"
                            class="text-center text-muted py-4">

                            <i class="fas fa-users-slash fa-2x d-block mb-2"></i>

                            Tidak ada data pengguna.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


<!-- MODAL -->
@include('superadmin.modals')

@endsection