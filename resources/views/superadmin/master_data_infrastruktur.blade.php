@extends('layout.master')

@section('title', 'Master Data — Infrastruktur Irigasi')

@section('konten')
<div class="container-fluid">

    {{-- ============================================================ --}}
    {{-- HEADER --}}
    {{-- ============================================================ --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap" style="gap: 10px;">
        <div>
            <h1 class="h3 font-weight-bold text-gray-800 mb-1">
                <i class="fas fa-layer-group text-primary mr-2"></i>
                Data Infrastruktur Irigasi
            </h1>
            <p class="text-muted small mb-0">
                Rekapitulasi seluruh berkas spasial infrastruktur irigasi yang telah disetujui dan dipublikasikan ke peta WebGIS BWS Kalimantan I.
            </p>
        </div>
        <a href="{{ route('peta') }}" class="btn btn-primary font-weight-bold shadow-sm">
            <i class="fas fa-map mr-1"></i> Buka Peta Spasial
        </a>
    </div>

    {{-- ============================================================ --}}
    {{-- KPI CARDS PER KATEGORI --}}
    {{-- ============================================================ --}}
    <div class="row mb-4">
        {{-- Total --}}
        <div class="col-6 col-md-2 mb-3">
            <a href="{{ route('master-data.infrastruktur') }}"
               class="md-kpi-card md-kpi-total {{ $categoryFilter === 'all' ? 'md-kpi-active' : '' }} h-100 text-decoration-none d-block">
                <div class="md-kpi-icon-wrap md-kpi-icon-total">
                    <i class="fas fa-database"></i>
                </div>
                <div class="md-kpi-value">{{ $totalApproved }}</div>
                <div class="md-kpi-label">Semua Data</div>
            </a>
        </div>

        @php
            $catDefs = [
                ['key' => 'DIR Baku',        'icon' => 'fa-map',           'color' => 'baku',    'dot' => '#27ae60'],
                ['key' => 'DIR Fungsional',  'icon' => 'fa-water',         'color' => 'fung',    'dot' => '#2980b9'],
                ['key' => 'DIR Potensial',   'icon' => 'fa-seedling',      'color' => 'pot',     'dot' => '#e67e22'],
                ['key' => 'Jaringan Saluran Irigasi', 'icon' => 'fa-route','color' => 'sal',    'dot' => '#0984e3'],
                ['key' => 'Bangunan Air',    'icon' => 'fa-building',      'color' => 'bang',    'dot' => '#d63031'],
            ];
        @endphp

        @foreach($catDefs as $cat)
        <div class="col-6 col-md-2 mb-3">
            <a href="{{ route('master-data.infrastruktur', ['category' => $cat['key']]) }}"
               class="md-kpi-card h-100 text-decoration-none d-block {{ $categoryFilter === $cat['key'] ? 'md-kpi-active' : '' }}"
               style="border-top: 3px solid {{ $cat['dot'] }};">
                <div class="md-kpi-icon-wrap" style="background: {{ $cat['dot'] }}18; color: {{ $cat['dot'] }};">
                    <i class="fas {{ $cat['icon'] }}"></i>
                </div>
                <div class="md-kpi-value">{{ $stats[$cat['key']] ?? 0 }}</div>
                <div class="md-kpi-label">{{ $cat['key'] }}</div>
            </a>
        </div>
        @endforeach
    </div>

    {{-- ============================================================ --}}
    {{-- TABEL DATA --}}
    {{-- ============================================================ --}}
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap"
             style="border-bottom: 1px solid #f1f5f9; gap: 10px;">
            <h6 class="m-0 font-weight-bold text-dark">
                <i class="fas fa-table text-secondary mr-2"></i>
                Daftar Berkas Spasial
                @if($categoryFilter !== 'all')
                    <span class="md-active-filter-chip ml-2">{{ $categoryFilter }}</span>
                @endif
            </h6>
            <small class="text-muted">
                Menampilkan <strong>{{ $layers->firstItem() ?? 0 }}</strong>
                &ndash; <strong>{{ $layers->lastItem() ?? 0 }}</strong>
                dari <strong>{{ $layers->total() }}</strong> berkas
            </small>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="pl-4 py-3" width="4%">No.</th>
                            <th class="py-3" width="28%">Nama Objek Spasial</th>
                            <th class="py-3" width="18%">Kategori</th>
                            <th class="py-3" width="14%">Pengunggah</th>
                            <th class="py-3" width="14%">Diverifikasi Oleh</th>
                            <th class="py-3" width="12%">Ukuran Berkas</th>
                            <th class="py-3 pr-4" width="10%">Tanggal Disetujui</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($layers as $index => $layer)
                        <tr>
                            <td class="pl-4 text-center text-muted font-weight-bold">
                                {{ $layers->firstItem() + $index }}
                            </td>
                            <td class="font-weight-bold text-dark">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-file-code text-primary mr-2 mt-1" style="flex-shrink:0;"></i>
                                    <div>
                                        {{ $layer->nama_objek }}
                                        <small class="text-muted d-block" style="font-family: monospace; font-size: 11px;">
                                            {{ $layer->filename }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $dotColor = $categoryColors[$layer->category] ?? '#64748b';
                                @endphp
                                <span class="md-category-chip" style="border-color: {{ $dotColor }}20; color: {{ $dotColor }}; background: {{ $dotColor }}15;">
                                    <span class="md-cat-dot" style="background: {{ $dotColor }};"></span>
                                    {{ $layer->category }}
                                </span>
                            </td>
                            <td class="text-muted">
                                {{ $layer->user ? $layer->user->name : 'Staf GIS' }}
                            </td>
                            <td class="text-muted">
                                {{ $layer->reviewer ? $layer->reviewer->name : 'Super Admin' }}
                            </td>
                            <td class="text-muted small">
                                {{ $layer->file_size ?? '-' }}
                            </td>
                            <td class="pr-4 text-muted small">
                                <i class="fas fa-calendar-check mr-1 text-success"></i>
                                {{ $layer->reviewed_at ? $layer->reviewed_at->format('d M Y') : '-' }}
                                <small class="d-block text-muted" style="font-size: 10.5px;">
                                    {{ $layer->reviewed_at ? $layer->reviewed_at->format('H:i') : '' }}
                                </small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="p-0 border-0">
                                <div class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block" style="opacity:.25;"></i>
                                    <div class="font-weight-bold">Belum Ada Data Infrastruktur</div>
                                    <small>Berkas spasial yang telah disetujui akan tampil di sini.</small>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination Footer --}}
        @if($layers->hasPages() || $layers->total() > 0)
        <div class="card-footer bg-white py-3 d-flex justify-content-between align-items-center flex-wrap"
             style="border-top: 1px solid #eaecf0; border-radius: 0 0 12px 12px; gap: 10px;">
            <small class="text-muted">
                Menampilkan <strong>{{ $layers->firstItem() ?? 0 }}</strong> &ndash; <strong>{{ $layers->lastItem() ?? 0 }}</strong>
                dari total <strong>{{ number_format($layers->total()) }}</strong> berkas spasial
            </small>

            @if($layers->hasPages())
            <div class="bwsmd-pagination">
                @if($layers->onFirstPage())
                    <span class="bwsmd-pg-btn disabled">&laquo;</span>
                @else
                    <a href="{{ $layers->previousPageUrl() }}" class="bwsmd-pg-btn">&laquo;</a>
                @endif

                @php
                    $cur   = $layers->currentPage();
                    $last  = $layers->lastPage();
                    $win   = 2;
                    $pStart = max(1, $cur - $win);
                    $pEnd   = min($last, $cur + $win);
                @endphp

                @if($pStart > 1)
                    <a href="{{ $layers->url(1) }}" class="bwsmd-pg-btn">1</a>
                    @if($pStart > 2)<span class="bwsmd-pg-btn disabled">&hellip;</span>@endif
                @endif

                @for($p = $pStart; $p <= $pEnd; $p++)
                    @if($p === $cur)
                        <span class="bwsmd-pg-btn active">{{ $p }}</span>
                    @else
                        <a href="{{ $layers->url($p) }}" class="bwsmd-pg-btn">{{ $p }}</a>
                    @endif
                @endfor

                @if($pEnd < $last)
                    @if($pEnd < $last - 1)<span class="bwsmd-pg-btn disabled">&hellip;</span>@endif
                    <a href="{{ $layers->url($last) }}" class="bwsmd-pg-btn">{{ $last }}</a>
                @endif

                @if($layers->hasMorePages())
                    <a href="{{ $layers->nextPageUrl() }}" class="bwsmd-pg-btn">&raquo;</a>
                @else
                    <span class="bwsmd-pg-btn disabled">&raquo;</span>
                @endif
            </div>
            @endif
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<style>
/* ================================================================
   KPI CARDS
================================================================ */
.md-kpi-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 16px 12px;
    border-radius: 12px;
    background: #fff;
    border: 1.5px solid #e9ecef;
    box-shadow: 0 1px 4px rgba(0,0,0,.05);
    transition: all .2s ease;
    cursor: pointer;
    color: inherit;
}
.md-kpi-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,.10);
    transform: translateY(-2px);
    border-color: #94a3b8;
}
.md-kpi-active {
    border-color: #1d4ed8 !important;
    box-shadow: 0 0 0 3px rgba(29,78,216,.12), 0 4px 16px rgba(0,0,0,.08) !important;
}
.md-kpi-total { border-top: 3px solid #1d4ed8; }
.md-kpi-icon-total { background: #eff6ff; color: #1d4ed8; }

.md-kpi-icon-wrap {
    width: 42px; height: 42px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 17px;
    margin-bottom: 10px;
}
.md-kpi-value {
    font-size: 24px;
    font-weight: 800;
    color: #1e293b;
    line-height: 1.1;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.md-kpi-label {
    font-size: 11px;
    color: #64748b;
    font-weight: 500;
    margin-top: 4px;
    line-height: 1.3;
    font-family: 'Inter', sans-serif;
}

/* ================================================================
   CATEGORY CHIP
================================================================ */
.md-category-chip {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px;
    border-radius: 20px;
    border: 1.5px solid;
    font-size: 11.5px; font-weight: 600;
    white-space: nowrap;
    font-family: 'Inter', sans-serif;
}
.md-cat-dot {
    width: 7px; height: 7px;
    border-radius: 50%; flex-shrink: 0;
}
.md-active-filter-chip {
    display: inline-flex; align-items: center;
    background: #dbeafe; color: #1d4ed8;
    border: 1px solid #bfdbfe;
    padding: 2px 10px; border-radius: 20px;
    font-size: 11px; font-weight: 700;
    vertical-align: middle;
}

/* ================================================================
   PAGINATION
================================================================ */
.bwsmd-pagination {
    display: flex; align-items: center; gap: 4px; flex-wrap: wrap;
}
.bwsmd-pg-btn {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 36px; height: 36px; padding: 0 10px;
    border-radius: 8px; border: 1.5px solid #e2e8f0;
    background: #fff; color: #475569;
    font-size: 13px; font-weight: 600; cursor: pointer;
    text-decoration: none;
    transition: all .15s ease; line-height: 1;
    font-family: 'Inter', sans-serif;
}
.bwsmd-pg-btn:hover:not(.active):not(.disabled) {
    background: #f1f5f9; border-color: #94a3b8;
    color: #1e293b; text-decoration: none;
}
.bwsmd-pg-btn.active {
    background: #1d4ed8; border-color: #1d4ed8;
    color: #fff; font-weight: 700; cursor: default;
    box-shadow: 0 2px 6px rgba(29,78,216,.35);
}
.bwsmd-pg-btn.disabled {
    opacity: .4; cursor: not-allowed;
    pointer-events: none; background: #f8fafc;
}
</style>
@endpush
