@extends('layout.master')

@section('title', 'Log Aktivitas Sistem - Super Admin')

@section('konten')

<!-- Header Halaman -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
    <div>
        <h1 class="h3 font-weight-bold text-gray-800 mb-1">
            <i class="fas fa-history text-primary mr-2"></i>
            Log Aktivitas Sistem
        </h1>
        <p class="text-muted small mb-0">
            Riwayat lengkap interaksi pengguna, unggahan file GeoJSON, dan perubahan sistem WebGIS.
        </p>
    </div>

    <div class="mt-2 mt-sm-0">
        <a href="{{ route('peta') }}" class="btn btn-sm btn-outline-primary shadow-sm mr-2">
            <i class="fas fa-map-marked-alt mr-1"></i> Buka Peta
        </a>
        <button type="button" class="btn btn-sm btn-outline-danger shadow-sm" data-toggle="modal" data-target="#modalClearLogs">
            <i class="fas fa-trash-alt mr-1"></i> Bersihkan Log
        </button>
    </div>
</div>

<!-- Flash Notifikasi -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif

<!-- Statistik Ringkas Log -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-primary shadow-sm h-100 py-2">
            <div class="card-body py-2">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Log Tercatat</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalLogs) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-info shadow-sm h-100 py-2">
            <div class="card-body py-2">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Aktivitas Hari Ini</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($todayLogs) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-success shadow-sm h-100 py-2">
            <div class="card-body py-2">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Upload File GIS</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalUploads) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-cloud-upload-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-danger shadow-sm h-100 py-2">
            <div class="card-body py-2">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Hapus Data & File</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalDeletes) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-trash fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Card Tabel Log Aktivitas -->
