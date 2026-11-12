@extends('layout.master')

@section('title', 'Dashboard Analitik - WebGIS BWS Kalimantan I')

@section('konten')
<div class="container-fluid px-3 py-2">

    {{-- ============================================================
         1. HERO WELCOME BANNER
         ============================================================ --}}
    <div class="dash-hero-card mb-4">
        <div class="dash-hero-content">
            <div class="dash-hero-badge">
                <i class="fas fa-chart-line mr-1 text-warning"></i> Monitoring &amp; Analitik Spasial
            </div>
            <h1 class="dash-hero-title">
                Dashboard WebGIS BWS Kalimantan I
            </h1>
            <p class="dash-hero-subtitle">
                Pusat data spasial dan inventarisasi infrastruktur Daerah Irigasi, Jaringan Saluran, dan Bangunan Air di Provinsi Kalimantan Barat.
            </p>
            <div class="dash-hero-actions">
                <a href="{{ route('peta') }}" class="btn btn-warning font-weight-bold text-dark px-4 py-2 shadow-sm mr-2" style="border-radius: 8px;">
                    <i class="fas fa-map-marked-alt mr-2"></i> Buka Peta Spasial Interaktif
                </a>
                <a href="{{ route('users.index') }}" class="btn btn-outline-light font-weight-bold px-3 py-2" style="border-radius: 8px;">
                    <i class="fas fa-users-cog mr-1"></i> Kelola Pengguna
                </a>
            </div>

            {{-- Status Command Center Real-Time --}}
            <div class="dash-hero-status-bar d-flex flex-wrap align-items-center mt-3 pt-2" style="border-top: 1px solid rgba(255,255,255,0.18); gap: 14px; font-size: 11.5px; color: rgba(255,255,255,0.92);">
                <span><i class="fas fa-circle text-success mr-1.5" style="font-size: 8px;"></i> Status: <b>Sistem Terhubung &amp; Aktif</b></span>
                <span><i class="fas fa-calendar-check mr-1.5 text-warning"></i> Sinkronisasi: <b>{{ date('d M Y') }}</b></span>
                <span><i class="fas fa-map-marked-alt mr-1.5 text-info"></i> Wilayah Kerja: <b>14 Kab/Kota Kalbar</b></span>
            </div>
        </div>
        <div class="dash-hero-illustration d-none d-lg-flex">
            <div class="dash-hero-circle">
                <img src="{{ asset('sbadmin/img/logo-pu.jpeg') }}" alt="Logo PUPR" class="dash-hero-logo">
            </div>
        </div>
    </div>

    {{-- ============================================================
         2. KPI SUMMARY METRIC CARDS
         ============================================================ --}}
    <div class="row mb-4">

        {{-- Card 1: Total File GIS --}}
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm dash-stat-card border-left-primary h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1" style="letter-spacing: 0.5px;">
                                Total Layer GIS
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">
                                {{ $totalGisFiles }} <span class="text-muted font-weight-normal" style="font-size: 13px;">Layer</span>
                            </div>
                            <small class="text-muted mt-1 d-block">
                                <i class="fas fa-database text-primary mr-1"></i> Data spasial SHP / GeoJSON
                            </small>
                        </div>
                        <div class="dash-stat-icon bg-primary-soft text-primary">
                            <i class="fas fa-layer-group"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: DIR Baku & Fungsional --}}
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm dash-stat-card border-left-success h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1" style="letter-spacing: 0.5px;">
                                DIR Baku &amp; Fungsional
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">
                                {{ $categoryCounts['baku'] + $categoryCounts['fungsional'] }} <span class="text-muted font-weight-normal" style="font-size: 13px;">Kawasan</span>
                            </div>
                            <small class="text-muted mt-1 d-block">
                                <span class="badge badge-success px-1.5 mr-1">{{ $categoryCounts['baku'] }} Baku</span>
                                <span class="badge badge-info px-1.5">{{ $categoryCounts['fungsional'] }} Fungsional</span>
                            </small>
                        </div>
                        <div class="dash-stat-icon bg-success-soft text-success">
                            <i class="fas fa-tint"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Estimasi Total Luasan Irigasi (Hektare) --}}
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm dash-stat-card border-left-warning h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1" style="letter-spacing: 0.5px;">
                                Est. Luas Daerah Irigasi
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">
                                38.450 <span class="text-muted font-weight-normal" style="font-size: 13px;">Hektare (Ha)</span>
                            </div>
                            <small class="text-muted mt-1 d-block">
                                <span class="badge badge-warning text-dark px-1.5 mr-1 font-weight-bold" style="background:#fef3c7; color:#92400e;">Rekap BWS</span>
                                <span class="badge badge-light border px-1.5" style="font-size:10px;">Kalimantan Barat</span>
                            </small>
                        </div>
                        <div class="dash-stat-icon bg-warning-soft text-warning">
                            <i class="fas fa-vector-square"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Fisik SDA (Saluran & Bangunan) --}}
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm dash-stat-card border-left-info h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1" style="letter-spacing: 0.5px;">
                                Jaringan &amp; Bangunan Air
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">
                                {{ $categoryCounts['saluran'] + $categoryCounts['bangunan'] }} <span class="text-muted font-weight-normal" style="font-size: 13px;">Aset SDA</span>
                            </div>
                            <small class="text-muted mt-1 d-block">
                                <span class="badge badge-primary px-1.5 mr-1">{{ $categoryCounts['saluran'] }} Saluran</span>
                                <span class="badge badge-danger px-1.5">{{ $categoryCounts['bangunan'] }} Bangunan</span>
                            </small>
                        </div>
                        <div class="dash-stat-icon bg-info-soft text-info">
                            <i class="fas fa-archway"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ============================================================
         3. GRAFIK & ANALITIK INTERAKTIF (CHART.JS) DENGAN FILTER WILAYAH
         ============================================================ --}}
    <div class="row mb-4">

        {{-- Grafik 1: Komposisi Kategori Spasial (Doughnut Chart) + FILTER INTERAKTIF --}}
        <div class="col-xl-5 col-lg-6 mb-4 mb-xl-0">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 14px;">
                <div class="card-header bg-white py-3 d-flex flex-wrap align-items-center justify-content-between" style="border-radius: 14px 14px 0 0; border-bottom: 1px solid #f1f5f9; gap: 8px;">
                    <div>
                        <h6 class="m-0 font-weight-bold text-dark">
                            <i class="fas fa-chart-pie text-primary mr-2"></i>Komposisi Kategori SDA
                        </h6>
                        <small class="text-muted">Distribusi layer berdasarkan kategori</small>
                    </div>
                    {{-- Dropdown Filter Interaktif Per Wilayah --}}
                    <div class="d-flex align-items-center" style="gap: 6px;">
                        <select id="filterWilayahChart" class="custom-select custom-select-sm" style="font-size: 11.5px; border-radius: 6px; width: 140px; font-weight: 600;">
                            <option value="Semua">Semua Wilayah</option>
                            <option value="Kab. Sambas">Kab. Sambas</option>
                            <option value="Kab. Ketapang">Kab. Ketapang</option>
                            <option value="Kab. Kubu Raya">Kab. Kubu Raya</option>
                            <option value="Kab. Mempawah & Kota Pontianak">Mempawah &amp; Pnk</option>
                            <option value="Kab. Bengkayang & Lainnya">Bengkayang dll</option>
                        </select>
                    </div>
                </div>
                <div class="card-body p-3 d-flex flex-column justify-content-center">
                    <div style="position: relative; height: 260px; width: 100%;">
                        <canvas id="chartKategori"></canvas>
                        {{-- Overlay Center Text di Tengah Donut Chart --}}
                        <div class="chart-donut-center" id="donutCenterInfo">
                            <div class="donut-center-number" id="donutCenterTotal">{{ $totalGisFiles }}</div>
                            <div class="donut-center-label">Total Layer</div>
                        </div>
                    </div>
                    <div class="mt-3 pt-2 border-top d-flex flex-wrap justify-content-center" id="legendKategoriPills" style="gap: 8px 14px; font-size: 11.5px;">
                        <span><i class="fas fa-circle mr-1" style="color: #27ae60;"></i> Baku (<b id="lblBaku">{{ $categoryCounts['baku'] }}</b>)</span>
                        <span><i class="fas fa-circle mr-1" style="color: #2980b9;"></i> Fungsional (<b id="lblFungsional">{{ $categoryCounts['fungsional'] }}</b>)</span>
                        <span><i class="fas fa-circle mr-1" style="color: #e67e22;"></i> Potensial (<b id="lblPotensial">{{ $categoryCounts['potensial'] }}</b>)</span>
                        <span><i class="fas fa-circle mr-1" style="color: #0984e3;"></i> Saluran (<b id="lblSaluran">{{ $categoryCounts['saluran'] }}</b>)</span>
                        <span><i class="fas fa-circle mr-1" style="color: #d63031;"></i> Bangunan (<b id="lblBangunan">{{ $categoryCounts['bangunan'] }}</b>)</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Grafik 2: Sebaran per Wilayah / Kabupaten (Bar Chart) --}}
        <div class="col-xl-7 col-lg-6 mb-4 mb-xl-0">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 14px;">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between" style="border-radius: 14px 14px 0 0; border-bottom: 1px solid #f1f5f9;">
                    <div>
                        <h6 class="m-0 font-weight-bold text-dark">
                            <i class="fas fa-chart-bar text-success mr-2"></i>Sebaran Data Spasial per Wilayah
                        </h6>
                        <small class="text-muted">Inventarisasi Daerah Irigasi &amp; Fasilitas di Kalimantan Barat</small>
                    </div>
                    <a href="{{ route('peta') }}" class="btn btn-sm btn-outline-primary" style="border-radius: 6px; font-size: 11.5px;">
                        <i class="fas fa-external-link-alt mr-1"></i> Buka di Peta
                    </a>
                </div>
                <div class="card-body p-3">
                    <div style="position: relative; height: 280px; width: 100%;">
                        <canvas id="chartWilayah"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ============================================================
         4. ROW BAWAH: DATA TERKINI & AKTIVITAS AUDIT LOG
         ============================================================ --}}
    <div class="row">

        {{-- Tabel Ringkas Data Spasial Terkini --}}
        <div class="col-lg-7 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 14px;">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between" style="border-radius: 14px 14px 0 0; border-bottom: 1px solid #f1f5f9;">
                    <h6 class="m-0 font-weight-bold text-dark">
                        <i class="fas fa-file-code text-primary mr-2"></i>Katalog File GIS Terbaru
                    </h6>
                    <a href="{{ route('peta') }}" class="small font-weight-bold text-primary">Lihat Semua di Peta &rarr;</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 12.5px;">
                            <thead class="bg-light text-muted text-uppercase" style="font-size: 11px;">
                                <tr>
                                    <th class="pl-3 py-2.5">Nama Objek Spasial</th>
                                    <th class="py-2.5">Kategori</th>
                                    <th class="py-2.5">Ukuran</th>
                                    <th class="pr-3 text-right py-2.5">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentFiles as $rf)
                                    @php
                                        $catColor = 'secondary';
                                        $catName = 'Custom';
                                        $uName = strtoupper($rf['name']);
                                        if (str_starts_with($uName, 'BAKU'))       { $catColor = 'success'; $catName = 'Baku'; }
                                        elseif (str_starts_with($uName, 'FUNGSIONAL')) { $catColor = 'info'; $catName = 'Fungsional'; }
                                        elseif (str_starts_with($uName, 'POTENSIAL'))  { $catColor = 'warning'; $catName = 'Potensial'; }
                                        elseif (str_starts_with($uName, 'SALURAN'))    { $catColor = 'primary'; $catName = 'Saluran'; }
                                        elseif (str_starts_with($uName, 'BANGUNAN'))   { $catColor = 'danger'; $catName = 'Bangunan'; }
                                    @endphp
                                    <tr>
                                        <td class="pl-3 font-weight-bold text-dark">
                                            <i class="fas fa-map-pin mr-1.5 text-muted"></i>
                                            {{ str_replace(['.json', '.geojson', '_'], ['', '', ' '], $rf['name']) }}
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $catColor }} px-2 py-1 font-weight-semibold" style="border-radius: 6px;">
                                                {{ $catName }}
                                            </span>
                                        </td>
                                        <td class="text-muted">{{ $rf['size'] }}</td>
                                        <td class="pr-3 text-right">
                                            <a href="{{ route('peta') }}" class="btn btn-xs btn-outline-primary px-2 py-1" style="border-radius: 6px; font-size: 11px;" title="Buka di Peta">
                                                <i class="fas fa-search-location mr-1"></i> Lihat
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Belum ada file GIS yang tersedia.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Log Aktivitas Terkini --}}
        <div class="col-lg-5 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 14px;">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between" style="border-radius: 14px 14px 0 0; border-bottom: 1px solid #f1f5f9;">
                    <h6 class="m-0 font-weight-bold text-dark">
                        <i class="fas fa-history text-danger mr-2"></i>Aktivitas Sistem Terkini
                    </h6>
                    @if(Auth::check() && Auth::user()->role === 'superadmin')
                        <a href="{{ route('activity-logs.index') }}" class="small font-weight-bold text-primary">Lihat Log Lengkap &rarr;</a>
                    @endif
                </div>
                <div class="card-body p-3">
                    <div class="dash-activity-timeline">
                        @forelse($recentLogs as $log)
                            <div class="dash-timeline-item">
                                <div class="dash-timeline-badge bg-{{ $log->badge_class ?? 'primary' }}">
                                    <i class="fas fa-bolt"></i>
                                </div>
                                <div class="dash-timeline-info">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="font-weight-bold text-dark" style="font-size: 12.5px;">{{ $log->user_name ?? 'Sistem' }}</span>
                                        <small class="text-muted" style="font-size: 11px;">{{ $log->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="text-muted mb-0 mt-0.5" style="font-size: 12px; line-height: 1.35;">
                                        {{ $log->description ?? $log->action }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center py-4 mb-0" style="font-size: 12px;">Belum ada aktivitas tercatat.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

{{-- ============================================================
     CSS KHUSUS DASHBOARD ANALITIK
     ============================================================ --}}
<style>
    /* Hero Welcome Banner */
    .dash-hero-card {
        background: linear-gradient(135deg, #0f2e59 0%, #1e3a8a 55%, #1d4ed8 100%);
        border-radius: 18px;
        padding: 32px 36px;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(15, 46, 89, 0.25);
    }
    .dash-hero-card::before {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 260px;
        height: 260px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(251, 191, 36, 0.22) 0%, transparent 70%);
        pointer-events: none;
    }
    .dash-hero-content {
        max-width: 640px;
        position: relative;
        z-index: 2;
    }
    .dash-hero-badge {
        display: inline-block;
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.25);
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 11.5px;
        font-weight: 600;
        margin-bottom: 12px;
        letter-spacing: 0.3px;
    }
    .dash-hero-title {
        font-size: 26px;
        font-weight: 800;
        letter-spacing: -0.3px;
        margin-bottom: 8px;
        color: #ffffff;
    }
    .dash-hero-subtitle {
        font-size: 13.5px;
        color: rgba(255, 255, 255, 0.88);
        line-height: 1.5;
        margin-bottom: 20px;
    }
    .dash-hero-circle {
        width: 100px;
        height: 100px;
        background: #ffffff;
        border-radius: 24px;
        padding: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.3);
        flex-shrink: 0;
    }
    .dash-hero-logo {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    /* KPI Cards */
    .dash-stat-card {
        border-radius: 14px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .dash-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08) !important;
    }
    .border-left-primary { border-left: 4px solid #1e3a8a !important; }
    .border-left-success { border-left: 4px solid #10b981 !important; }
    .border-left-info    { border-left: 4px solid #0284c7 !important; }
    .border-left-warning { border-left: 4px solid #f59e0b !important; }

    .dash-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .bg-primary-soft { background: #eff6ff; }
    .bg-success-soft { background: #ecfdf5; }
    .bg-info-soft    { background: #f0f9ff; }
    .bg-warning-soft { background: #fffbeb; }

    /* Timeline Log */
    .dash-activity-timeline {
        position: relative;
        padding-left: 20px;
    }
    .dash-activity-timeline::before {
        content: '';
        position: absolute;
        top: 6px;
        bottom: 6px;
        left: 7px;
        width: 2px;
        background: #e2e8f0;
    }
    .dash-timeline-item {
        position: relative;
        margin-bottom: 18px;
    }
    .dash-timeline-item:last-child {
        margin-bottom: 0;
    }
    .dash-timeline-badge {
        position: absolute;
        left: -20px;
        top: 2px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 8px;
        box-shadow: 0 0 0 3px #ffffff;
    }
    .dash-timeline-info {
        padding-left: 6px;
    }

    /* Center Text di Lubang Tengah Donut Chart */
    .chart-donut-center {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        pointer-events: none;
        user-select: none;
    }
    .donut-center-number {
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
        letter-spacing: -0.5px;
    }
    .donut-center-label {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin-top: 3px;
    }
</style>

{{-- ============================================================
     SCRIPT CHART.JS & MINI-MAP LEAFLET
     ============================================================ --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. DOUGHNUT CHART: Komposisi Kategori Spasial
    var ctxKat = document.getElementById('chartKategori');
    var doughnutChart = null;
    if (ctxKat) {
        doughnutChart = new Chart(ctxKat, {
            type: 'doughnut',
            data: {
                labels: ['DIR Baku', 'DIR Fungsional', 'DIR Potensial', 'Jaringan Saluran', 'Bangunan Air', 'Lainnya'],
                datasets: [{
                    data: [
                        {{ $categoryCounts['baku'] }},
                        {{ $categoryCounts['fungsional'] }},
                        {{ $categoryCounts['potensial'] }},
                        {{ $categoryCounts['saluran'] }},
                        {{ $categoryCounts['bangunan'] }},
                        {{ $categoryCounts['custom'] }}
                    ],
                    backgroundColor: ['#27ae60', '#2980b9', '#e67e22', '#0984e3', '#d63031', '#8e44ad'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var val = context.raw || 0;
                                var dataset = context.dataset.data;
                                var total = dataset.reduce(function(a, b) { return a + b; }, 0) || 1;
                                var pct = Math.round((val / total) * 100);
                                return ' ' + context.label + ': ' + val + ' Layer (' + pct + '%)';
                            }
                        }
                    }
                },
                cutout: '68%'
            }
        });

        // Handler Filter Interaktif Per-Wilayah
        var filterEl = document.getElementById('filterWilayahChart');
        var wilayahData = @json($wilayahCategoryData);
        if (filterEl) {
            filterEl.addEventListener('change', function() {
                var selected = this.value;
                var dataObj = (selected === 'Semua') ? @json($categoryCounts) : (wilayahData[selected] || @json($categoryCounts));
                doughnutChart.data.datasets[0].data = [
                    dataObj.baku || 0,
                    dataObj.fungsional || 0,
                    dataObj.potensial || 0,
                    dataObj.saluran || 0,
                    dataObj.bangunan || 0,
                    dataObj.custom || 0
                ];
                doughnutChart.update();

                // Update Total di Tengah Lubang Donat
                var sum = (dataObj.baku || 0) + (dataObj.fungsional || 0) + (dataObj.potensial || 0) + (dataObj.saluran || 0) + (dataObj.bangunan || 0) + (dataObj.custom || 0);
                var cntCenter = document.getElementById('donutCenterTotal');
                if (cntCenter) cntCenter.textContent = sum;

                var b = document.getElementById('lblBaku');
                var f = document.getElementById('lblFungsional');
                var p = document.getElementById('lblPotensial');
                var s = document.getElementById('lblSaluran');
                var bg = document.getElementById('lblBangunan');
                if (b) b.textContent = dataObj.baku || 0;
                if (f) f.textContent = dataObj.fungsional || 0;
                if (p) p.textContent = dataObj.potensial || 0;
                if (s) s.textContent = dataObj.saluran || 0;
                if (bg) bg.textContent = dataObj.bangunan || 0;
            });
        }
    }

    // 2. BAR CHART: Sebaran per Wilayah / Kabupaten (Warna Berbeda Per Kabupaten)
    var ctxWil = document.getElementById('chartWilayah');
    if (ctxWil) {
        new Chart(ctxWil, {
            type: 'bar',
            data: {
                labels: [
                    'Kab. Sambas',
                    'Kab. Ketapang',
                    'Kab. Kubu Raya',
                    'Mempawah & Pontianak',
                    'Bengkayang & Lainnya'
                ],
                datasets: [{
                    label: 'Jumlah Objek & Layer Spasial',
                    data: [
                        {{ $wilayahCounts['Kab. Sambas'] }},
                        {{ $wilayahCounts['Kab. Ketapang'] }},
                        {{ $wilayahCounts['Kab. Kubu Raya'] }},
                        {{ $wilayahCounts['Kab. Mempawah & Kota Pontianak'] }},
                        {{ $wilayahCounts['Kab. Bengkayang & Lainnya'] }}
                    ],
                    backgroundColor: [
                        '#10b981', // Sambas (Emerald Green)
                        '#3b82f6', // Ketapang (Royal Blue)
                        '#f59e0b', // Kubu Raya (Amber Gold)
                        '#06b6d4', // Mempawah & Pontianak (Cyan)
                        '#8b5cf6'  // Bengkayang & Lainnya (Violet)
                    ],
                    borderColor: [
                        '#059669',
                        '#2563eb',
                        '#d97706',
                        '#0891b2',
                        '#7c3aed'
                    ],
                    borderWidth: 1.5,
                    borderRadius: 8,
                    maxBarThickness: 46
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 2, font: { size: 11 } },
                        grid: { color: 'rgba(226, 232, 240, 0.6)' }
                    },
                    x: {
                        ticks: { font: { size: 11 } },
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.parsed.y + ' Data Spasial';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection

