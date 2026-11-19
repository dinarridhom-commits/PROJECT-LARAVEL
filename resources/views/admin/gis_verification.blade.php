@extends('layout.master')

@section('title', 'Verifikasi Berkas GIS - Super Admin')

@section('konten')
<div class="container-fluid">

    {{-- ============================================================ --}}
    {{-- HEADER --}}
    {{-- ============================================================ --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 font-weight-bold text-gray-800">
                <i class="fas fa-clipboard-check text-primary mr-2"></i>
                Verifikasi Berkas Spasial
            </h1>
            <p class="text-muted small mb-0">
                Tinjau dan verifikasi data GeoJSON yang diunggah oleh staf sebelum dipublikasikan ke peta WebGIS BWS Kalimantan I.
            </p>
        </div>

        <div class="d-flex align-items-center" style="gap: 10px;">
            <a href="{{ route('gis.trash') }}" class="bws-btn-top bws-btn-top-archive" title="Buka Arsip Berkas Spasial yang Dihapus">
                <i class="fas fa-archive mr-2 text-danger"></i>
                <span>Arsip Terhapus</span>
            </a>
            <a href="{{ route('peta') }}" class="bws-btn-top bws-btn-top-primary" title="Buka Peta Spasial Interaktif" target="_blank">
                <i class="fas fa-map-marked-alt mr-2"></i>
                <span>Buka Peta</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- KPI STATS CARDS --}}
    {{-- ============================================================ --}}
    @php
        $pendingCount  = count($pendingLayers);
        $approvedCount = collect($historyLayers)->where('status', 'approved')->count();
        $rejectedCount = collect($historyLayers)->where('status', 'rejected')->count();
        $totalCount    = $pendingCount + $approvedCount + $rejectedCount;
    @endphp

    <div class="row mb-4" style="gap: 0;">
        {{-- Menunggu --}}
        <div class="col-6 col-md-3 mb-3">
            <div class="gis-kpi-card gis-kpi-warning h-100">
                <div class="gis-kpi-icon-wrap gis-kpi-icon-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="gis-kpi-body">
                    <div class="gis-kpi-value">{{ $pendingCount }}</div>
                    <div class="gis-kpi-label">Menunggu Verifikasi</div>
                </div>
            </div>
        </div>
        {{-- Disetujui --}}
        <div class="col-6 col-md-3 mb-3">
            <div class="gis-kpi-card gis-kpi-success h-100">
                <div class="gis-kpi-icon-wrap gis-kpi-icon-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="gis-kpi-body">
                    <div class="gis-kpi-value">{{ $approvedCount }}</div>
                    <div class="gis-kpi-label">Berkas Disetujui</div>
                </div>
            </div>
        </div>
        {{-- Ditolak --}}
        <div class="col-6 col-md-3 mb-3">
            <div class="gis-kpi-card gis-kpi-danger h-100">
                <div class="gis-kpi-icon-wrap gis-kpi-icon-danger">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="gis-kpi-body">
                    <div class="gis-kpi-value">{{ $rejectedCount }}</div>
                    <div class="gis-kpi-label">Berkas Ditolak</div>
                </div>
            </div>
        </div>
        {{-- Total --}}
        <div class="col-6 col-md-3 mb-3">
            <div class="gis-kpi-card gis-kpi-info h-100">
                <div class="gis-kpi-icon-wrap gis-kpi-icon-info">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="gis-kpi-body">
                    <div class="gis-kpi-value">{{ $totalCount }}</div>
                    <div class="gis-kpi-label">Total Berkas Spasial</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- TABEL ANTREAN PENDING --}}
    {{-- ============================================================ --}}
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid #f1f5f9;">
            <h6 class="m-0 font-weight-bold text-dark">
                <i class="fas fa-clock text-warning mr-2"></i>
                Antrean Berkas Menunggu Verifikasi
                <span class="gis-kpi-count-chip ml-1">{{ $pendingCount }}</span>
            </h6>
            @if($pendingCount > 0)
                <span class="gis-badge-pending">Butuh Tindakan</span>
            @endif
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="pl-4 py-3">Nama Objek Spasial</th>
                            <th class="py-3">Pengunggah</th>
                            <th class="py-3">Kategori</th>
                            <th class="py-3">Ukuran Berkas</th>
                            <th class="py-3">Waktu Diunggah</th>
                            <th class="pr-4 text-right py-3">Aksi Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingLayers as $l)
                            <tr>
                                <td class="pl-4 font-weight-bold text-dark">
                                    <i class="fas fa-file-code text-primary mr-1"></i>
                                    {{ $l->nama_objek }}
                                    <small class="text-muted d-block" style="font-family: monospace;">{{ $l->filename }}</small>
                                </td>
                                <td>
                                    <span class="font-weight-semibold text-dark">
                                        {{ $l->user ? $l->user->name : 'Staf GIS' }}
                                    </span>
                                    <small class="text-muted d-block">{{ $l->user ? $l->user->email : '-' }}</small>
                                </td>
                                <td>
                                    <span class="gis-badge-category text-uppercase">
                                        {{ $l->category }}
                                    </span>
                                </td>
                                <td class="text-muted small">{{ $l->file_size ?? '-' }}</td>
                                <td class="text-muted small">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $l->created_at ? $l->created_at->format('d M Y, H:i') : '-' }}
                                </td>
                                <td class="pr-4 text-right">
                                    {{-- Tombol Setujui --}}
                                    <form action="{{ route('gis.approve', $l->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Setujui berkas ini agar langsung tampil di peta WebGIS publik?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success font-weight-bold px-3 shadow-sm"
                                                style="border-radius: 6px;" title="Setujui dan publikasikan berkas ke peta">
                                            <i class="fas fa-check mr-1"></i> Setujui
                                        </button>
                                    </form>

                                    {{-- Tombol Tolak --}}
                                    <button type="button" class="btn btn-sm btn-outline-danger ml-1"
                                            style="border-radius: 6px;"
                                            data-toggle="modal" data-target="#modalReject{{ $l->id }}"
                                            title="Tolak berkas dengan catatan alasan">
                                        <i class="fas fa-times mr-1"></i> Tolak
                                    </button>

                                    {{-- Modal Alasan Penolakan --}}
                                    <div class="modal fade text-left" id="modalReject{{ $l->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                                                <form action="{{ route('gis.reject', $l->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header bg-light">
                                                        <h5 class="modal-title font-weight-bold text-danger">
                                                            <i class="fas fa-exclamation-triangle mr-2"></i>Tolak Berkas Spasial
                                                        </h5>
                                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="small text-muted mb-3">
                                                            Anda akan menolak berkas <b>{{ $l->nama_objek }}</b>. Berikan catatan alasan penolakan agar staf pengunggah dapat melakukan perbaikan.
                                                        </p>
                                                        <div class="form-group mb-0">
                                                            <label class="font-weight-bold small text-dark">Alasan Penolakan <span class="text-danger">*</span></label>
                                                            <textarea name="review_notes" rows="3" class="form-control"
                                                                      placeholder="Contoh: Format koordinat belum sesuai standar WGS84, atau batas poligon bertumpuk..." required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-danger btn-sm font-weight-bold">
                                                            <i class="fas fa-paper-plane mr-1"></i> Kirim Penolakan
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            {{-- IMPROVED EMPTY STATE --}}
                            <tr>
                                <td colspan="6" class="p-0 border-0">
                                    <div class="gis-empty-state">
                                        <div class="gis-empty-icon-ring">
                                            <i class="fas fa-shield-alt"></i>
                                        </div>
                                        <h6 class="gis-empty-title">Semua Berkas Spasial Telah Diverifikasi</h6>
                                        <p class="gis-empty-sub">Tidak ada antrean berkas yang menunggu tindakan verifikasi saat ini.<br>Berkas baru akan muncul di sini setelah staf mengunggah data spasial.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- RIWAYAT VERIFIKASI --}}
    {{-- ============================================================ --}}
    @php
        // Palet warna dot per kategori (otomatis assign warna bergilir jika tidak terdaftar)
        $catColorMap = [
            'dir baku'        => '#3b82f6',
            'dir fungsional'  => '#6366f1',
            'dir potensial'   => '#f59e0b',
            'saluran'         => '#06b6d4',
            'bangunan air'    => '#ef4444',
            'irigasi'         => '#10b981',
            'sungai'          => '#0ea5e9',
        ];
        $fallbackColors = ['#8b5cf6','#ec4899','#14b8a6','#f97316','#84cc16'];
        $uniqueCategories = collect($historyLayers)
            ->pluck('category')->filter()->unique()->sort()->values();
        $assignedColors = [];
        $fallbackIdx = 0;
        foreach ($uniqueCategories as $cat) {
            $key = strtolower(trim($cat));
            if (isset($catColorMap[$key])) {
                $assignedColors[$cat] = $catColorMap[$key];
            } else {
                $assignedColors[$cat] = $fallbackColors[$fallbackIdx % count($fallbackColors)];
                $fallbackIdx++;
            }
        }
    @endphp

    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
        <div class="card-header bg-white py-3" style="border-bottom: 1px solid #f1f5f9;">

            {{-- Baris 1: Judul + Search + Status Tab --}}
            <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap: 10px;">
                <h6 class="m-0 font-weight-bold text-dark">
                    <i class="fas fa-history text-secondary mr-2"></i>
                    Riwayat Verifikasi Terbaru
                </h6>

                {{-- Search + Tab Filter --}}
                <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                    {{-- Search bar --}}
                    <div class="input-group input-group-sm" style="width: 230px;">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0" style="border-radius: 8px 0 0 8px;">
                                <i class="fas fa-search text-muted" style="font-size: 11px;"></i>
                            </span>
                        </div>
                        <input type="text" id="historySearch" class="form-control border-left-0"
                               placeholder="Cari nama berkas..."
                               style="border-radius: 0 8px 8px 0; font-size: 12.5px;">
                    </div>
                    {{-- Tab Filter Status --}}
                    <div class="gis-filter-tabs">
                        <button class="gis-filter-tab active" data-filter="all">
                            Semua <span class="gis-filter-count">{{ count($historyLayers) }}</span>
                        </button>
                        <button class="gis-filter-tab" data-filter="approved">
                            Disetujui <span class="gis-filter-count">{{ $approvedCount }}</span>
                        </button>
                        <button class="gis-filter-tab" data-filter="rejected">
                            Ditolak <span class="gis-filter-count">{{ $rejectedCount }}</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Baris 2: Chip Filter Kategori --}}
            @if($uniqueCategories->count() > 0)
            <div class="gis-category-chips mt-2" id="categoryChipBar">
                <button class="gis-cat-chip active" data-cat="all">
                    <span class="gis-cat-dot" style="background:#64748b;"></span>
                    Semua Kategori
                </button>
                @foreach($uniqueCategories as $cat)
                    @php $dotColor = $assignedColors[$cat] ?? '#64748b'; @endphp
                    <button class="gis-cat-chip" data-cat="{{ strtolower(trim($cat)) }}">
                        <span class="gis-cat-dot" style="background:{{ $dotColor }};"></span>
                        {{ $cat }}
                    </button>
                @endforeach
            </div>
            @endif

        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="historyTable" style="font-size: 12.5px;">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="pl-4 py-3">Nama Berkas</th>
                            <th class="py-3">Pengunggah</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Diverifikasi Oleh</th>
                            <th class="py-3">Catatan / Waktu</th>
                        </tr>
                    </thead>
                    <tbody id="historyTableBody">
                        @forelse($historyLayers as $hl)
                            <tr class="history-row"
                                data-status="{{ $hl->status }}"
                                data-name="{{ strtolower($hl->nama_objek) }}"
                                data-category="{{ strtolower(trim($hl->category ?? '')) }}">
                                <td class="pl-4 font-weight-bold text-dark">
                                    {{ $hl->nama_objek }}
                                </td>
                                <td class="text-muted">{{ $hl->user ? $hl->user->name : '-' }}</td>
                                <td>
                                    @if($hl->status === 'approved')
                                        <span class="gis-badge-approved"><i class="fas fa-check mr-1"></i>Disetujui</span>
                                    @else
                                        <span class="gis-badge-rejected"><i class="fas fa-times mr-1"></i>Ditolak</span>
                                    @endif
                                </td>
                                <td>{{ $hl->reviewer ? $hl->reviewer->name : 'Super Admin' }}</td>
                                <td>
                                    @if($hl->status === 'rejected' && $hl->review_notes)
                                        <div class="gis-rejection-note">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            {{ $hl->review_notes }}
                                        </div>
                                    @else
                                        <span class="text-muted">{{ $hl->review_notes ?? '-' }}</span>
                                    @endif
                                    <small class="d-block text-muted mt-1">
                                        {{ $hl->reviewed_at ? $hl->reviewed_at->format('d M Y, H:i') : '' }}
                                    </small>
                                </td>
                            </tr>
                        @empty
                            <tr id="historyEmptyRow">
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat verifikasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{-- No results message --}}
                <div id="historyNoResults" class="text-center py-4 text-muted" style="display:none; font-size: 13px;">
                    <i class="fas fa-search fa-2x mb-2 d-block text-muted" style="opacity:.4;"></i>
                    Tidak ditemukan berkas yang sesuai dengan pencarian.
                </div>
            </div>

            {{-- Pagination Footer --}}
            <div class="bws-table-v2-footer px-3 py-2" id="historyTableFooter"
                 style="display:none; border-top: 1px solid #eaecf0; background:#fafafa; border-radius: 0 0 12px 12px;">
                <div class="bws-table-v2-info" id="historyFooterInfo" style="font-size:12.5px; color:#6c757d;"></div>
                <div class="bws-table-v2-pagination" id="historyPagination"></div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<style>