<div class="card shadow-sm mb-4">
    <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center flex-wrap">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-list-alt mr-1"></i> Riwayat Aktivitas Pengguna & Sistem
        </h6>

        <!-- Form Filter & Pencarian -->
        <form method="GET" action="{{ route('activity-logs.index') }}" class="form-inline mt-2 mt-sm-0">
            <select name="action" class="custom-select custom-select-sm mr-2" onchange="this.form.submit()">
                <option value="">-- Semua Aksi --</option>
                <option value="LOGIN" {{ request('action') == 'LOGIN' ? 'selected' : '' }}>Login</option>
                <option value="LOGOUT" {{ request('action') == 'LOGOUT' ? 'selected' : '' }}>Logout</option>
                <option value="UPLOAD_GEOJSON" {{ request('action') == 'UPLOAD_GEOJSON' ? 'selected' : '' }}>Upload GeoJSON</option>
                <option value="DELETE_GEOJSON" {{ request('action') == 'DELETE_GEOJSON' ? 'selected' : '' }}>Hapus GeoJSON</option>
                <option value="DELETE_GEOJSON_BULK" {{ request('action') == 'DELETE_GEOJSON_BULK' ? 'selected' : '' }}>Hapus Massal GeoJSON</option>
                <option value="UPDATE_USER" {{ request('action') == 'UPDATE_USER' ? 'selected' : '' }}>Update User</option>
                <option value="DELETE_USER" {{ request('action') == 'DELETE_USER' ? 'selected' : '' }}>Hapus User</option>
            </select>

            <div class="input-group input-group-sm">
                <input type="text" name="search" class="form-control" placeholder="Cari nama, aksi, IP..." value="{{ request('search') }}">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                    @if(request('search') || request('action'))
                        <a href="{{ route('activity-logs.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0" style="font-size: 13px;">
                <thead class="thead-light">
                    <tr>
                        <th width="5%" class="text-center">No.</th>
                        <th width="16%">Waktu</th>
                        <th width="18%">Pengguna</th>
                        <th width="12%">Role</th>
                        <th width="14%">Aksi</th>
                        <th width="23%">Keterangan</th>
                        <th width="12%">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $index => $log)
                    <tr>
                        <td class="text-center align-middle font-weight-bold text-muted">
                            {{ $logs->firstItem() + $index }}
                        </td>
                        <td class="align-middle">
                            <div class="font-weight-bold text-dark">{{ $log->created_at->format('d M Y, H:i') }}</div>
                            <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                        </td>
                        <td class="align-middle">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle mr-2 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 12px;">
                                    {{ strtoupper(substr($log->user_name, 0, 1)) }}
                                </div>
                                <span class="font-weight-bold text-dark">{{ $log->user_name }}</span>
                            </div>
                        </td>
                        <td class="align-middle">
                            @if($log->role === 'superadmin')
                                <span class="badge badge-danger px-2 py-1"><i class="fas fa-crown mr-1"></i>SUPER ADMIN</span>
                            @elseif($log->role === 'admin')
                                <span class="badge badge-primary px-2 py-1"><i class="fas fa-user-shield mr-1"></i>ADMIN</span>
                            @elseif($log->role === 'user')
                                <span class="badge badge-secondary px-2 py-1"><i class="fas fa-user mr-1"></i>USER</span>
                            @else
                                <span class="badge badge-dark px-2 py-1"><i class="fas fa-globe mr-1"></i>PUBLIK</span>
                            @endif
                        </td>
                        <td class="align-middle">
                            <span class="badge {{ $log->badge_class }} px-2 py-1 text-uppercase" style="font-size: 11px;">
                                {{ str_replace('_', ' ', $log->action) }}
                            </span>
                        </td>
                        <td class="align-middle text-break">
                            {{ $log->description ?? '-' }}
                        </td>
                        <td class="align-middle">
                            <code>{{ $log->ip_address ?? '127.0.0.1' }}</code>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-clipboard-check fa-3x text-muted mb-3 d-block"></i>
                            Belum ada riwayat log aktivitas yang tercatat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($logs->hasPages() || $logs->total() > 0)
    <div class="card-footer bg-white py-3 d-flex justify-content-between align-items-center flex-wrap" style="border-top: 1px solid #eaecf0; border-radius: 0 0 8px 8px; gap: 10px;">

        {{-- Info kiri --}}
        <small class="text-muted">
            Menampilkan <strong>{{ $logs->firstItem() }}</strong> &ndash; <strong>{{ $logs->lastItem() }}</strong>
            dari total <strong>{{ number_format($logs->total()) }}</strong> log aktivitas
        </small>

        {{-- Tombol Pagination Custom --}}
        @if($logs->hasPages())
        <div class="bwslog-pagination">
            {{-- Prev --}}
            @if($logs->onFirstPage())
                <span class="bwslog-pg-btn disabled">&laquo;</span>
            @else
                <a href="{{ $logs->previousPageUrl() }}" class="bwslog-pg-btn" title="Halaman Sebelumnya">&laquo;</a>
            @endif

            {{-- Nomor halaman --}}
            @php
                $currentPage = $logs->currentPage();
                $lastPage    = $logs->lastPage();
                $window      = 2; // halaman di kiri-kanan current
                $start       = max(1, $currentPage - $window);
                $end         = min($lastPage, $currentPage + $window);
            @endphp

            @if($start > 1)
                <a href="{{ $logs->url(1) }}" class="bwslog-pg-btn">1</a>
                @if($start > 2)
                    <span class="bwslog-pg-btn disabled">&hellip;</span>
                @endif
            @endif

            @for($p = $start; $p <= $end; $p++)
                @if($p === $currentPage)
                    <span class="bwslog-pg-btn active">{{ $p }}</span>
                @else
                    <a href="{{ $logs->url($p) }}" class="bwslog-pg-btn">{{ $p }}</a>
                @endif
            @endfor

            @if($end < $lastPage)
                @if($end < $lastPage - 1)
                    <span class="bwslog-pg-btn disabled">&hellip;</span>
                @endif
                <a href="{{ $logs->url($lastPage) }}" class="bwslog-pg-btn">{{ $lastPage }}</a>
            @endif

            {{-- Next --}}
            @if($logs->hasMorePages())
                <a href="{{ $logs->nextPageUrl() }}" class="bwslog-pg-btn" title="Halaman Berikutnya">&raquo;</a>
            @else
                <span class="bwslog-pg-btn disabled">&raquo;</span>
            @endif
        </div>
        @endif

    </div>
    @endif
</div>

<!-- Modal Konfirmasi Hapus Log -->
<div class="modal fade" id="modalClearLogs" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Bersihkan Seluruh Log?
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                <p class="mb-0">Apakah Anda yakin ingin menghapus seluruh riwayat aktivitas sistem?</p>
                <small class="text-muted">Tindakan ini tidak dapat dibatalkan.</small>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                <form action="{{ route('activity-logs.clear') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Ya, Bersihkan Log</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<style>
/* ================================================================
   CUSTOM PAGINATION - LOG AKTIVITAS
================================================================ */
.bwslog-pagination {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-wrap: wrap;
}
.bwslog-pg-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    height: 36px;
    padding: 0 10px;
    border-radius: 8px;
    border: 1.5px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.15s ease;
    line-height: 1;
    font-family: 'Inter', sans-serif;
    user-select: none;
}
.bwslog-pg-btn:hover:not(.active):not(.disabled) {
    background: #f1f5f9;
    border-color: #94a3b8;
    color: #1e293b;
    text-decoration: none;
}
.bwslog-pg-btn.active {
    background: #1d4ed8;
    border-color: #1d4ed8;
    color: #ffffff;
    font-weight: 700;
    cursor: default;
    box-shadow: 0 2px 6px rgba(29, 78, 216, 0.35);
}
.bwslog-pg-btn.disabled {
    opacity: 0.4;
    cursor: not-allowed;
    pointer-events: none;
    background: #f8fafc;
}
</style>
@endpush
