@extends('layout.master')

@section('title', 'Profil Pengguna & Keamanan Akun — WebGIS BWS Kalimantan I')

@section('konten')
<div class="container-fluid">

    {{-- ============================================================ --}}
    {{-- HEADER HALAMAN & TOMBOL NAVIGASI CEPAT --}}
    {{-- ============================================================ --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap" style="gap: 12px;">
        <div>
            <h1 class="h3 font-weight-bold text-gray-800 mb-1" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                <i class="fas fa-user-circle text-primary mr-2"></i>
                Profil Pengguna &amp; Keamanan Akun
            </h1>
            <p class="text-muted small mb-0" style="font-family: 'Inter', sans-serif;">
                Kelola identitas akun kedinasan, kontak, dan kata sandi Anda di sistem WebGIS BWS Kalimantan I.
            </p>
        </div>

        <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
            <a href="{{ route('dashboard') }}" class="bws-btn-top bws-btn-top-map" title="Kembali ke Dashboard Analitik">
                <i class="fas fa-th-large mr-2 text-primary"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('peta') }}" class="bws-btn-top bws-btn-top-map" title="Buka tampilan peta interaktif" target="_blank">
                <i class="fas fa-map-marked-alt mr-2 text-primary"></i>
                <span>Buka Peta</span>
            </a>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- FLASH NOTIFIKASI --}}
    {{-- ============================================================ --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" style="border-radius: 10px; background: #ecfdf5; color: #065f46; border-left: 4px solid #10b981 !important;">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle fa-lg mr-2.5 text-success"></i>
                <div class="font-weight-medium" style="font-size: 13.5px;">{{ session('success') }}</div>
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" style="border-radius: 10px; background: #fef2f2; color: #991b1b; border-left: 4px solid #ef4444 !important;">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle fa-lg mr-2.5 text-danger"></i>
                <div class="font-weight-medium" style="font-size: 13.5px;">{{ session('error') }}</div>
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" style="border-radius: 10px; background: #fef2f2; color: #991b1b; border-left: 4px solid #ef4444 !important;">
            <div class="d-flex align-items-center mb-1">
                <i class="fas fa-exclamation-circle fa-lg mr-2 text-danger"></i>
                <strong>Terjadi kesalahan input:</strong>
            </div>
            <ul class="mb-0 pl-4 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- KONTEN UTAMA 2 KOLOM (EXECUTIVE PROFILE PAGE) --}}
    {{-- ============================================================ --}}
    <div class="row">

        {{-- KOLOM KIRI (KARTU IDENTITAS PEGAWAI & INFORMASI AKUN) --}}
        <div class="col-lg-4 mb-4">

            {{-- Kartu Profil Utama --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 14px; overflow: hidden;">
                {{-- Banner Atas Gradasi Biru PUPR --}}
                <div class="profile-hero-banner position-relative">
                    <img src="{{ asset('sbadmin/img/logo-pu.jpeg') }}" alt="Logo PU" class="profile-banner-pu-logo">
                </div>

                <div class="card-body text-center pt-0 pb-4 px-4 position-relative">
                    {{-- Avatar Lingkaran Besar --}}
                    <div class="profile-avatar-wrapper mb-3">
                        <div class="profile-avatar-circle">
                            <i class="fas fa-user-tie"></i>
                        </div>
                    </div>

                    <h4 class="font-weight-bold text-dark mb-1" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 18px;">
                        {{ $user->name }}
                    </h4>
                    <p class="text-muted small mb-2" style="font-family: 'Inter', sans-serif;">
                        <i class="far fa-envelope mr-1 text-gray-400"></i>{{ $user->email }}
                    </p>

                    {{-- Role Soft Badge --}}
                    <div class="mb-3">
                        @if($user->role === 'superadmin')
                            <span class="user-role-badge user-role-super">
                                <i class="fas fa-crown mr-1"></i>Super Admin
                            </span>
                        @elseif($user->role === 'admin')
                            <span class="user-role-badge user-role-admin">
                                <i class="fas fa-user-shield mr-1"></i>Admin / Staf GIS
                            </span>
                        @else
                            <span class="user-role-badge user-role-default">
                                <i class="fas fa-user mr-1"></i>Pengguna
                            </span>
                        @endif
                    </div>

                    <div class="profile-info-divider my-3"></div>

                    {{-- Rincian Instansi & Status Akun --}}
                    <div class="text-left profile-meta-list" style="font-size: 12.5px;">
                        <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                            <span class="text-muted"><i class="fas fa-building text-gray-400 mr-2" style="width: 14px;"></i>Unit Kerja</span>
                            <span class="font-weight-bold text-dark text-right">BWS Kalimantan I</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                            <span class="text-muted"><i class="fas fa-landmark text-gray-400 mr-2" style="width: 14px;"></i>Kementerian</span>
                            <span class="font-weight-semibold text-dark text-right">Pekerjaan Umum</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                            <span class="text-muted"><i class="fas fa-calendar-check text-gray-400 mr-2" style="width: 14px;"></i>Terdaftar Sejak</span>
                            <span class="font-weight-semibold text-dark text-right">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between py-2">
                            <span class="text-muted"><i class="fas fa-shield-alt text-gray-400 mr-2" style="width: 14px;"></i>Status Akun</span>
                            <span class="badge badge-success px-2 py-0.5" style="border-radius: 10px; font-size: 11px;">
                                <i class="fas fa-circle text-white mr-1" style="font-size: 6px; vertical-align: middle;"></i>Aktif &amp; Terverifikasi
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kartu Riwayat Aktivitas Akun --}}
            <div class="card shadow-sm border-0" style="border-radius: 14px;">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-dark" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 13.5px;">
                        <i class="fas fa-history text-primary mr-2"></i>Aktivitas Akun Terakhir
                    </h6>
                    <span class="badge badge-light border text-muted" style="font-size: 11px;">
                        {{ count($recentUserLogs) }} Riwayat
                    </span>
                </div>
                <div class="card-body p-3">
                    @forelse($recentUserLogs as $l)
                        <div class="d-flex align-items-start py-2 border-bottom" style="font-size: 12px;">
                            <div class="mr-2 mt-1" style="color: #2563eb;">
                                <i class="fas fa-check-circle" style="font-size: 11px;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="font-weight-bold text-dark mb-0.5" style="line-height: 1.25;">
                                    {{ str_replace('_', ' ', $l->action) }}
                                </div>
                                <div class="text-muted" style="font-size: 11px;">
                                    {{ $l->created_at ? $l->created_at->diffForHumans() : '' }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small text-center my-3">Belum ada riwayat aktivitas yang tercatat untuk akun ini.</p>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- KOLOM KANAN (FORMULIR EDIT PROFIL & PENGATURAN KATA SANDI) --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0" style="border-radius: 14px; overflow: hidden;">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-dark" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                        <i class="fas fa-sliders-h text-primary mr-2"></i>
                        Formulir Pengaturan Profil &amp; Kata Sandi
                    </h6>
                    <span class="text-muted small" style="font-family: 'Inter', sans-serif;">
                        <span class="text-danger">*</span> Wajib diisi
                    </span>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('profile.update') }}" method="POST" autocomplete="off">
                        @csrf
                        @method('PUT')

                        {{-- SEKSI 1: IDENTITAS & KONTAK KEDINASAN --}}
                        <div class="d-flex align-items-center mb-3">
                            <span class="profile-section-badge mr-2">1</span>
                            <h6 class="font-weight-bold text-dark mb-0" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 14px;">
                                Identitas Pengguna &amp; Kontak Resmi
                            </h6>
                        </div>

                        <div class="row mb-4">
                            {{-- Nama Lengkap --}}
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="font-weight-bold text-dark small mb-1">
                                    Nama Lengkap / Username <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light text-muted border-right-0" style="border-radius: 8px 0 0 8px;">
                                            <i class="fas fa-user"></i>
                                        </span>
                                    </div>
                                    <input type="text"
                                           name="name"
                                           class="form-control"
                                           placeholder="Masukkan nama lengkap Anda..."
                                           value="{{ old('name', $user->name) }}"
                                           required
                                           style="border-radius: 0 8px 8px 0; font-size: 13px;">
                                </div>
                                <small class="text-muted d-block mt-1">Nama ini akan tercatat pada jejak audit unggahan berkas spasial.</small>
                            </div>

                            {{-- Alamat Email --}}
                            <div class="col-md-6">
                                <label class="font-weight-bold text-dark small mb-1">
                                    Alamat Email Kedinasan <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light text-muted border-right-0" style="border-radius: 8px 0 0 8px;">
                                            <i class="fas fa-envelope"></i>
                                        </span>
                                    </div>
                                    <input type="email"
                                           name="email"
                                           class="form-control"
                                           placeholder="nama@pu.go.id atau email resmi..."
                                           value="{{ old('email', $user->email) }}"
                                           required
                                           style="border-radius: 0 8px 8px 0; font-size: 13px;">
                                </div>
                                <small class="text-muted d-block mt-1">Digunakan untuk akses login dan notifikasi sistem WebGIS.</small>
                            </div>
                        </div>

                        <div class="profile-info-divider my-4"></div>

                        {{-- SEKSI 2: KEAMANAN & PERGANTIAN KATA SANDI --}}
                        <div class="d-flex align-items-center mb-2">
                            <span class="profile-section-badge mr-2">2</span>
                            <h6 class="font-weight-bold text-dark mb-0" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 14px;">
                                Pengaturan Kata Sandi (Opsional)
                            </h6>
                        </div>

                        <div class="alert alert-light border py-2.5 px-3 mb-3 d-flex align-items-center" style="border-radius: 10px; background: #f8fafc; font-size: 12.5px;">
                            <i class="fas fa-info-circle text-primary mr-2 fa-lg"></i>
                            <span class="text-muted">Biarkan kedua kolom kata sandi di bawah <strong>kosong</strong> jika Anda tidak berniat mengubah kata sandi saat ini.</span>
                        </div>

                        <div class="row mb-4">
                            {{-- Password Baru --}}
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="font-weight-bold text-dark small mb-1">
                                    Kata Sandi Baru
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light text-muted border-right-0" style="border-radius: 8px 0 0 8px;">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                    </div>
                                    <input type="password"
                                           name="password"
                                           id="inputNewPassword"
                                           class="form-control"
                                           placeholder="Minimal 6 karakter..."
                                           style="border-radius: 0; font-size: 13px;">
                                    <div class="input-group-append">
                                        <button class="btn btn-light border border-left-0 text-muted"
                                                type="button"
                                                onclick="togglePasswordVisibility('inputNewPassword', 'iconToggleNew')"
                                                style="border-radius: 0 8px 8px 0;">
                                            <i class="far fa-eye" id="iconToggleNew"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted d-block mt-1">Gunakan kombinasi huruf, angka, atau simbol agar aman.</small>
                            </div>

                            {{-- Konfirmasi Password Baru --}}
                            <div class="col-md-6">
                                <label class="font-weight-bold text-dark small mb-1">
                                    Konfirmasi Kata Sandi Baru
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light text-muted border-right-0" style="border-radius: 8px 0 0 8px;">
                                            <i class="fas fa-check-circle"></i>
                                        </span>
                                    </div>
                                    <input type="password"
                                           name="password_confirmation"
                                           id="inputConfirmPassword"
                                           class="form-control"
                                           placeholder="Ulangi kata sandi baru..."
                                           style="border-radius: 0; font-size: 13px;">
                                    <div class="input-group-append">
                                        <button class="btn btn-light border border-left-0 text-muted"
                                                type="button"
                                                onclick="togglePasswordVisibility('inputConfirmPassword', 'iconToggleConfirm')"
                                                style="border-radius: 0 8px 8px 0;">
                                            <i class="far fa-eye" id="iconToggleConfirm"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted d-block mt-1">Harus sama persis dengan kolom kata sandi baru di samping.</small>
                            </div>
                        </div>

                        {{-- TOMBOL AKSI SUBMIT --}}
                        <div class="pt-3 border-top d-flex align-items-center justify-content-between flex-wrap" style="gap: 10px;">
                            <a href="{{ route('dashboard') }}" class="bws-btn-top bws-btn-top-map" style="font-size: 13px;">
                                <i class="fas fa-times mr-1.5 text-secondary"></i> Batal
                            </a>

                            <button type="submit" class="bws-btn-top bws-btn-top-primary" style="font-size: 13.5px; padding: 0 24px;">
                                <i class="fas fa-save mr-2"></i>
                                <span>Simpan Perubahan Profil</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

</div>

{{-- ================================================================ --}}
{{-- SCRIPT INTERAKTIF INTIP KATA SANDI --}}
{{-- ================================================================ --}}
<script>
function togglePasswordVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (!input || !icon) return;

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

{{-- ================================================================ --}}
{{-- STYLING CSS HALAMAN PROFIL EKSEKUTIF --}}
{{-- ================================================================ --}}
<style>
/* Hero Banner di Kartu Profil */
.profile-hero-banner {
    height: 110px;
    background: linear-gradient(135deg, #091e3a 0%, #153e75 52%, #1d4ed8 100%);
    position: relative;
    border-bottom: 3px solid #fbbf24; /* Aksen Emas PU */
}
.profile-banner-pu-logo {
    position: absolute;
    top: 14px;
    right: 16px;
    width: 38px;
    height: 38px;
    border-radius: 8px;
    background: #ffffff;
    padding: 2px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.25);
}

/* Avatar Besar */
.profile-avatar-wrapper {
    margin-top: -50px;
    display: inline-block;
}
.profile-avatar-circle {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
    color: #ffffff;
    border: 4px solid #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 38px;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.18);
    margin: 0 auto;
}

/* Divider & Badge Seksi Form */
.profile-info-divider {
    height: 1px;
    background: #f1f5f9;
}
.profile-section-badge {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #dbeafe;
    color: #1d4ed8;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 800;
    font-family: 'Plus Jakarta Sans', sans-serif;
}

/* Role Badges */
.user-role-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11.5px;
    font-weight: 700;
    font-family: 'Plus Jakarta Sans', sans-serif;
    white-space: nowrap;
}
.user-role-super {
    background: #fef2f2;
    color: #991b1b;
    border: 1px solid #fecaca;
}
.user-role-admin {
    background: #eff6ff;
    color: #1e40af;
    border: 1px solid #bfdbfe;
}
.user-role-default {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
}

/* Header Buttons */
.bws-btn-top {
    height: 38px;
    padding: 0 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    font-family: 'Plus Jakarta Sans', sans-serif;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    white-space: nowrap;
    cursor: pointer;
}
.bws-btn-top:active {
    transform: scale(0.96) !important;
}
.bws-btn-top-primary {
    background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
    color: #ffffff !important;
    border: 1px solid #1d4ed8;
}
.bws-btn-top-primary:hover {
    background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 100%);
    box-shadow: 0 4px 14px rgba(29, 78, 216, 0.35);
    transform: translateY(-1px);
}
.bws-btn-top-map {
    background: #ffffff;
    color: #1e293b !important;
    border: 1.5px solid #cbd5e1;
}
.bws-btn-top-map:hover {
    background: #eff6ff;
    color: #1d4ed8 !important;
    border-color: #93c5fd;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
    transform: translateY(-1px);
}
</style>
@endsection
