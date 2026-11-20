@extends('layout.master')

@section('title', 'Manajemen Pengguna — Super Admin')

@section('konten')
<div class="container-fluid">

    {{-- ============================================================ --}}
    {{-- HEADER HALAMAN & TOMBOL AKSI UTAMA --}}
    {{-- ============================================================ --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap" style="gap: 12px;">
        <div>
            <h1 class="h3 font-weight-bold text-gray-800 mb-1" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                <i class="fas fa-users-cog text-primary mr-2"></i>
                Manajemen Pengguna
            </h1>
            <p class="text-muted small mb-0" style="font-family: 'Inter', sans-serif;">
                Pembuatan akun staf/admin langsung oleh Super Admin dan pengelolaan hak akses.
            </p>
        </div>

        <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
            {{-- Tombol Tambah Pengguna --}}
            <button type="button" class="bws-btn-top bws-btn-top-primary" data-toggle="modal" data-target="#modalCreateUser">
                <i class="fas fa-user-plus mr-2"></i>
                <span>Tambah Pengguna</span>
            </button>

            {{-- Tombol Arsip Terhapus Pengguna --}}
            <a href="{{ route('users.trash') }}" class="bws-btn-top bws-btn-top-archive" title="Lihat akun yang terhapus sementara">
                <i class="fas fa-archive mr-2 text-danger"></i>
                <span>Arsip Terhapus</span>
                @php $trashedCount = \App\Models\User::onlyTrashed()->count(); @endphp
                @if($trashedCount > 0)
                    <span class="badge badge-danger ml-1.5" style="border-radius: 10px; font-size: 10.5px; padding: 2px 6px;">{{ $trashedCount }}</span>
                @endif
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
    {{-- KPI CARDS STATISTIK PENGGUNA (DAPAT DI-KLIK SEBAGAI FILTER) --}}
    {{-- ============================================================ --}}
    @php
        $currRole = $roleFilter ?? '';
    @endphp

    <div class="row mb-4">
        {{-- Total Pengguna --}}
        <div class="col-12 col-md-4 mb-3 mb-md-0">
            <a href="{{ route('users.index') }}"
               class="user-kpi-card {{ empty($currRole) ? 'user-kpi-active' : '' }} text-decoration-none d-block h-100"
               style="border-top: 3px solid #1d4ed8;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="user-kpi-label">Total Pengguna</div>
                        <div class="user-kpi-value text-dark">{{ $totalUsers }} <span class="user-kpi-unit">Akun</span></div>
                        <div class="user-kpi-desc text-muted">Semua staf &amp; administrator</div>
                    </div>
                    <div class="user-kpi-icon-wrap" style="background: #eff6ff; color: #1d4ed8;">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </a>
        </div>

        {{-- Super Admin --}}
        <div class="col-12 col-md-4 mb-3 mb-md-0">
            <a href="{{ route('users.index', ['role' => 'superadmin']) }}"
               class="user-kpi-card {{ $currRole === 'superadmin' ? 'user-kpi-active' : '' }} text-decoration-none d-block h-100"
               style="border-top: 3px solid #dc2626;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="user-kpi-label" style="color: #dc2626;">Super Admin</div>
                        <div class="user-kpi-value text-dark">{{ $totalSuperadmin }} <span class="user-kpi-unit">Akun</span></div>
                        <div class="user-kpi-desc text-muted">Otoritas penuh &amp; verifikasi data</div>
                    </div>
                    <div class="user-kpi-icon-wrap" style="background: #fee2e2; color: #dc2626;">
                        <i class="fas fa-crown"></i>
                    </div>
                </div>
            </a>
        </div>

        {{-- Admin --}}
        <div class="col-12 col-md-4">
            <a href="{{ route('users.index', ['role' => 'admin']) }}"
               class="user-kpi-card {{ $currRole === 'admin' ? 'user-kpi-active' : '' }} text-decoration-none d-block h-100"
               style="border-top: 3px solid #0284c7;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="user-kpi-label" style="color: #0284c7;">Admin / Staf GIS</div>
                        <div class="user-kpi-value text-dark">{{ $totalAdmin }} <span class="user-kpi-unit">Akun</span></div>
                        <div class="user-kpi-desc text-muted">Pengelola berkas &amp; unggah layer</div>
                    </div>
                    <div class="user-kpi-icon-wrap" style="background: #e0f2fe; color: #0284c7;">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- TABEL DATA PENGGUNA --}}
    {{-- ============================================================ --}}
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header bg-white py-3" style="border-bottom: 1px solid #f1f5f9;">
            <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap: 12px;">
                <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                    <h6 class="m-0 font-weight-bold text-dark" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                        <i class="fas fa-table text-primary mr-2"></i>
                        Daftar Akun Pengguna
                    </h6>
                    @if(!empty($currRole))
                        <span class="user-filter-chip">
                            <i class="fas fa-filter mr-1"></i>
                            Role: {{ $currRole === 'superadmin' ? 'Super Admin' : 'Admin' }}
                            <a href="{{ route('users.index', ['search' => $search ?? '']) }}" class="ml-1 text-primary" title="Hapus Filter Role">&times;</a>
                        </span>
                    @endif
                    @if(!empty($search))
                        <span class="badge badge-light border text-muted px-2 py-1" style="font-size: 11px;">
                            Pencarian: "<strong>{{ $search }}</strong>"
                        </span>
                    @endif
                </div>

                {{-- Form Pencarian Data --}}
                <form method="GET" action="{{ route('users.index') }}" class="form-inline m-0">
                    @if(!empty($currRole))
                        <input type="hidden" name="role" value="{{ $currRole }}">
                    @endif
                    <div class="input-group input-group-sm" style="width: 260px;">
                        <input type="text" name="search" class="form-control"
                               placeholder="Cari nama, email, role..."
                               value="{{ $search ?? '' }}"
                               style="border-radius: 8px 0 0 8px; font-size: 12.5px;">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit" title="Cari data" style="border-radius: {{ empty($search) ? '0 8px 8px 0' : '0' }};">
                                <i class="fas fa-search"></i>
                            </button>
                            @if(!empty($search))
                                <a href="{{ route('users.index', ['role' => $currRole]) }}"
                                   class="btn btn-outline-secondary" title="Hapus pencarian" style="border-radius: 0 8px 8px 0;">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                    <thead class="bg-light text-muted small text-uppercase" style="letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0;">
                        <tr>
                            <th class="pl-4 py-3 text-center" width="5%">No.</th>
                            <th class="py-3" width="35%">Nama &amp; Pengguna</th>
                            <th class="py-3" width="25%">Email Akun</th>
                            <th class="py-3 text-center" width="15%">Hak Akses / Role</th>
                            <th class="py-3 text-center" width="12%">Terdaftar</th>
                            <th class="py-3 pr-4 text-center" width="8%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $u)
                        @php
                            // Ambil inisial nama untuk avatar
                            $words = preg_split("/\s+/", trim($u->name));
                            $initials = '';
                            if (count($words) >= 2) {
                                $initials = strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1));
                            } else {
                                $initials = strtoupper(mb_substr($u->name, 0, 2));
                            }
                            $isSelf = (Auth::id() == $u->id);
                        @endphp
                        <tr>
                            {{-- Nomor Urut --}}
                            <td class="pl-4 text-center text-muted font-weight-bold align-middle">
                                {{ $index + 1 }}
                            </td>

                            {{-- Avatar & Nama Pengguna --}}
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar-circle mr-3 {{ $u->role === 'superadmin' ? 'user-avatar-super' : 'user-avatar-admin' }}">
                                        {{ $initials }}
                                    </div>
                                    <div>
                                        <div class="font-weight-bold text-dark d-flex align-items-center flex-wrap" style="font-size: 13.5px; gap: 6px;">
                                            <span>{{ $u->name }}</span>
                                            @if($isSelf)
                                                <span class="badge user-self-badge">
                                                    <i class="fas fa-check mr-1" style="font-size: 9px;"></i>Akun Anda
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Email --}}
                            <td class="align-middle font-monospace" style="font-size: 12.5px; color: #334155;">
                                <i class="far fa-envelope text-muted mr-1.5" style="font-size: 12px;"></i>
                                {{ $u->email }}
                            </td>

                            {{-- Role / Hak Akses --}}
                            <td class="align-middle text-center">
                                @if($u->role === 'superadmin')
                                    <span class="user-role-badge user-role-super">
                                        <i class="fas fa-crown mr-1.5" style="font-size: 10.5px;"></i>Super Admin
                                    </span>
                                @else
                                    <span class="user-role-badge user-role-admin">
                                        <i class="fas fa-user-shield mr-1.5" style="font-size: 10.5px;"></i>Admin
                                    </span>
                                @endif
                            </td>

                            {{-- Tanggal Terdaftar --}}
                            <td class="align-middle text-center text-muted small">
                                <div><i class="fas fa-calendar-alt text-gray-400 mr-1"></i>{{ $u->created_at ? $u->created_at->format('d M Y') : '-' }}</div>
                                <small class="text-gray-400">{{ $u->created_at ? $u->created_at->format('H:i') : '' }}</small>
                            </td>

                            {{-- Tombol Aksi --}}
                            <td class="pr-4 text-center align-middle">
                                <div class="d-flex align-items-center justify-content-center" style="gap: 6px;">
                                    {{-- Tombol Edit --}}
                                    <button type="button"
                                            class="bws-action-btn bws-act-edit"
                                            title="Edit Data Pengguna"
                                            data-toggle="modal"
                                            data-target="#editUser{{ $u->id }}">
                                        <i class="fas fa-user-edit"></i>
                                    </button>

                                    {{-- Tombol Hapus --}}
                                    @if(!$isSelf)
                                        <button type="button"
                                                class="bws-action-btn bws-act-delete"
                                                title="Pindahkan ke Arsip Terhapus"
                                                data-toggle="modal"
                                                data-target="#deleteUser{{ $u->id }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    @else
                                        <span class="bws-action-btn bws-act-disabled"
                                              title="Akun Anda sedang aktif digunakan (tidak dapat dihapus)">
                                            <i class="fas fa-lock text-muted"></i>
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-0 border-0">
                                <div class="text-center py-5 text-muted">
                                    <div style="width: 60px; height: 60px; border-radius: 50%; background: #f1f5f9; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px; color: #94a3b8; font-size: 24px;">
                                        <i class="fas fa-users-slash"></i>
                                    </div>
                                    <h6 class="font-weight-bold text-dark mb-1">Tidak Ada Data Pengguna</h6>
                                    <p class="text-muted small mb-3">
                                        @if(!empty($search) || !empty($currRole))
                                            Tidak ada akun yang sesuai dengan filter atau kata kunci pencarian.
                                        @else
                                            Belum ada pengguna lain yang terdaftar di dalam sistem.
                                        @endif
                                    </p>
                                    @if(!empty($search) || !empty($currRole))
                                        <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-primary" style="border-radius: 6px;">
                                            <i class="fas fa-redo mr-1"></i> Reset Filter &amp; Pencarian
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white py-3 border-top d-flex align-items-center justify-content-between flex-wrap" style="gap: 8px;">
            <div class="text-muted small" style="font-family: 'Inter', sans-serif;">
                Menampilkan <strong>{{ count($users) }}</strong> dari total <strong>{{ $totalUsers }}</strong> pengguna terdaftar
            </div>
        </div>
    </div>

