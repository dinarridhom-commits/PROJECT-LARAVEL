<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title> BWS KALIMANTAN I - Peta Daerah Irigasi (DIR)</title>

    <!-- Google Fonts: Inter & Nunito -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome Free Icons -->
    <link href="{{ asset('sbadmin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">

    <!-- Bootstrap 4 CSS -->
    <link href="{{ asset('sbadmin/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>

    <style>
        :root {
            --bws-primary: #1d4ed8;       /* Royal Blue BWS */
            --bws-primary-dark: #1e40af;
            --bws-primary-light: #3b82f6;
            --bws-accent-yellow: #f59e0b; /* Kuning Emas PU */
            --bws-navy-header: #1e293b;   /* Dark Slate/Navy Header Tabel */
            --bws-bg: #f8fafc;            /* Background abu sangat bersih */
            --bws-card: #ffffff;
            --bws-border: #e2e8f0;
            --bws-text-main: #1e293b;
            --bws-text-muted: #64748b;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--bws-bg);
            color: var(--bws-text-main);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* ========================================================
           1. TOPBAR HEADER RESMI BWS KALIMANTAN I
           ======================================================== */
        .bws-navbar {
            background-color: var(--bws-primary);
            position: sticky;
            top: 0;
            z-index: 1030;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.12);
        }

        .bws-navbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 24px;
            min-height: 72px;
        }

        .bws-nav-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        /* Tombol Reset Tampilan Peta (Lingkaran Putih <) */
        .bws-back-btn {
            width: 42px;
            height: 42px;
            background: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1e293b;
            text-decoration: none;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            transition: all 0.2s ease;
            font-size: 16px;
            border: none;
            cursor: pointer;
        }

        .bws-back-btn:hover {
            background: #f1f5f9;
            color: var(--bws-primary);
            transform: scale(1.05);
            text-decoration: none;
        }

        /* Logo PU & Identitas Instansi */
        .bws-brand-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #ffffff;
        }

        .bws-logo-pu {
            width: 44px;
            height: 44px;
            object-fit: cover;
            border-radius: 8px;
            background: #ffffff;
            padding: 2px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .bws-brand-text {
            line-height: 1.25;
        }

        .bws-brand-title {
            font-weight: 800;
            font-size: 15px;
            letter-spacing: 0.5px;
            color: #ffffff;
            text-transform: uppercase;
        }

        .bws-brand-subtitle {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.95);
            font-weight: 500;
        }

        .bws-brand-dept {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.82);
            font-weight: 400;
        }

        /* Nav Right Items */
        .bws-nav-right {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .bws-header-btn {
            background: transparent;
            border: none;
            color: #ffffff;
            font-size: 18px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }

        .bws-header-btn:hover {
            background: rgba(255, 255, 255, 0.16);
            color: #ffffff;
        }

        .bws-badge-pill {
            position: absolute;
            top: 2px;
            right: 2px;
            background: #ef4444;
            color: #ffffff;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 10px;
            line-height: 1;
            border: 1.5px solid var(--bws-primary);
        }

        /* Profile Pill Header (Admin Dinar) */
        .bws-profile-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(4px);
            padding: 5px 14px 5px 6px;
            border-radius: 50px;
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.25);
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 600;
            font-size: 13px;
        }

        .bws-profile-pill:hover {
            background: rgba(255, 255, 255, 0.28);
            color: #ffffff;
            text-decoration: none;
        }

        .bws-profile-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--bws-primary);
            overflow: hidden;
            font-size: 15px;
        }

        /* Garis Aksen Emas PU di Bawah Navbar */
        .bws-accent-strip {
            height: 4px;
            background: linear-gradient(90deg, #f59e0b 0%, #fbbf24 50%, #f59e0b 100%);
            width: 100%;
        }

        /* ========================================================
           2. MAIN CONTAINER & SIDEBAR FILTER (Layout 2 Kolom)
           ======================================================== */
        .bws-page-container {
            display: flex;
            min-height: calc(100vh - 76px);
            position: relative;
        }

        /* Left Filter Sidebar */
        .bws-sidebar {
            width: 290px;
            min-width: 290px;
            max-width: 290px;
            background: var(--bws-card);
            border-right: 1px solid var(--bws-border);
            padding: 24px 20px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .bws-filter-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .bws-filter-label {
            font-size: 13px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 0;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .bws-select-control, .bws-input-control {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 9px 12px;
            font-size: 13px;
            color: #1e293b;
            background-color: #ffffff;
            font-weight: 500;
            height: auto;
            transition: all 0.2s ease;
        }

        .bws-select-control:focus, .bws-input-control:focus {
            border-color: var(--bws-primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
            outline: none;
        }

        .bws-btn-block {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 10px;
        }

        .bws-btn-action {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        /* Right Content Area */
        .bws-content {
            flex: 1;
            min-width: 0;
            padding: 20px 24px 40px 24px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Breadcrumb */
        .bws-breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
        }

        .bws-breadcrumb-parent {
            color: #64748b;
            font-weight: 600;
        }

        .bws-breadcrumb-arrow {
            font-size: 14px;
            color: #94a3b8;
        }

        .bws-breadcrumb-current {
            color: #1e293b;
        }

        /* ========================================================
           3. MAP CONTAINER & KONTROL
           ======================================================== */
        .bws-map-card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid var(--bws-border);
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
            overflow: hidden;
            position: relative;
            transition: all 0.3s ease;
        }

        #map {
            width: 100%;
            height: 480px;
            z-index: 1;
            background: #e2e8f0;
        }

        /* Status Loading Overlay Peta */
        .bws-map-loading {
            position: absolute;
            top: 14px;
            right: 14px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(4px);
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
            color: var(--bws-primary);
            box-shadow: 0 2px 8px rgba(0,0,0,0.12);
            z-index: 999;
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(0,0,0,0.08);
        }

        /* ========================================================
           4. DATA TABLE SECTION (Header Gelap / Dark Slate)
           ======================================================== */
        .bws-table-card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid var(--bws-border);
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);
            overflow: hidden;
        }

        .bws-table-toolbar {
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
            border-bottom: 1px solid var(--bws-border);
            background: #ffffff;
        }

        .bws-table-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .bws-table-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .bws-search-input-wrap {
            position: relative;
            min-width: 240px;
        }

        .bws-search-input-wrap i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 13px;
        }

        .bws-search-input-wrap input {
            padding: 8px 12px 8px 34px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 13px;
            width: 100%;
            outline: none;
            transition: all 0.2s ease;
        }

        .bws-search-input-wrap input:focus {
            border-color: var(--bws-primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .btn-export-green {
            background-color: #10b981;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25);
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .btn-export-green:hover {
            background-color: #059669;
            color: #ffffff;
            transform: translateY(-1px);
        }

        /* TABEL DENGAN HEADER GELAP (DARK SLATE) */
        .bws-table-responsive {
            max-height: 540px;
            overflow-y: auto;
            overflow-x: auto;
        }

        .bws-custom-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 0;
        }

        .bws-custom-table thead tr th {
            background-color: var(--bws-navy-header) !important;
            color: #ffffff !important;
            font-size: 12.5px;
            font-weight: 700;
            letter-spacing: 0.3px;
            padding: 13px 14px;
            border: none;
            position: sticky;
            top: 0;
            z-index: 10;
            white-space: nowrap;
        }

        .bws-custom-table tbody tr {
            transition: background-color 0.15s ease;
            border-bottom: 1px solid #f1f5f9;
        }

        .bws-custom-table tbody tr:hover {
            background-color: #f8fafc;
        }

        .bws-custom-table tbody tr:nth-child(even) {
            background-color: #fcfdfe;
        }

        .bws-custom-table tbody td {
            padding: 11px 14px;
            font-size: 13px;
            color: #334155;
            vertical-align: middle;
            border-top: 1px solid #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
        }

        .bws-table-footer {
            padding: 12px 20px;
            background: #ffffff;
            border-top: 1px solid var(--bws-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12.5px;
            color: #64748b;
        }

        /* Badges */
        .badge-baku { background: #dcfce7; color: #166534; font-weight: 700; border: 1px solid #bbf7d0; }
        .badge-fungsional { background: #dbeafe; color: #1e40af; font-weight: 700; border: 1px solid #bfdbfe; }
        .badge-potensial { background: #ffedd5; color: #9a3412; font-weight: 700; border: 1px solid #fed7aa; }
        .badge-saluran { background: #cffafe; color: #155e75; font-weight: 700; border: 1px solid #a5f3fc; }
        .badge-bangunan { background: #fee2e2; color: #991b1b; font-weight: 700; border: 1px solid #fecaca; }

        /* Tombol Fokus */
        .btn-focus-row {
            background: #ffffff;
            color: var(--bws-primary);
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.15s ease;
            cursor: pointer;
        }

        .btn-focus-row:hover {
            background: var(--bws-primary);
            color: #ffffff;
            border-color: var(--bws-primary);
        }

        /* Leaflet Legend */
        .map-legend {
            background: rgba(255,255,255,0.96);
            padding: 10px 14px;
            border-radius: 10px;
            box-shadow: 0 4px 18px rgba(0,0,0,0.18);
            font-size: 12px;
            line-height: 1.6;
            max-width: 240px;
            border: 1px solid #e2e8f0;
        }
        .map-legend h6 {
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 8px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
            color: #1e293b;
        }
        .legend-item { display: flex; align-items: center; margin-bottom: 5px; gap: 8px; }
        .legend-color-box { width: 18px; height: 12px; border-radius: 3px; border: 1px solid rgba(0,0,0,0.2); flex-shrink: 0; }
        .legend-line { width: 20px; height: 4px; border-radius: 2px; flex-shrink: 0; }
        .legend-circle { width: 11px; height: 11px; border-radius: 50%; border: 1.5px solid #fff; box-shadow: 0 0 0 1px #999; flex-shrink: 0; }

        /* Responsif Layar Mobile */
        @media (max-width: 991.98px) {
            .bws-page-container {
                flex-direction: column;
            }
            .bws-sidebar {
                width: 100%;
                min-width: 100%;
                max-width: 100%;
                border-right: none;
                border-bottom: 1px solid var(--bws-border);
            }
            .bws-content {
                padding: 16px;
            }
            .bws-brand-title {
                font-size: 13px;
            }
            .bws-brand-subtitle, .bws-brand-dept {
                display: none;
            }
        }
    </style>
</head>
<body>

    <!-- 1. TOPBAR NAVBAR RESMI BWS KALIMANTAN I -->
    <header class="bws-navbar">
        <div class="bws-navbar-inner">
            <div class="bws-nav-left">
                <!-- Tombol Reset / Home Tampilan Peta (Lingkaran Putih <) -->
                <button type="button" class="bws-back-btn" onclick="resetMapView()" title="Reset Tampilan Peta ke Awal">
                    <i class="fas fa-chevron-left"></i>
                </button>

                <!-- Logo PU & Teks Instansi -->
                <div class="bws-brand-wrapper">
                    <img src="{{ asset('sbadmin/img/logo-pu.jpeg') }}" alt="Logo Kementerian PU" class="bws-logo-pu">
                    <div class="bws-brand-text">
                        <div class="bws-brand-title">FEWS BWS KALIMANTAN I</div>
                        <div class="bws-brand-subtitle">Direktorat Jenderal Sumber Daya Air</div>
                        <div class="bws-brand-dept">Kementerian Pekerjaan Umum</div>
                    </div>
                </div>
            </div>

            <div class="bws-nav-right">
                <!-- Tombol Fullscreen -->
                <button type="button" class="bws-header-btn" id="btnFullscreenToggle" title="Tampilan Layar Penuh">
                    <i class="fas fa-expand"></i>
                </button>

                <!-- Tombol Notifikasi (Total File GIS Aktif) -->
                <div class="position-relative">
                    <button type="button" class="bws-header-btn" title="Total File GIS Tersimpan">
                        <i class="fas fa-bell"></i>
                        <span class="bws-badge-pill" id="header-file-count">{{ count($fileList ?? []) }}</span>
                    </button>
                </div>

                <!-- User Profile Pill (Admin Dinar) & Dropdown Bersih -->
                <div class="dropdown">
                    <div class="bws-profile-pill" id="userMenuDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="bws-profile-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <span class="font-weight-bold">{{ Auth::user()->name ?? 'Admin Dinar' }}</span>
                        <i class="fas fa-chevron-down ml-1 small text-white-50"></i>
                    </div>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in py-2" aria-labelledby="userMenuDropdown">
                        <div class="dropdown-header text-uppercase font-weight-bold text-xs text-muted">
                            Sistem WebGIS BWSK I
                        </div>
                        <div class="px-3 py-1 small text-dark font-weight-bold">
                            <i class="fas fa-user-circle mr-1 text-primary"></i> {{ Auth::user()->name ?? 'Admin Dinar' }}
                        </div>
                        <div class="px-3 pb-2 small text-muted">
                            Role: <span class="badge badge-primary">{{ strtoupper(Auth::user()->role ?? 'ADMIN') }}</span>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger py-2" href="#" data-toggle="modal" data-target="#logoutModal">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2"></i>Keluar / Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Garis Aksen Emas PU -->
        <div class="bws-accent-strip"></div>
    </header>

    <!-- 2. LAYOUT SINGLE-PAGE DASHBOARD (Sidebar Filter di Kiri + Konten Peta & Tabel di Kanan) -->
    <div class="bws-page-container">

        <!-- PANEL FILTER (SISI KIRI - TANPA FILTER KATEGORI) -->
        <aside class="bws-sidebar">
            <!-- 1. Dashboard Type -->
            <div class="bws-filter-group">
                <label class="bws-filter-label">Dashboard Type</label>
                <select class="form-control bws-select-control" id="filterDashboardType">
                    <option value="data" selected>Dashboard Data</option>
                </select>
            </div>

            <!-- 2. Filter Tanggal (Format: 16 October 2026) -->
            <div class="bws-filter-group">
                <label class="bws-filter-label">Filter Tanggal</label>
                <div class="input-group">
                    <input type="text" class="form-control bws-input-control bg-white" id="filterTanggal" value="{{ date('d F Y') }}" readonly>
                    <div class="input-group-append">
                        <span class="input-group-text bg-white border-left-0 text-muted"><i class="far fa-calendar-alt"></i></span>
                    </div>
                </div>
            </div>

            <!-- 3. View Mode (Table + Maps / Maps Only / Table Only) -->
            <div class="bws-filter-group">
                <label class="bws-filter-label">View</label>
                <select class="form-control bws-select-control" id="filterViewMode" onchange="switchViewMode(this.value)">
                    <option value="both" selected>Table + Maps</option>
                    <option value="maps">Maps Only</option>
                    <option value="table">Table Only</option>
                </select>
            </div>

            <!-- Tombol Kelola File & Upload GeoJSON -->
            <div class="bws-btn-block">
                <button type="button" class="btn btn-outline-primary bws-btn-action shadow-sm" data-toggle="modal" data-target="#modalKelolaGeojson">
                    <i class="fas fa-folder-open"></i>
                    <span>Kelola File GIS</span>
                    <span class="badge badge-primary ml-auto" id="sidebar-file-count">{{ count($fileList ?? []) }}</span>
                </button>
                <button type="button" class="btn btn-primary bws-btn-action shadow-sm text-white" data-toggle="modal" data-target="#modalUploadGeojson">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <span>Upload GeoJSON</span>
                </button>
            </div>
        </aside>

        <!-- AREA KONTEN UTAMA (SISI KANAN) -->
        <main class="bws-content">

            <!-- Breadcrumb Navigation -->
            <div class="bws-breadcrumb">
                <span class="bws-breadcrumb-parent">Dashboard Data</span>
                <i class="fas fa-chevron-right bws-breadcrumb-arrow"></i>
                <span class="bws-breadcrumb-current">Peta Daerah Irigasi (DIR)</span>
            </div>

            {{-- Flash Alert Notifikasi --}}
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm mb-0" role="alert">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-0" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            <!-- 1. MAP CARD (Leaflet Interactive Map) -->
            <div class="bws-map-card" id="section-map">
                <div class="bws-map-loading" id="map-loading">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>Memuat koordinat GIS ArcMap...</span>
                </div>
                <div id="map"></div>
            </div>

            <!-- 2. DATA TABLE CARD (Header Gelap / Dark Slate) -->
            <div class="bws-table-card" id="section-table">
                <div class="bws-table-toolbar">
                    <div class="bws-table-title">
                        <span>Daftar Pos Daerah Irigasi & Data Spasial</span>
                        <span class="badge badge-light border text-muted px-2 py-1" style="font-size:12px;" id="badge-total-file-tabel">
                            {{ count($fileList) }} File
                        </span>
                    </div>

                    <div class="bws-table-controls">
                        <!-- Pencarian Data Realtime -->
                        <div class="bws-search-input-wrap">
                            <i class="fas fa-search"></i>
                            <input type="text" id="cari-tabel-koordinat" placeholder="Cari nama pos/objek/file..." onkeyup="filterTabelKoordinat(this.value)">
                        </div>

                        <!-- Tombol Export Data Hijau -->
                        <button type="button" class="btn-export-green" onclick="exportDataToCsv()" title="Unduh data dalam format CSV/Excel">
                            <i class="fas fa-file-excel"></i>
                            <span>Export Data</span>
                        </button>
                    </div>
                </div>

                <!-- Tabel Data GIS (Dark Header) -->
                <div class="bws-table-responsive">
                    <table class="bws-custom-table" id="tabel-lokasi">
                        <thead>
                            <tr>
                                <th width="5%" class="text-center">No.</th>
                                <th width="28%">Nama Pos / Objek</th>
                                <th width="18%">Wilayah / Kategori</th>
                                <th width="12%" class="text-center">Latitude (Y)</th>
                                <th width="12%" class="text-center">Longitude (X)</th>
                                <th width="17%">Sumber File GIS</th>
                                <th width="8%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-lokasi">
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-spinner fa-spin text-primary mr-2"></i>Membaca data koordinat GIS ArcMap...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="bws-table-footer">
                    <span id="tabel-footer-info">Memuat data koordinat...</span>
                    <span><i class="fas fa-sync-alt text-success mr-1"></i>Otomatis sinkron dengan file yang diunggah</span>
                </div>
            </div>

        </main>
    </div>

    <!-- MODAL 1: UPLOAD GEOJSON (Dukungan Batch Chunked Upload Anti-PostTooLargeException) -->
    <div class="modal fade" id="modalUploadGeojson" tabindex="-1" role="dialog" aria-labelledby="modalUploadGeojsonLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form id="formUploadGeojson" action="{{ route('peta.upload-geojson') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold" id="modalUploadGeojsonLabel">
                        <i class="fas fa-cloud-upload-alt mr-2"></i>Upload File GeoJSON
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" id="btnCloseUploadModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-group mb-3" id="uploadInputContainer">
                        <label class="font-weight-bold text-gray-800">
                            <i class="fas fa-file-code text-primary mr-1"></i>Pilih File GeoJSON / JSON:
                        </label>
                        <div class="custom-file">
                            <input type="file" name="file_geojson[]" class="custom-file-input" id="inputGeojsonFile" multiple accept=".geojson,.json" required onchange="updateFileInputLabel(this)">
                            <label class="custom-file-label" for="inputGeojsonFile" id="labelGeojsonFile">Pilih satu atau beberapa file...</label>
                        </div>
                        <small class="form-text text-muted mt-2">
                            <i class="fas fa-info-circle mr-1"></i>Format yang didukung: <code>.geojson</code> atau <code>.json</code>. Anda dapat memilih banyak file sekaligus, otomatis diunggah bertahap tanpa error ukuran.
                        </small>
                    </div>

                    <div id="uploadProgressSection" class="mb-3" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="font-weight-bold small text-primary" id="uploadStatusText">
                                <i class="fas fa-spinner fa-spin mr-1"></i>Mengunggah file...
                            </span>
                            <span class="font-weight-bold small text-muted" id="uploadPercentText">0%</span>
                        </div>
                        <div class="progress" style="height: 18px; border-radius: 9px;">
                            <div id="uploadProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success font-weight-bold" role="progressbar" style="width: 0%; font-size: 11px;">0%</div>
                        </div>
                        <div class="small text-muted mt-2 d-flex justify-content-between" id="uploadDetailText">
                            <span>Memproses batch file...</span>
                            <span id="uploadBatchCountText">Batch 0/0</span>
                        </div>
                    </div>

                    <div id="uploadSuccessSection" class="alert alert-success py-2 px-3 small mb-3" style="display: none;">
                        <i class="fas fa-check-circle mr-1"></i><span id="uploadSuccessMessage">Semua file berhasil diunggah!</span>
                    </div>

                    <div id="uploadErrorSection" class="alert alert-danger py-2 px-3 small mb-3" style="display: none;">
                        <i class="fas fa-exclamation-triangle mr-1"></i><span id="uploadErrorMessage"></span>
                    </div>

                    <div class="alert alert-info py-2 px-3 small mb-0" id="uploadInfoNote">
                        <i class="fas fa-folder-open mr-1"></i>File yang diunggah akan langsung disimpan ke folder <code>public/geojson/</code> dan otomatis dimuat ke peta interaktif.
                    </div>
                </div>
                <div class="modal-footer bg-light" id="uploadModalFooter">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="btnBatalUpload">
                        <i class="fas fa-times mr-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary font-weight-bold" id="btnSubmitUpload">
                        <i class="fas fa-upload mr-1"></i>Upload Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: KELOLA & HAPUS FILE GEOJSON -->
    <div class="modal fade" id="modalKelolaGeojson" tabindex="-1" role="dialog" aria-labelledby="modalKelolaGeojsonLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title font-weight-bold" id="modalKelolaGeojsonLabel">
                        <i class="fas fa-folder-open text-warning mr-2"></i>Kelola File GIS (public/geojson)
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                        <div class="small text-muted mb-2 mb-sm-0 d-flex align-items-center flex-wrap gap-2">
                            <span>Total tersimpan: <span class="badge badge-primary px-2 py-1">{{ count($fileList ?? []) }} File GIS</span></span>
                            <span id="bulk-selected-info" class="ml-2" style="display:none;">
                                <span class="badge badge-warning text-dark px-2 py-1">
                                    <i class="fas fa-check-square mr-1"></i><span id="bulk-selected-count">0</span> Dipilih
                                </span>
                            </span>
                        </div>
                        <div class="w-50">
                            <input type="text" id="cari-file-geojson" class="form-control form-control-sm" placeholder="🔍 Cari nama file..." onkeyup="filterTabelFileGeojson(this.value)">
                        </div>
                    </div>

                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm table-hover table-bordered mb-0" id="tabel-kelola-geojson">
                            <thead class="thead-light">
                                <tr>
                                    <th width="5%" class="text-center">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="chk-select-all-geojson">
                                            <label class="custom-control-label" for="chk-select-all-geojson"></label>
                                        </div>
                                    </th>
                                    <th width="4%" class="text-center">#</th>
                                    <th>Nama File</th>
                                    <th width="13%" class="text-center">Ukuran</th>
                                    <th width="20%" class="text-center">Waktu Simpan</th>
                                    <th width="13%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($fileList ?? [] as $index => $f)
                                <tr class="item-file-row">
                                    <td class="text-center align-middle">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input chk-file-geojson" id="chk-file-{{ $index }}" value="{{ $f['name'] }}">
                                            <label class="custom-control-label" for="chk-file-{{ $index }}"></label>
                                        </div>
                                    </td>
                                    <td class="text-center font-weight-bold text-muted align-middle">{{ $index + 1 }}</td>
                                    <td class="align-middle">
                                        <i class="fas fa-file-code text-info mr-1"></i>
                                        <span class="nama-file-text font-weight-bold">{{ $f['name'] }}</span>
                                        @if(!$f['is_default'])
                                            <span class="badge badge-warning text-dark ml-1 small"><i class="fas fa-cloud-upload-alt mr-1"></i>Upload</span>
                                        @else
                                            <span class="badge badge-light border text-secondary ml-1 small">Sistem</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle"><span class="badge badge-light border">{{ $f['size'] }}</span></td>
                                    <td class="text-center small text-muted align-middle">{{ $f['date'] }}</td>
                                    <td class="text-center align-middle">
                                        <form action="{{ route('peta.delete-geojson') }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus file \'{{ $f['name'] }}\'? File ini akan dihapus secara permanen dari server.')">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="file_name" value="{{ $f['name'] }}">
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2 font-weight-bold" title="Hapus File Ini">
                                                <i class="fas fa-trash-alt mr-1"></i>Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">Tidak ada file GIS di folder public/geojson</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 d-flex justify-content-between align-items-center">
                    <div>
                        <button type="button" id="btn-hapus-terpilih" class="btn btn-danger btn-sm font-weight-bold" disabled onclick="konfirmasiHapusTerpilih()">
                            <i class="fas fa-trash-alt mr-1"></i>Hapus Terpilih (<span id="btn-bulk-count">0</span>)
                        </button>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Bulk Delete Form -->
    <form id="form-bulk-delete" action="{{ route('peta.delete-geojson-bulk') }}" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
        <div id="bulk-delete-inputs"></div>
    </form>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold" id="logoutModalLabel">Konfirmasi Logout</h5>
                    <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body p-4">Apakah Anda yakin ingin keluar dari akun WebGIS ini?</div>
                <div class="modal-footer bg-light">
                    <button class="btn btn-secondary font-weight-bold" type="button" data-dismiss="modal">Batal</button>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger font-weight-bold">Logout Sekarang</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Core Scripts -->
    <script src="{{ asset('sbadmin/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('sbadmin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.9.0/proj4.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

    <script>
    // 0. REGISTRASI PROJ4 DEFINISI UNTUK KOORDINAT ARCMAP / ESRI
    if (typeof proj4 !== 'undefined') {
        proj4.defs("EPSG:54034", "+proj=eqc +lat_ts=0 +lat_0=0 +lon_0=0 +x_0=0 +y_0=0 +datum=WGS84 +units=m +no_defs");
        proj4.defs("ESRI:54034", "+proj=eqc +lat_ts=0 +lat_0=0 +lon_0=0 +x_0=0 +y_0=0 +datum=WGS84 +units=m +no_defs");
        proj4.defs("EPSG:3857", "+proj=merc +a=6378137 +b=6378137 +lat_ts=0 +lon_0=0 +x_0=0 +y_0=0 +k=1 +units=m +nadgrids=@null +wktext +no_defs");
        proj4.defs("EPSG:32649", "+proj=utm +zone=49 +datum=WGS84 +units=m +no_defs");
        proj4.defs("EPSG:32749", "+proj=utm +zone=49 +south +datum=WGS84 +units=m +no_defs");
    }

    // 1. FULLSCREEN TOGGLE
    var btnFs = document.getElementById('btnFullscreenToggle');
    if (btnFs) {
        btnFs.addEventListener('click', function() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(function(err){});
                btnFs.innerHTML = '<i class="fas fa-compress"></i>';
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                    btnFs.innerHTML = '<i class="fas fa-expand"></i>';
                }
            }
        });
    }

    // 2. VIEW SWITCHER (Table + Maps / Maps Only / Table Only)
    window.switchViewMode = function(mode) {
        var mapSec = document.getElementById('section-map');
        var tblSec = document.getElementById('section-table');
        var mapEl  = document.getElementById('map');

        if (mode === 'maps') {
            mapSec.style.display = 'block';
            tblSec.style.display = 'none';
            mapEl.style.height = 'calc(100vh - 190px)';
        } else if (mode === 'table') {
            mapSec.style.display = 'none';
            tblSec.style.display = 'block';
        } else {
            // both
            mapSec.style.display = 'block';
            tblSec.style.display = 'block';
            mapEl.style.height = '480px';
        }

        setTimeout(function() {
            if (window.map) window.map.invalidateSize();
        }, 200);
    };

    // 3. INISIALISASI LEAFLET MAP
    var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19
    });

    var googleSat = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
        attribution: '&copy; Google Satellite',
        maxZoom: 20
    });

    var defaultCenter = [-0.0263, 109.3425]; // Kalimantan Barat / Pontianak
    var map = L.map('map', {
        center: defaultCenter,
        zoom: 8,
        layers: [osm],
        zoomControl: true
    });
    window.map = map;

    // Layer Groups
    var availableTypes = {!! json_encode($availableTypes) !!};
    var layerBaku       = L.layerGroup(); if (availableTypes.baku)       layerBaku.addTo(map);
    var layerFungsional = L.layerGroup(); if (availableTypes.fungsional) layerFungsional.addTo(map);
    var layerPotensial  = L.layerGroup(); if (availableTypes.potensial)  layerPotensial.addTo(map);
    var layerSaluran    = L.layerGroup(); if (availableTypes.saluran)    layerSaluran.addTo(map);
    var layerBangunan   = L.layerGroup(); if (availableTypes.bangunan)   layerBangunan.addTo(map);

    var baseMaps = {
        "Peta (OpenStreetMap)": osm,
        "Satelit (Google Hybrid)": googleSat
    };

    var overlayMaps = {};
    if (availableTypes.baku)       overlayMaps["Daerah Irigasi Baku (Hijau)"]       = layerBaku;
    if (availableTypes.fungsional) overlayMaps["Daerah Irigasi Fungsional (Biru)"]  = layerFungsional;
    if (availableTypes.potensial)  overlayMaps["Daerah Irigasi Potensial (Oranye)"] = layerPotensial;
    if (availableTypes.saluran)    overlayMaps["Jaringan Saluran Irigasi"]           = layerSaluran;
    if (availableTypes.bangunan)   overlayMaps["Bangunan Air / Pelengkap"]           = layerBangunan;

    L.control.layers(baseMaps, overlayMaps, { collapsed: false, position: 'topright' }).addTo(map);

    // Legenda Dinamis
    var legend = L.control({ position: 'bottomleft' });
    legend.onAdd = function () {
        var div = L.DomUtil.create('div', 'map-legend');
        var legendItems = '';
        if (availableTypes.baku)       legendItems += '<div class="legend-item"><span class="legend-color-box" style="background:#27ae60;border-color:#1b7a43;"></span> <b>DIR Baku</b> (Hijau)</div>';
        if (availableTypes.fungsional) legendItems += '<div class="legend-item"><span class="legend-color-box" style="background:#2980b9;border-color:#1a5276;"></span> <b>DIR Fungsional</b> (Biru)</div>';
        if (availableTypes.potensial)  legendItems += '<div class="legend-item"><span class="legend-color-box" style="background:#e67e22;border-color:#b95c00;"></span> <b>DIR Potensial</b> (Oranye)</div>';
        if (availableTypes.saluran) {
            legendItems += '<div class="legend-item"><span class="legend-line" style="background:#0984e3;"></span> <b>Jaringan Saluran</b></div>';
        }
        if (availableTypes.bangunan)   legendItems += '<div class="legend-item"><span class="legend-circle" style="background:#d63031;"></span> <b>Bangunan Air</b></div>';

        if (!legendItems) { div.style.display = 'none'; return div; }
        div.innerHTML = '<h6><i class="fas fa-palette text-primary mr-1"></i> Legenda Peta</h6>' + legendItems;
        return div;
    };
    legend.addTo(map);

    // Tombol Reset Peta (Kembali ke Bounds Awal)
    window.resetMapView = function() {
        if (mapBounds.isValid && mapBounds.isValid()) {
            map.fitBounds(mapBounds, { padding: [40, 40] });
        } else {
            map.setView(defaultCenter, 8);
        }
    };

    // 4. REPROYEKSI KOORDINAT (Mendukung WKID 54034, 3857, UTM 49, WGS84)
    function projectCoordinate(x, y, wkid) {
        if (typeof x !== 'number' || typeof y !== 'number' || isNaN(x) || isNaN(y)) return [x, y];
        // Jika sudah derajat WGS84 normal (lon ~108-111, lat -3 s/d 2)
        if (Math.abs(x) <= 180 && Math.abs(y) <= 90) return [x, y];

        // Jika koordinat dalam meter (Mercator / Equidistant Cylindrical untuk Kalimantan / Indonesia)
        if (Math.abs(x) >= 5000000 && Math.abs(x) <= 20000000 && Math.abs(y) <= 2000000) {
            var lon = (x / 6378137.0) * (180.0 / Math.PI);
            var lat = (2.0 * Math.atan(Math.exp(y / 6378137.0)) - (Math.PI / 2.0)) * (180.0 / Math.PI);
            return [lon, lat];
        }

        try {
            var wkidStr = String(wkid || '');
            if (wkid === 54034 || wkidStr.indexOf('54034') !== -1) {
                var lonEqc = (x / 6378137.0) * (180.0 / Math.PI);
                var latEqc = (2.0 * Math.atan(Math.exp(y / 6378137.0)) - (Math.PI / 2.0)) * (180.0 / Math.PI);
                return [lonEqc, latEqc];
            }
            if (wkid === 3857 || wkidStr.indexOf('3857') !== -1) {
                return proj4('EPSG:3857', 'EPSG:4326', [x, y]);
            }
            if (wkid === 32649 || wkidStr.indexOf('32649') !== -1) {
                return proj4('EPSG:32649', 'EPSG:4326', [x, y]);
            }
            if (wkid === 32749 || wkidStr.indexOf('32749') !== -1) {
                return proj4('EPSG:32749', 'EPSG:4326', [x, y]);
            }
            if (wkid && proj4.defs['EPSG:' + wkid]) {
                return proj4('EPSG:' + wkid, 'EPSG:4326', [x, y]);
            }
        } catch(e) {}

        // Fallback UTM zona 49
        if (x > 100000 && x < 1000000) {
            try { return proj4('EPSG:32649', 'EPSG:4326', [x, y]); } catch(e){}
        }

        return [x, y];
    }

    function transformGeoJsonCoords(coords, geomType, wkid) {
        if (!coords) return coords;
        if (geomType === 'Point') return projectCoordinate(coords[0], coords[1], wkid);
        if (geomType === 'LineString' || geomType === 'MultiPoint') {
            return coords.map(function(c) { return projectCoordinate(c[0], c[1], wkid); });
        }
        if (geomType === 'Polygon' || geomType === 'MultiLineString') {
            return coords.map(function(ring) { return ring.map(function(c) { return projectCoordinate(c[0], c[1], wkid); }); });
        }
        if (geomType === 'MultiPolygon') {
            return coords.map(function(poly) {
                return poly.map(function(ring) { return ring.map(function(c) { return projectCoordinate(c[0], c[1], wkid); }); });
            });
        }
        return coords;
    }

    function convertEsriToGeoJson(data) {
        if (!data) return null;
        var wkid = null;
        if (data.crs && data.crs.properties && data.crs.properties.name) wkid = String(data.crs.properties.name);
        else if (data.spatialReference) wkid = data.spatialReference.wkid || data.spatialReference.latestWkid;

        if (data.type === 'FeatureCollection' && Array.isArray(data.features)) {
            data.features.forEach(function(f) {
                if (f.geometry && f.geometry.coordinates) {
                    f.geometry.coordinates = transformGeoJsonCoords(f.geometry.coordinates, f.geometry.type, wkid);
                }
            });
            return data;
        }
        if (!data.features || !Array.isArray(data.features)) return null;

        var geomType = data.geometryType;
        var gj = { type: 'FeatureCollection', features: [] };
        data.features.forEach(function(f, idx) {
            if (!f.geometry) return;
            var geom = null;
            if (geomType === 'esriGeometryPoint' || (f.geometry.x !== undefined && f.geometry.y !== undefined)) {
                var pt = projectCoordinate(f.geometry.x, f.geometry.y, wkid);
                geom = { type: 'Point', coordinates: [pt[0], pt[1]] };
            } else if (geomType === 'esriGeometryPolyline' || f.geometry.paths) {
                var paths = f.geometry.paths || [];
                if (paths.length === 1) geom = { type: 'LineString', coordinates: paths[0].map(function(c){ return projectCoordinate(c[0],c[1],wkid); }) };
                else if (paths.length > 1) geom = { type: 'MultiLineString', coordinates: paths.map(function(p){ return p.map(function(c){ return projectCoordinate(c[0],c[1],wkid); }); }) };
            } else if (geomType === 'esriGeometryPolygon' || f.geometry.rings) {
                var rings = f.geometry.rings || [];
                if (rings.length > 0) geom = { type: 'Polygon', coordinates: rings.map(function(r){ return r.map(function(c){ return projectCoordinate(c[0],c[1],wkid); }); }) };
            }
            if (geom) {
                gj.features.push({
                    type: 'Feature',
                    id: (f.attributes && (f.attributes.FID || f.attributes.OBJECTID)) ? (f.attributes.FID || f.attributes.OBJECTID) : idx,
                    geometry: geom,
                    properties: f.attributes || {}
                });
            }
        });
        return gj;
    }

    // 5. FETCH & LOAD GEOJSON FILES DARI SERVER SECARA OTOMATIS
    var geojsonFiles = {!! json_encode($geojsonFiles ?? []) !!};
    var mapLoadingEl = document.getElementById('map-loading');
    var allTableRows = [];
    var currentSearchText = '';
    var mapBounds = L.latLngBounds();

    if (geojsonFiles.length > 0) {
        var loadedCount = 0;
        geojsonFiles.forEach(function(fileName) {
            fetch('/geojson/' + fileName)
                .then(function(res) { if (!res.ok) throw new Error('HTTP ' + res.status); return res.json(); })
                .then(function(rawData) {
                    var gj = convertEsriToGeoJson(rawData);
                    if (!gj || !gj.features || gj.features.length === 0) return;

                    var nameUpper = fileName.toUpperCase();
                    var cleanName = fileName.replace(/\.(json|geojson)$/i, '').replace(/_/g, ' ');
                    var targetLayer = layerBaku;
                    var categoryType = 'baku';
                    var categoryBadge = '<span class="badge badge-baku px-2 py-1">DIR Baku</span>';
                    var strokeColor = '#27ae60';
                    var fillColor   = '#2ecc71';

                    if (nameUpper.indexOf('BAKU') === 0) {
                        targetLayer = layerBaku; categoryType = 'baku';
                        categoryBadge = '<span class="badge badge-baku px-2 py-1">DIR Baku</span>';
                        strokeColor = '#1e824c'; fillColor = '#27ae60';
                    } else if (nameUpper.indexOf('FUNGSIONAL') === 0) {
                        targetLayer = layerFungsional; categoryType = 'fungsional';
                        categoryBadge = '<span class="badge badge-fungsional px-2 py-1">DIR Fungsional</span>';
                        strokeColor = '#1f618d'; fillColor = '#2980b9';
                    } else if (nameUpper.indexOf('POTENSIAL') === 0) {
                        targetLayer = layerPotensial; categoryType = 'potensial';
                        categoryBadge = '<span class="badge badge-potensial px-2 py-1">DIR Potensial</span>';
                        strokeColor = '#b95c00'; fillColor = '#e67e22';
                    } else if (nameUpper.indexOf('SALURAN') === 0) {
                        targetLayer = layerSaluran; categoryType = 'saluran';
                        categoryBadge = '<span class="badge badge-saluran px-2 py-1">Saluran Irigasi</span>';
                        strokeColor = '#0984e3'; fillColor = '#0984e3';
                    } else if (nameUpper.indexOf('BANGUNAN') === 0) {
                        targetLayer = layerBangunan; categoryType = 'bangunan';
                        categoryBadge = '<span class="badge badge-bangunan px-2 py-1">Bangunan Air</span>';
                        strokeColor = '#d63031'; fillColor = '#d63031';
                    }

                    // Render GeoJSON ke Leaflet
                    var gjLayer = L.geoJSON(gj, {
                        style: function() {
                            return {
                                color: strokeColor,
                                weight: (categoryType === 'saluran') ? 2.5 : 1.8,
                                fillOpacity: 0.45,
                                fillColor: fillColor
                            };
                        },
                        pointToLayer: function(feat, latlng) {
                            return L.circleMarker(latlng, {
                                radius: 6,
                                fillColor: fillColor,
                                color: '#ffffff',
                                weight: 1.5,
                                fillOpacity: 0.9
                            });
                        },
                        onEachFeature: function(feat, layer) {
                            var p = feat.properties || {};
                            var popup = '<div style="min-width:200px;font-size:12px;">' +
                                '<div class="font-weight-bold text-primary mb-1">' + cleanName + '</div>' +
                                '<div class="mb-1">' + categoryBadge + '</div>' +
                                '<table class="table table-sm table-bordered mt-2 mb-0" style="font-size:11px;">';
                            var countProp = 0;
                            for (var k in p) {
                                if (countProp < 5 && p[k] !== null && p[k] !== '') {
                                    popup += '<tr><th class="bg-light">' + k + '</th><td>' + p[k] + '</td></tr>';
                                    countProp++;
                                }
                            }
                            popup += '</table></div>';
                            layer.bindPopup(popup);
                        }
                    });

                    targetLayer.addLayer(gjLayer);

                    // Ambil koordinat pusat & batas wilayah
                    var b = gjLayer.getBounds();
                    if (b.isValid()) {
                        mapBounds.extend(b);
                        var center = b.getCenter();
                        allTableRows.push({
                            name: cleanName,
                            categoryType: categoryType,
                            categoryBadge: categoryBadge,
                            lat: center.lat,
                            lng: center.lng,
                            fileName: fileName,
                            bounds: b,
                            layerObj: gjLayer
                        });
                    }
                })
                .catch(function(err) {})
                .finally(function() {
                    loadedCount++;
                    if (loadedCount >= geojsonFiles.length) {
                        if (mapLoadingEl) mapLoadingEl.style.display = 'none';
                        if (mapBounds.isValid()) map.fitBounds(mapBounds, { padding: [40, 40] });
                        renderTable();
                    }
                });
        });
    } else {
        if (mapLoadingEl) mapLoadingEl.style.display = 'none';
        renderTable();
    }

    // 6. RENDER DATA TABLE (Dark Header & State Kosong Rapi)
    function renderTable() {
        var tbody = document.getElementById('tbody-lokasi');
        var footerInfo = document.getElementById('tabel-footer-info');
        if (!tbody) return;

        var filtered = allTableRows.filter(function(row) {
            if (currentSearchText === '') return true;
            var term = currentSearchText.toLowerCase();
            return (row.name && row.name.toLowerCase().indexOf(term) !== -1) ||
                   (row.fileName && row.fileName.toLowerCase().indexOf(term) !== -1);
        });

        if (footerInfo) {
            footerInfo.innerHTML = 'Menampilkan <b>' + filtered.length + '</b> dari <b>' + allTableRows.length + '</b> file GIS';
        }

        // STATE KOSONG RAPI (Jika tidak ada file atau hasil filter nihil)
        if (filtered.length === 0) {
            if (allTableRows.length === 0) {
                tbody.innerHTML = 
                    '<tr>' +
                        '<td colspan="7" class="text-center py-5 text-muted">' +
                            '<div class="mb-2"><i class="fas fa-folder-open fa-3x text-gray-300"></i></div>' +
                            '<div class="font-weight-bold text-gray-700 mb-1" style="font-size:15px;">Belum Ada Data GIS Tersimpan</div>' +
                            '<p class="small text-muted mb-3">Silakan klik tombol "Upload GeoJSON" untuk menambahkan file koordinat dan layer ke peta.</p>' +
                            '<button type="button" class="btn btn-sm btn-primary font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalUploadGeojson">' +
                                '<i class="fas fa-cloud-upload-alt mr-1"></i> Upload GeoJSON Sekarang' +
                            '</button>' +
                        '</td>' +
                    '</tr>';
            } else {
                tbody.innerHTML = 
                    '<tr>' +
                        '<td colspan="7" class="text-center py-4 text-muted">' +
                            '<i class="fas fa-info-circle mr-2 text-info"></i>Tidak ditemukan data GIS yang cocok dengan pencarian "<b>' + currentSearchText + '</b>".' +
                        '</td>' +
                    '</tr>';
            }
            return;
        }

        var frag = document.createDocumentFragment();
        filtered.forEach(function(row, idx) {
            var tr = document.createElement('tr');
            var latStr = (row.lat !== null && !isNaN(row.lat)) ? Number(row.lat).toFixed(5) : '-';
            var lngStr = (row.lng !== null && !isNaN(row.lng)) ? Number(row.lng).toFixed(5) : '-';

            tr.innerHTML = 
                '<td class="text-center font-weight-bold text-muted">' + (idx + 1) + '</td>' +
                '<td class="font-weight-bold text-dark">' + row.name + '</td>' +
                '<td>' + row.categoryBadge + '</td>' +
                '<td class="text-center font-weight-bold text-dark"><code>' + latStr + '</code></td>' +
                '<td class="text-center font-weight-bold text-dark"><code>' + lngStr + '</code></td>' +
                '<td><span class="badge badge-light border text-monospace text-truncate d-inline-block" style="max-width:200px;" title="' + row.fileName + '">' + row.fileName + '</span></td>' +
                '<td class="text-center">' +
                    '<button type="button" class="btn-focus-row" title="Fokuskan peta ke objek ini">' +
                        '<i class="fas fa-crosshairs"></i> Fokus' +
                    '</button>' +
                '</td>';

            // TOMBOL FOKUS KE OBJEK SPASIAL SECARA PRESISI
            var btn = tr.querySelector('.btn-focus-row');
            btn.addEventListener('click', function() {
                if (row.bounds && row.bounds.isValid && row.bounds.isValid()) {
                    map.fitBounds(row.bounds, { padding: [50, 50], maxZoom: 16 });
                } else if (row.lat && row.lng && !isNaN(row.lat) && !isNaN(row.lng)) {
                    map.flyTo([row.lat, row.lng], 15, { animate: true, duration: 1.0 });
                }
                if (row.layerObj) {
                    if (row.layerObj.openPopup) {
                        row.layerObj.openPopup();
                    } else if (row.layerObj.eachLayer) {
                        row.layerObj.eachLayer(function(l) { if (l.openPopup) { l.openPopup(); return; } });
                    }
                }
                var mapSec = document.getElementById('section-map');
                if (mapSec) {
                    window.scrollTo({ top: mapSec.offsetTop - 85, behavior: 'smooth' });
                }
            });

            frag.appendChild(tr);
        });

        tbody.innerHTML = '';
        tbody.appendChild(frag);
    }

    // 7. PENCARIAN DATA REALTIME
    window.filterTabelKoordinat = function(val) {
        currentSearchText = (val || '').trim();
        renderTable();
    };

    // 8. EXPORT DATA TO CSV (Export Data Spasial Realtime)
    window.exportDataToCsv = function() {
        if (!allTableRows || allTableRows.length === 0) {
            alert('Belum ada data GIS yang dapat diexport.');
            return;
        }

        var csv = 'No,Nama Pos / Objek DIR,Kategori Layer,Latitude (Y),Longitude (X),Sumber File GIS\n';
        allTableRows.forEach(function(r, i) {
            var clean = (r.name || '').replace(/,/g, ' ');
            var cat = r.categoryType || '';
            var file = r.fileName || '';
            var lat = (r.lat !== null && !isNaN(r.lat)) ? Number(r.lat).toFixed(5) : '';
            var lng = (r.lng !== null && !isNaN(r.lng)) ? Number(r.lng).toFixed(5) : '';
            csv += (i + 1) + ',"' + clean + '","' + cat + '",' + lat + ',' + lng + ',"' + file + '"\n';
        });

        var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        var url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'Data_GIS_DIR_BWS_Kalimantan_I_' + new Date().toISOString().slice(0,10) + '.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };

    // 9. HELPER MODAL & CHECKBOX KELOLA FILE GIS
    function updateFileInputLabel(input) {
        var label = document.getElementById('labelGeojsonFile');
        if (input.files && input.files.length > 1) {
            label.textContent = input.files.length + ' file dipilih';
        } else if (input.files && input.files.length === 1) {
            label.textContent = input.files[0].name;
        } else {
            label.textContent = 'Pilih satu atau beberapa file...';
        }
    }

    function filterTabelFileGeojson(query) {
        var term = (query || '').toLowerCase().trim();
        document.querySelectorAll('#tabel-kelola-geojson tbody tr.item-file-row').forEach(function(row) {
            var text = row.querySelector('.nama-file-text');
            if (!text) return;
            row.style.display = (text.textContent.toLowerCase().indexOf(term) !== -1) ? '' : 'none';
        });
    }

    function updateBulkDeleteUI() {
        var checked = document.querySelectorAll('.chk-file-geojson:checked');
        var total = checked.length;
        var btn = document.getElementById('btn-hapus-terpilih');
        var countEl = document.getElementById('bulk-selected-count');
        var btnCountEl = document.getElementById('btn-bulk-count');
        var infoEl = document.getElementById('bulk-selected-info');

        if (countEl) countEl.textContent = total;
        if (btnCountEl) btnCountEl.textContent = total;
        if (infoEl) infoEl.style.display = total > 0 ? '' : 'none';
        if (btn) btn.disabled = total === 0;

        var all = document.querySelectorAll('.chk-file-geojson');
        var chkAll = document.getElementById('chk-select-all-geojson');
        if (chkAll) {
            if (total === 0) { chkAll.checked = false; chkAll.indeterminate = false; }
            else if (total === all.length) { chkAll.checked = true; chkAll.indeterminate = false; }
            else { chkAll.checked = false; chkAll.indeterminate = true; }
        }
    }

    function konfirmasiHapusTerpilih() {
        var checked = document.querySelectorAll('.chk-file-geojson:checked');
        if (checked.length === 0) return;

        var names = [];
        checked.forEach(function(c) { names.push(c.value); });

        var msg = 'Anda akan menghapus ' + checked.length + ' file GIS secara permanen:\n\n';
        names.forEach(function(n, i) { msg += (i + 1) + '. ' + n + '\n'; });
        msg += '\nLanjutkan penghapusan?';

        if (!confirm(msg)) return;

        var container = document.getElementById('bulk-delete-inputs');
        container.innerHTML = '';
        names.forEach(function(name) {
            var inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'file_names[]';
            inp.value = name;
            container.appendChild(inp);
        });

        document.getElementById('form-bulk-delete').submit();
    }

    document.addEventListener('DOMContentLoaded', function() {
        var chkAll = document.getElementById('chk-select-all-geojson');
        if (chkAll) {
            chkAll.addEventListener('change', function() {
                document.querySelectorAll('.chk-file-geojson').forEach(function(c) { c.checked = chkAll.checked; });
                updateBulkDeleteUI();
            });
        }
        document.querySelectorAll('.chk-file-geojson').forEach(function(c) {
            c.addEventListener('change', updateBulkDeleteUI);
        });

        // 10. CHUNKED BATCH UPLOAD UNTUK MENCEGAH PostTooLargeException
        var formUpload = document.getElementById('formUploadGeojson');
        if (formUpload) {
            formUpload.addEventListener('submit', async function(e) {
                e.preventDefault();

                var input = document.getElementById('inputGeojsonFile');
                if (!input || !input.files || input.files.length === 0) {
                    alert('Silakan pilih setidaknya satu file GeoJSON/JSON terlebih dahulu.');
                    return;
                }

                var files = Array.from(input.files);
                var totalFiles = files.length;
                var BATCH_SIZE = 5; // Unggah 5 file per batch secara aman
                var totalBatches = Math.ceil(totalFiles / BATCH_SIZE);

                var progressSection = document.getElementById('uploadProgressSection');
                var progressBar     = document.getElementById('uploadProgressBar');
                var percentText     = document.getElementById('uploadPercentText');
                var statusText      = document.getElementById('uploadStatusText');
                var detailText      = document.getElementById('uploadDetailText');
                var batchCountText  = document.getElementById('uploadBatchCountText');
                var successSection  = document.getElementById('uploadSuccessSection');
                var errorSection    = document.getElementById('uploadErrorSection');
                var btnSubmit       = document.getElementById('btnSubmitUpload');
                var btnBatal        = document.getElementById('btnBatalUpload');
                var btnClose        = document.getElementById('btnCloseUploadModal');

                if (errorSection) { errorSection.style.display = 'none'; errorSection.innerText = ''; }
                if (successSection) successSection.style.display = 'none';
                if (progressSection) progressSection.style.display = 'block';
                if (btnSubmit) {
                    btnSubmit.disabled = true;
                    btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Mengunggah...';
                }
                if (btnBatal) btnBatal.disabled = true;
                if (btnClose) btnClose.style.display = 'none';

                var totalUploaded = 0;
                var allUploadedNames = [];
                var allFailed = [];
                var csrfToken = formUpload.querySelector('input[name="_token"]')?.value || '{{ csrf_token() }}';

                try {
                    for (var b = 0; b < totalBatches; b++) {
                        var start = b * BATCH_SIZE;
                        var end = Math.min(start + BATCH_SIZE, totalFiles);
                        var batchFiles = files.slice(start, end);
                        var currentBatchNum = b + 1;

                        if (statusText) {
                            statusText.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Mengunggah batch ' + currentBatchNum + ' dari ' + totalBatches + '...';
                        }
                        if (batchCountText) {
                            batchCountText.textContent = 'Batch ' + currentBatchNum + '/' + totalBatches + ' (' + batchFiles.length + ' file)';
                        }

                        var formData = new FormData();
                        formData.append('_token', csrfToken);
                        for (var f = 0; f < batchFiles.length; f++) {
                            formData.append('file_geojson[]', batchFiles[f]);
                        }

                        var response = await fetch("{{ route('peta.upload-geojson') }}", {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        if (!response.ok) {
                            var errJson = await response.json().catch(function() { return {}; });
                            throw new Error(errJson.message || ('Gagal pada pengunggahan batch ke-' + currentBatchNum + ' (HTTP ' + response.status + ')'));
                        }

                        var result = await response.json();
                        if (result.uploaded && result.uploaded.length > 0) {
                            allUploadedNames = allUploadedNames.concat(result.uploaded);
                            totalUploaded += result.uploaded.length;
                        }
                        if (result.failed && result.failed.length > 0) {
                            allFailed = allFailed.concat(result.failed);
                        }

                        var pct = Math.round((end / totalFiles) * 100);
                        if (progressBar) {
                            progressBar.style.width = pct + '%';
                            progressBar.textContent = pct + '%';
                        }
                        if (percentText) percentText.textContent = pct + '%';
                        if (detailText) {
                            detailText.innerHTML = '<span>Tersimpan <b>' + totalUploaded + '</b> dari <b>' + totalFiles + '</b> file</span><span id="uploadBatchCountText">Batch ' + currentBatchNum + '/' + totalBatches + '</span>';
                        }
                    }

                    if (statusText) {
                        statusText.innerHTML = '<i class="fas fa-check-circle text-success mr-1"></i>Semua file selesai diunggah!';
                    }
                    if (successSection) {
                        var successMsg = totalUploaded + ' file GeoJSON berhasil diunggah! Memuat ulang halaman peta...';
                        if (allFailed.length > 0) {
                            successMsg += '<br><small class="text-warning">File dilewati: ' + allFailed.join(', ') + '</small>';
                        }
                        successSection.innerHTML = '<i class="fas fa-check-circle mr-1"></i>' + successMsg;
                        successSection.style.display = 'block';
                    }

                    setTimeout(function() {
                        window.location.reload();
                    }, 1200);

                } catch (err) {
                    if (errorSection) {
                        errorSection.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i><b>Terjadi Kesalahan:</b> ' + err.message +
                            (totalUploaded > 0 ? '<br><small>(' + totalUploaded + ' file sempat tersimpan sebelum error)</small>' : '');
                        errorSection.style.display = 'block';
                    }
                    if (btnSubmit) {
                        btnSubmit.disabled = false;
                        btnSubmit.innerHTML = '<i class="fas fa-redo mr-1"></i>Coba Lagi';
                    }
                    if (btnBatal) btnBatal.disabled = false;
                    if (btnClose) btnClose.style.display = '';
                }
            });
        }
    });
    </script>
</body>
</html>
