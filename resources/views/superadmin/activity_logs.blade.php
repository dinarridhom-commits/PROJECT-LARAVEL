@extends('layout.master')

@section('title', 'Log Aktivitas Sistem — Super Admin')

@section('konten')
<div class="container-fluid">

    {{-- ============================================================ --}}
    {{-- HEADER HALAMAN & TOMBOL AKSI UTAMA --}}
    {{-- ============================================================ --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap" style="gap: 12px;">
        <div>
            <h1 class="h3 font-weight-bold text-gray-800 mb-1" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                <i class="fas fa-history text-primary mr-2"></i>
                Log Aktivitas Sistem
            </h1>
            <p class="text-muted small mb-0" style="font-family: 'Inter', sans-serif;">
                Riwayat lengkap interaksi pengguna, unggahan file GeoJSON, dan perubahan sistem WebGIS BWS Kalimantan I.
            </p>
        </div>

        <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
            {{-- Tombol Buka Peta --}}
            <a href="{{ route('peta') }}" class="bws-btn-top bws-btn-top-map" title="Buka tampilan peta interaktif" target="_blank">
                <i class="fas fa-map-marked-alt mr-2 text-primary"></i>
                <span>Buka Peta</span>
            </a>

            {{-- Tombol Ekspor CSV (Audit Trail) --}}
            <a href="{{ route('activity-logs.export') }}" class="bws-btn-top bws-btn-top-export" title="Unduh seluruh riwayat aktivitas dalam format CSV/Excel">
                <i class="fas fa-file-excel mr-2 text-success"></i>
                <span>Ekspor CSV</span>
            </a>

            {{-- Tombol Bersihkan Log --}}
            <button type="button" class="bws-btn-top bws-btn-top-archive" data-toggle="modal" data-target="#modalClearLogs" title="Bersihkan riwayat aktivitas sistem">
                <i class="fas fa-trash-alt mr-2 text-danger"></i>
                <span>Bersihkan Log</span>
            </button>
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

    {{-- ============================================================ --}}
    {{-- 4 KPI CARDS INTERAKTIF (DAPAT DI-KLIK SEBAGAI FILTER CEPAT) --}}
    {{-- ============================================================ --}}
    @php
        $currQf = $quickFilter ?? '';
        $currAction = $actionFilter ?? '';
    @endphp

    <div class="row mb-4">
        {{-- Total Log Tercatat --}}
        <div class="col-12 col-sm-6 col-xl-3 mb-3 mb-xl-0">
            <a href="{{ route('activity-logs.index') }}"
               class="log-kpi-card {{ (empty($currQf) && empty($currAction)) ? 'log-kpi-active' : '' }} text-decoration-none d-block h-100"
               style="border-top: 3px solid #1d4ed8;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="log-kpi-label">Total Log Tercatat</div>
                        <div class="log-kpi-value text-dark">{{ number_format($totalLogs) }} <span class="log-kpi-unit">Data</span></div>
                        <div class="log-kpi-desc text-muted">Seluruh jejak audit sistem</div>
                    </div>
                    <div class="log-kpi-icon-wrap" style="background: #eff6ff; color: #1d4ed8;">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>
            </a>
        </div>

        {{-- Aktivitas Hari Ini --}}
        <div class="col-12 col-sm-6 col-xl-3 mb-3 mb-xl-0">
            <a href="{{ route('activity-logs.index', ['filter' => 'today']) }}"
               class="log-kpi-card {{ $currQf === 'today' ? 'log-kpi-active' : '' }} text-decoration-none d-block h-100"
               style="border-top: 3px solid #0284c7;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="log-kpi-label" style="color: #0284c7;">Aktivitas Hari Ini</div>
                        <div class="log-kpi-value text-dark">{{ number_format($todayLogs) }} <span class="log-kpi-unit">Data</span></div>
                        <div class="log-kpi-desc text-muted">{{ date('d M Y') }}</div>
                    </div>
                    <div class="log-kpi-icon-wrap" style="background: #e0f2fe; color: #0284c7;">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                </div>
            </a>
        </div>

        {{-- Upload File GIS --}}
        <div class="col-12 col-sm-6 col-xl-3 mb-3 mb-xl-0">
            <a href="{{ route('activity-logs.index', ['filter' => 'uploads']) }}"
               class="log-kpi-card {{ ($currQf === 'uploads' || $currAction === 'UPLOAD_GEOJSON') ? 'log-kpi-active' : '' }} text-decoration-none d-block h-100"
               style="border-top: 3px solid #16a34a;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="log-kpi-label" style="color: #16a34a;">Upload File GIS</div>
                        <div class="log-kpi-value text-dark">{{ number_format($totalUploads) }} <span class="log-kpi-unit">Unggahan</span></div>
                        <div class="log-kpi-desc text-muted">GeoJSON &amp; layer spasial</div>
                    </div>
                    <div class="log-kpi-icon-wrap" style="background: #dcfce7; color: #16a34a;">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                </div>
            </a>
        </div>

        {{-- Hapus Data & File --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <a href="{{ route('activity-logs.index', ['filter' => 'deletes']) }}"
               class="log-kpi-card {{ $currQf === 'deletes' ? 'log-kpi-active' : '' }} text-decoration-none d-block h-100"
               style="border-top: 3px solid #dc2626;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="log-kpi-label" style="color: #dc2626;">Hapus Data &amp; File</div>
                        <div class="log-kpi-value text-dark">{{ number_format($totalDeletes) }} <span class="log-kpi-unit">Tindakan</span></div>
                        <div class="log-kpi-desc text-muted">Penghapusan berkas &amp; akun</div>
                    </div>
                    <div class="log-kpi-icon-wrap" style="background: #fee2e2; color: #dc2626;">
                        <i class="fas fa-trash-alt"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- TABEL RIWAYAT AKTIVITAS SISTEM --}}
    {{-- ============================================================ --}}
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header bg-white py-3" style="border-bottom: 1px solid #f1f5f9;">
            <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap: 12px;">
                <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                    <h6 class="m-0 font-weight-bold text-dark" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                        <i class="fas fa-list-alt text-primary mr-2"></i>
                        Riwayat Aktivitas Pengguna &amp; Sistem
                    </h6>

                    @if(!empty($currQf))
                        <span class="log-filter-chip">
                            <i class="fas fa-filter mr-1"></i>
                            @if($currQf === 'today') Filter: Hari Ini
                            @elseif($currQf === 'uploads') Filter: Upload File GIS
                            @elseif($currQf === 'deletes') Filter: Hapus Data &amp; File
                            @endif
                            <a href="{{ route('activity-logs.index', ['search' => $search ?? '', 'action' => $currAction]) }}" class="ml-1 text-primary" title="Hapus Filter">&times;</a>
                        </span>
                    @endif

                    @if(!empty($currAction))
                        <span class="log-filter-chip">
                            <i class="fas fa-bolt mr-1"></i> Aksi: {{ str_replace('_', ' ', $currAction) }}
                            <a href="{{ route('activity-logs.index', ['search' => $search ?? '', 'filter' => $currQf]) }}" class="ml-1 text-primary" title="Hapus Filter Aksi">&times;</a>
                        </span>
                    @endif

                    @if(!empty($search))
                        <span class="badge badge-light border text-muted px-2 py-1" style="font-size: 11px;">
                            Pencarian: "<strong>{{ $search }}</strong>"
                        </span>
                    @endif
                </div>

                {{-- Form Filter & Pencarian --}}
                <form method="GET" action="{{ route('activity-logs.index') }}" class="form-inline m-0" style="gap: 6px;">
                    @if(!empty($currQf))
                        <input type="hidden" name="filter" value="{{ $currQf }}">
                    @endif

                    {{-- Dropdown Pilih Aksi --}}
                    <select name="action" class="custom-select custom-select-sm" style="font-size: 12.5px; border-radius: 8px; font-weight: 500;" onchange="this.form.submit()">
                        <option value="">-- Semua Aksi --</option>
                        <option value="LOGIN" {{ $currAction === 'LOGIN' ? 'selected' : '' }}>Login</option>
                        <option value="LOGOUT" {{ $currAction === 'LOGOUT' ? 'selected' : '' }}>Logout</option>
                        <option value="UPLOAD_GEOJSON" {{ $currAction === 'UPLOAD_GEOJSON' ? 'selected' : '' }}>Upload GeoJSON</option>
                        <option value="DELETE_GEOJSON" {{ $currAction === 'DELETE_GEOJSON' ? 'selected' : '' }}>Hapus GeoJSON</option>
                        <option value="DELETE_GEOJSON_BULK" {{ $currAction === 'DELETE_GEOJSON_BULK' ? 'selected' : '' }}>Hapus Massal GeoJSON</option>
                        <option value="UPDATE_USER" {{ $currAction === 'UPDATE_USER' ? 'selected' : '' }}>Update User</option>
                        <option value="DELETE_USER" {{ $currAction === 'DELETE_USER' ? 'selected' : '' }}>Hapus User</option>
                        <option value="CLEAR_LOGS" {{ $currAction === 'CLEAR_LOGS' ? 'selected' : '' }}>Bersihkan Log</option>
                    </select>

                    {{-- Input Pencarian --}}
                    <div class="input-group input-group-sm" style="width: 220px;">
                        <input type="text" name="search" class="form-control"
                               placeholder="Cari nama, aksi, IP..."
                               value="{{ $search ?? '' }}"
                               style="border-radius: 8px 0 0 8px; font-size: 12.5px;">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit" title="Cari data" style="border-radius: {{ (empty($search) && empty($currAction) && empty($currQf)) ? '0 8px 8px 0' : '0' }};">
                                <i class="fas fa-search"></i>
                            </button>
                            @if(!empty($search) || !empty($currAction) || !empty($currQf))
                                <a href="{{ route('activity-logs.index') }}"
                                   class="btn btn-outline-secondary" title="Reset Semua Filter" style="border-radius: 0 8px 8px 0;">
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
                            <th class="py-3" width="16%">Waktu Kejadian</th>
                            <th class="py-3" width="18%">Pengguna</th>
                            <th class="py-3 text-center" width="13%">Role Akun</th>
                            <th class="py-3 text-center" width="14%">Aksi Sistem</th>
                            <th class="py-3" width="22%">Keterangan</th>
                            <th class="py-3 pr-4 text-center" width="12%">IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $index => $log)
                        @php
                            // Ambil inisial nama untuk avatar
                            $userName = $log->user_name ?? 'Sistem';
                            $words = preg_split("/\s+/", trim($userName));
                            $initials = '';
                            if (count($words) >= 2) {
                                $initials = strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1));
                            } else {
                                $initials = strtoupper(mb_substr($userName, 0, 2));
                            }

                            // Tentukan kelas ikon dan warna badge aksi
                            $actionUpper = strtoupper($log->action);
                            $actIcon = 'fa-bolt';
                            $actClass = 'log-act-default';

                            if (str_contains($actionUpper, 'UPLOAD')) {
                                $actIcon = 'fa-cloud-upload-alt';
                                $actClass = 'log-act-upload';
                            } elseif (str_contains($actionUpper, 'DELETE') || str_contains($actionUpper, 'HAPUS')) {
                                $actIcon = 'fa-trash-alt';
                                $actClass = 'log-act-delete';
                            } elseif (str_contains($actionUpper, 'LOGIN') || str_contains($actionUpper, 'LOGOUT')) {
                                $actIcon = 'fa-sign-in-alt';
                                $actClass = 'log-act-login';
                            } elseif (str_contains($actionUpper, 'CLEAR')) {
                                $actIcon = 'fa-broom';
                                $actClass = 'log-act-clear';
                            } elseif (str_contains($actionUpper, 'UPDATE')) {
                                $actIcon = 'fa-user-edit';
                                $actClass = 'log-act-update';
                            }
                        @endphp
                        <tr>
                            {{-- Nomor Urut --}}
                            <td class="pl-4 text-center text-muted font-weight-bold align-middle">
                                {{ $logs->firstItem() + $index }}
                            </td>

                            {{-- Waktu --}}
                            <td class="align-middle">
                                <div class="font-weight-bold text-dark mb-0.5">
                                    {{ $log->created_at ? $log->created_at->format('d M Y, H:i') : '-' }}
                                </div>
                                <small class="text-muted" style="font-size: 11px;">
                                    <i class="far fa-clock mr-1"></i>{{ $log->created_at ? $log->created_at->diffForHumans() : '' }}
                                </small>
                            </td>

                            {{-- Pengguna --}}
                            <td class="align-middle">
                                <span class="font-weight-bold text-dark">{{ $userName }}</span>
                            </td>

                            {{-- Role --}}
                            <td class="align-middle text-center">
                                @if($log->role === 'superadmin')
                                    <span class="log-role-badge log-role-super">
                                        <i class="fas fa-crown mr-1"></i>Super Admin
                                    </span>
                                @elseif($log->role === 'admin')
                                    <span class="log-role-badge log-role-admin">
                                        <i class="fas fa-user-shield mr-1"></i>Admin
                                    </span>
                                @elseif($log->role === 'user')
                                    <span class="log-role-badge log-role-user">
                                        <i class="fas fa-user mr-1"></i>User
                                    </span>
                                @else
                                    <span class="log-role-badge log-role-default">
                                        <i class="fas fa-cog mr-1"></i>Sistem
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="align-middle text-center">
                                <span class="log-act-badge {{ $actClass }}">
                                    <i class="fas {{ $actIcon }} mr-1.5" style="font-size: 10px;"></i>
                                    {{ str_replace('_', ' ', $log->action) }}
                                </span>
                            </td>

                            {{-- Keterangan --}}
                            <td class="align-middle text-dark" style="line-height: 1.4;">
                                {{ $log->description ?? '-' }}
                            </td>

                            {{-- IP Address --}}
                            <td class="pr-4 text-center align-middle">
                                <span class="badge badge-light border text-muted px-2 py-1 font-monospace" style="font-size: 11px; letter-spacing: 0.3px;">
                                    {{ $log->ip_address ?? '127.0.0.1' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="p-0 border-0">
                                <div class="text-center py-5 text-muted">
                                    <div style="width: 60px; height: 60px; border-radius: 50%; background: #f1f5f9; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px; color: #94a3b8; font-size: 24px;">
                                        <i class="fas fa-clipboard-check"></i>
                                    </div>
                                    <h6 class="font-weight-bold text-dark mb-1">Belum Ada Riwayat Log</h6>
                                    <p class="text-muted small mb-3">
                                        @if(!empty($search) || !empty($currAction) || !empty($currQf))
                                            Tidak ada riwayat aktivitas yang sesuai dengan filter atau kata kunci pencarian.
                                        @else
                                            Belum ada interaksi sistem yang tercatat di dalam basis data.
                                        @endif
                                    </p>
                                    @if(!empty($search) || !empty($currAction) || !empty($currQf))
                                        <a href="{{ route('activity-logs.index') }}" class="btn btn-sm btn-outline-primary" style="border-radius: 6px;">
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

        {{-- Footer & Pagination --}}
        @if($logs->hasPages() || $logs->total() > 0)
        <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
            <div class="text-muted small" style="font-family: 'Inter', sans-serif;">
                Menampilkan <strong>{{ $logs->firstItem() ?? 0 }}</strong> &ndash; <strong>{{ $logs->lastItem() ?? 0 }}</strong>
                dari total <strong>{{ number_format($logs->total()) }}</strong> log aktivitas
            </div>

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
                    $window      = 2;
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

</div>

{{-- MODAL KONFIRMASI HAPUS LOG --}}
<div class="modal fade" id="modalClearLogs" tabindex="-1" role="dialog" aria-labelledby="modalClearLogsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header bg-danger text-white py-3">
                <h5 class="modal-title font-weight-bold" id="modalClearLogsLabel" style="font-size: 15px;">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Bersihkan Seluruh Riwayat Log?
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center py-4 px-4">
                <div style="width: 64px; height: 64px; border-radius: 50%; background: #fee2e2; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px; color: #dc2626; font-size: 26px;">
                    <i class="fas fa-trash-alt"></i>
                </div>
                <h6 class="font-weight-bold text-dark mb-2">Tindakan ini Tidak Dapat Dibatalkan</h6>
                <p class="text-muted small mb-0">
                    Seluruh jejak audit trail sistem akan dihapus permanen dari basis data. Pastikan Anda telah mengunduh <strong>Ekspor CSV</strong> terlebih dahulu jika dibutuhkan sebagai arsip laporan.
                </p>
            </div>
            <div class="modal-footer bg-light py-2.5 px-3">
                <button type="button" class="btn btn-secondary btn-sm px-3" data-dismiss="modal" style="border-radius: 6px;">Batal</button>
                <form action="{{ route('activity-logs.clear') }}" method="POST" class="m-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm px-3 font-weight-bold" style="border-radius: 6px;">
                        <i class="fas fa-trash-alt mr-1"></i> Ya, Bersihkan Log
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ================================================================ --}}
{{-- STYLING MODERN HALAMAN LOG AKTIVITAS SISTEM --}}
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
.bws-btn-top-export {
    background: #ffffff;
    color: #15803d !important;
    border: 1.5px solid #bbf7d0;
}
.bws-btn-top-export:hover {
    background: #f0fdf4;
    border-color: #86efac;
    color: #166534 !important;
    box-shadow: 0 4px 12px rgba(22, 163, 74, 0.15);
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
.log-kpi-card {
    padding: 16px 18px;
    border-radius: 12px;
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
}
.log-kpi-card:hover {
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    transform: translateY(-3px);
    border-color: #cbd5e1;
}
.log-kpi-card:active {
    transform: translateY(0) scale(0.97) !important;
}
.log-kpi-active {
    border-color: #1d4ed8 !important;
    box-shadow: 0 0 0 3px rgba(29,78,216,0.12), 0 4px 16px rgba(0,0,0,0.08) !important;
    background: #fbfdff;
}
.log-kpi-label {
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
    font-family: 'Plus Jakarta Sans', sans-serif;
    margin-bottom: 2px;
}
.log-kpi-value {
    font-size: 24px;
    font-weight: 800;
    line-height: 1.15;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.log-kpi-unit {
    font-size: 13px;
    font-weight: 600;
    color: #64748b;
}
.log-kpi-desc {
    font-size: 11px;
    margin-top: 3px;
    font-family: 'Inter', sans-serif;
}
.log-kpi-icon-wrap {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

/* User Avatar & Filter Chips */
.log-avatar-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11.5px;
    font-weight: 800;
    font-family: 'Plus Jakarta Sans', sans-serif;
    flex-shrink: 0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}
.log-avatar-super {
    background: #fee2e2;
    color: #b91c1c;
    border: 1.5px solid #fecaca;
}
.log-avatar-admin {
    background: #e0f2fe;
    color: #0369a1;
    border: 1.5px solid #bae6fd;
}

.log-filter-chip {
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

/* Role Badges */
.log-role-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    font-family: 'Plus Jakarta Sans', sans-serif;
    white-space: nowrap;
}
.log-role-super { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
.log-role-admin { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }
.log-role-user  { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
.log-role-default { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }

/* Action Badges */
.log-act-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 9px;
    border-radius: 6px;
    font-size: 10.5px;
    font-weight: 700;
    letter-spacing: 0.3px;
    white-space: nowrap;
    text-transform: uppercase;
}
.log-act-upload  { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
.log-act-delete  { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
.log-act-login   { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
.log-act-clear   { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
.log-act-update  { background: #fdf4ff; color: #a21caf; border: 1px solid #f5d0fe; }
.log-act-default { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

.table-hover tbody tr:hover {
    background-color: #f8fafc;
}

/* Pagination */
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
.bwslog-pg-btn:active:not(.disabled) {
    transform: scale(0.93) !important;
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
@endsection