</div>

{{-- MODAL CREATE, EDIT & DELETE --}}
@include('superadmin.modals')

{{-- ================================================================ --}}
{{-- STYLING MODERN HALAMAN MANAJEMEN PENGGUNA --}}
{{-- ================================================================ --}}
<style>
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
.bws-btn-top-archive {
    background: #ffffff;
    color: #475569 !important;
    border: 1.5px solid #cbd5e1;
}
.bws-btn-top-archive:hover {
    background: #fef2f2;
    color: #dc2626 !important;
    border-color: #fca5a5;
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.15);
    transform: translateY(-1px);
}

/* KPI Cards */
.user-kpi-card {
    padding: 16px 18px;
    border-radius: 12px;
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
}
.user-kpi-card:hover {
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    transform: translateY(-3px);
    border-color: #cbd5e1;
}
.user-kpi-card:active {
    transform: translateY(0) scale(0.97) !important;
}
.user-kpi-active {
    border-color: #1d4ed8 !important;
    box-shadow: 0 0 0 3px rgba(29,78,216,0.12), 0 4px 16px rgba(0,0,0,0.08) !important;
    background: #fbfdff;
}
.user-kpi-label {
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
    font-family: 'Plus Jakarta Sans', sans-serif;
    margin-bottom: 2px;
}
.user-kpi-value {
    font-size: 24px;
    font-weight: 800;
    line-height: 1.15;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.user-kpi-unit {
    font-size: 13px;
    font-weight: 600;
    color: #64748b;
}
.user-kpi-desc {
    font-size: 11px;
    margin-top: 3px;
    font-family: 'Inter', sans-serif;
}
.user-kpi-icon-wrap {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

/* User Avatar Inisial */
.user-avatar-circle {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 800;
    font-family: 'Plus Jakarta Sans', sans-serif;
    flex-shrink: 0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}
.user-avatar-super {
    background: #fee2e2;
    color: #b91c1c;
    border: 1.5px solid #fecaca;
}
.user-avatar-admin {
    background: #e0f2fe;
    color: #0369a1;
    border: 1.5px solid #bae6fd;
}

/* Badges */
.user-self-badge {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    border-radius: 12px;
    padding: 2px 7px;
    font-size: 10px;
    font-weight: 700;
}
.user-role-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px;
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
.user-filter-chip {
    display: inline-flex;
    align-items: center;
    background: #dbeafe;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11.5px;
    font-weight: 700;
    vertical-align: middle;
}

/* Action Buttons */
.bws-action-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12.5px;
    cursor: pointer;
    text-decoration: none !important;
    transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
}
.bws-action-btn:active {
    transform: scale(0.90) !important;
}
.bws-act-edit {
    color: #0284c7;
}
.bws-act-edit:hover {
    background: #e0f2fe;
    border-color: #7dd3fc;
    color: #0369a1;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(2, 132, 199, 0.20);
}
.bws-act-delete {
    color: #dc2626;
}
.bws-act-delete:hover {
    background: #fef2f2;
    border-color: #fca5a5;
    color: #b91c1c;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(220, 38, 38, 0.20);
}
.bws-act-disabled {
    cursor: not-allowed;
    background: #f8fafc;
    opacity: 0.5;
    border-color: #e2e8f0;
}

.table-hover tbody tr:hover {
    background-color: #f8fafc;
}
</style>
@endsection