/* ================================================================
   TOP HEADER BUTTONS (PROFESIONAL & GAGAH)
================================================================ */
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

/* ================================================================
   KPI CARDS
================================================================ */
.gis-kpi-card {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px 18px;
    border-radius: 12px;
    background: #fff;
    border: 1px solid #e9ecef;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
    transition: box-shadow .2s;
}
.gis-kpi-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,.10); }

.gis-kpi-icon-wrap {
    width: 46px; height: 46px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.gis-kpi-icon-warning { background:#fff8e1; color:#f59e0b; }
.gis-kpi-icon-success { background:#dcfce7; color:#15803d; }
.gis-kpi-icon-danger  { background:#fee2e2; color:#b91c1c; }
.gis-kpi-icon-info    { background:#eff6ff; color:#1d4ed8; }

.gis-kpi-warning { border-left: 4px solid #f59e0b; }
.gis-kpi-success { border-left: 4px solid #15803d; }
.gis-kpi-danger  { border-left: 4px solid #b91c1c; }
.gis-kpi-info    { border-left: 4px solid #1d4ed8; }

.gis-kpi-value {
    font-size: 26px;
    font-weight: 800;
    color: #1e293b;
    line-height: 1.1;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.gis-kpi-label {
    font-size: 11.5px;
    color: #64748b;
    font-weight: 500;
    margin-top: 2px;
    font-family: 'Inter', sans-serif;
}

/* ================================================================
   BADGES
================================================================ */
.gis-badge-approved {
    display: inline-flex; align-items: center;
    background: #dcfce7; color: #15803d;
    border: 1px solid #bbf7d0;
    padding: 3px 10px; border-radius: 20px;
    font-size: 11.5px; font-weight: 700;
    white-space: nowrap;
}
.gis-badge-rejected {
    display: inline-flex; align-items: center;
    background: #fee2e2; color: #b91c1c;
    border: 1px solid #fecaca;
    padding: 3px 10px; border-radius: 20px;
    font-size: 11.5px; font-weight: 700;
    white-space: nowrap;
}
.gis-badge-pending {
    display: inline-flex; align-items: center;
    background: #fff8e1; color: #b45309;
    border: 1px solid #fde68a;
    padding: 3px 10px; border-radius: 20px;
    font-size: 11px; font-weight: 700;
}
.gis-badge-category {
    display: inline-block;
    background: #eff6ff; color: #1d4ed8;
    border: 1px solid #bfdbfe;
    padding: 2px 9px; border-radius: 20px;
    font-size: 10.5px; font-weight: 700;
}
.gis-kpi-count-chip {
    display: inline-flex; align-items: center; justify-content: center;
    background: #f1f5f9; color: #475569;
    border-radius: 20px;
    font-size: 11px; font-weight: 700;
    padding: 1px 8px;
    vertical-align: middle;
}

/* ================================================================
   REJECTION NOTE CALLOUT
================================================================ */
.gis-rejection-note {
    background: #fff7ed;
    border: 1px solid #fed7aa;
    border-radius: 6px;
    padding: 5px 10px;
    font-size: 11.5px;
    color: #9a3412;
    display: inline-block;
    max-width: 280px;
}

/* ================================================================
   EMPTY STATE
================================================================ */
.gis-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 52px 24px;
    text-align: center;
}
.gis-empty-icon-ring {
    width: 68px; height: 68px;
    border-radius: 50%;
    background: #dcfce7;
    display: flex; align-items: center; justify-content: center;
    font-size: 28px; color: #15803d;
    margin-bottom: 16px;
    box-shadow: 0 0 0 10px #f0fdf4;
}
.gis-empty-title {
    font-size: 15px; font-weight: 700;
    color: #1e293b; margin-bottom: 6px;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.gis-empty-sub {
    font-size: 12.5px; color: #64748b;
    max-width: 420px; line-height: 1.6; margin-bottom: 0;
}

/* ================================================================
   FILTER TABS (STATUS)
================================================================ */
.gis-filter-tabs {
    display: flex; align-items: center;
    background: #f1f5f9;
    border-radius: 8px;
    padding: 3px;
    gap: 2px;
}
.gis-filter-tab {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 12px;
    border-radius: 6px;
    border: none;
    background: transparent;
    font-size: 12px; font-weight: 600;
    color: #64748b;
    cursor: pointer;
    transition: all .15s ease;
    white-space: nowrap;
}
.gis-filter-tab:hover { background: #e2e8f0; color: #1e293b; }
.gis-filter-tab.active { background: #fff; color: #1d4ed8; box-shadow: 0 1px 3px rgba(0,0,0,.12); }
.gis-filter-count {
    background: #e2e8f0; color: #475569;
    border-radius: 10px; font-size: 10px;
    padding: 1px 6px; font-weight: 700;
}
.gis-filter-tab.active .gis-filter-count {
    background: #dbeafe; color: #1d4ed8;
}

/* ================================================================
   CATEGORY CHIP FILTER
================================================================ */
.gis-category-chips {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
    padding-top: 2px;
}
.gis-cat-chip {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 13px 4px 10px;
    border-radius: 20px;
    border: 1.5px solid #e2e8f0;
    background: #fff;
    font-size: 12px; font-weight: 600;
    color: #475569;
    cursor: pointer;
    transition: all .15s ease;
    white-space: nowrap;
    font-family: 'Inter', sans-serif;
}
.gis-cat-chip:hover {
    border-color: #94a3b8;
    color: #1e293b;
    background: #f8fafc;
}
.gis-cat-chip.active {
    border-color: currentColor;
    color: #1e293b;
    background: #f1f5f9;
    box-shadow: 0 1px 4px rgba(0,0,0,.10);
    font-weight: 700;
}
.gis-cat-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    display: inline-block;
    flex-shrink: 0;
}

/* ================================================================
   PAGINATION
================================================================ */
.bws-table-v2-footer {
    display: flex; align-items: center;
    justify-content: space-between;
    flex-wrap: wrap; gap: 8px;
}
.bws-table-v2-pagination { display: flex; align-items: center; gap: 4px; flex-wrap: wrap; }
.bws-pg-btn {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 32px; height: 32px; padding: 0 8px;
    border-radius: 6px; border: 1px solid #dee2e6;
    background: #fff; color: #495057;
    font-size: 12.5px; font-weight: 500; cursor: pointer;
    transition: all 0.15s ease; line-height: 1;
}
.bws-pg-btn:hover:not(.active):not(.disabled) { background:#f1f3f5; border-color:#adb5bd; color:#212529; }
.bws-pg-btn.active  { background:#0d6efd; border-color:#0d6efd; color:#fff; font-weight:700; cursor:default; }
.bws-pg-btn.disabled { opacity:.4; cursor:not-allowed; pointer-events:none; }
</style>

<script>
(function () {
    'use strict';

    const perPage   = 10;
    let currentPage = 1;
    let activeFilter   = 'all';   // status filter
    let activeCatFilter = 'all';  // category filter
    let searchTerm     = '';

    const allRows      = Array.from(document.querySelectorAll('#historyTableBody .history-row'));
    const footer       = document.getElementById('historyTableFooter');
    const footerInfo   = document.getElementById('historyFooterInfo');
    const paginationEl = document.getElementById('historyPagination');
    const noResults    = document.getElementById('historyNoResults');
    const searchInput  = document.getElementById('historySearch');
    const filterTabs   = document.querySelectorAll('.gis-filter-tab');
    const catChips     = document.querySelectorAll('.gis-cat-chip');

    if (allRows.length === 0) return;

    /* ---- helpers ---- */
    function getVisibleRows() {
        return allRows.filter(function(row) {
            const matchStatus = (activeFilter === 'all') || (row.dataset.status === activeFilter);
            const matchCat    = (activeCatFilter === 'all') || (row.dataset.category === activeCatFilter);
            const matchSearch = !searchTerm || row.dataset.name.includes(searchTerm);
            return matchStatus && matchCat && matchSearch;
        });
    }

    function render() {
        const visibleRows  = getVisibleRows();
        const totalVisible = visibleRows.length;
        const totalPages   = Math.max(1, Math.ceil(totalVisible / perPage));

        if (currentPage > totalPages) currentPage = totalPages;

        const start = (currentPage - 1) * perPage;
        const end   = start + perPage;

        allRows.forEach(function(r) { r.style.display = 'none'; });
        visibleRows.forEach(function(r, idx) {
            r.style.display = (idx >= start && idx < end) ? '' : 'none';
        });

        if (noResults) noResults.style.display = (totalVisible === 0) ? 'block' : 'none';

        if (totalVisible > 0) {
            footer.style.display = 'flex';
            var actualEnd = Math.min(end, totalVisible);
            footerInfo.textContent = 'Menampilkan ' + (start + 1) + ' - ' + actualEnd + ' dari ' + totalVisible + ' riwayat verifikasi';
            renderPagination(totalPages);
        } else {
            footer.style.display = 'none';
        }
    }

    function renderPagination(totalPages) {
        paginationEl.innerHTML = '';

        var prev = document.createElement('button');
        prev.className = 'bws-pg-btn' + (currentPage === 1 ? ' disabled' : '');
        prev.innerHTML = '&laquo;';
        prev.title = 'Halaman Sebelumnya';
        if (currentPage > 1) prev.addEventListener('click', function() { currentPage--; render(); });
        paginationEl.appendChild(prev);

        var maxButtons = 5;
        var half       = Math.floor(maxButtons / 2);
        var startPage  = Math.max(1, currentPage - half);
        var endPage    = Math.min(totalPages, startPage + maxButtons - 1);
        if (endPage - startPage < maxButtons - 1) startPage = Math.max(1, endPage - maxButtons + 1);

        if (startPage > 1) { appendPageBtn(1); if (startPage > 2) appendEllipsis(); }
        for (var p = startPage; p <= endPage; p++) appendPageBtn(p);
        if (endPage < totalPages) { if (endPage < totalPages - 1) appendEllipsis(); appendPageBtn(totalPages); }

        var next = document.createElement('button');
        next.className = 'bws-pg-btn' + (currentPage === totalPages ? ' disabled' : '');
        next.innerHTML = '&raquo;';
        next.title = 'Halaman Berikutnya';
        if (currentPage < totalPages) next.addEventListener('click', function() { currentPage++; render(); });
        paginationEl.appendChild(next);
    }

    function appendPageBtn(p) {
        var btn = document.createElement('button');
        btn.className = 'bws-pg-btn' + (p === currentPage ? ' active' : '');
        btn.textContent = p;
        btn.addEventListener('click', function() { currentPage = p; render(); });
        paginationEl.appendChild(btn);
    }

    function appendEllipsis() {
        var span = document.createElement('span');
        span.className = 'bws-pg-btn disabled';
        span.textContent = '...';
        paginationEl.appendChild(span);
    }

    /* ---- Events ---- */
    // Status filter tabs
    filterTabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            filterTabs.forEach(function(t) { t.classList.remove('active'); });
            tab.classList.add('active');
            activeFilter = tab.dataset.filter;
            currentPage  = 1;
            render();
        });
    });

    // Category chip filter
    catChips.forEach(function(chip) {
        chip.addEventListener('click', function() {
            catChips.forEach(function(c) { c.classList.remove('active'); });
            chip.classList.add('active');
            activeCatFilter = chip.dataset.cat;
            currentPage     = 1;
            render();
        });
    });

    // Search
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            searchTerm  = searchInput.value.toLowerCase().trim();
            currentPage = 1;
            render();
        });
    }

    // Init
    render();
})();
</script>
@endpush
