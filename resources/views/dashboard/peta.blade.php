<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>BWS Kalimantan I – Peta Daerah Irigasi & Jaringan SDA</title>

    {{-- Google Fonts: Inter & Nunito --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

    {{-- FontAwesome Free Icons --}}
    <link href="{{ asset('sbadmin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">

    {{-- Bootstrap 4 CSS --}}
    <link href="{{ asset('sbadmin/css/sb-admin-2.min.css') }}" rel="stylesheet">

    {{-- Leaflet CSS & MarkerCluster --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />

    {{-- CSS Khusus Peta --}}
    <link rel="stylesheet" href="{{ asset('css/peta.css') }}?v={{ filemtime(public_path('css/peta.css')) }}">
</head>
<body class="page-peta-v2">

    {{-- ============================================================
         NAVBAR PUTIH (Khusus Halaman Peta v2)
         ============================================================ --}}
    <header class="peta-navbar" id="petaNavbar">
        <div class="peta-navbar-inner">
            {{-- Kiri: Logo + Nama Instansi --}}
            <a href="{{ route('peta') }}" class="peta-brand">
                <img src="{{ asset('sbadmin/img/logo-pu.jpeg') }}" alt="Logo PU" class="peta-brand-logo">
                <div class="peta-brand-text">
                    <div class="peta-brand-title">WEBGIS BWS KALIMANTAN I</div>
                    <div class="peta-brand-subtitle">Direktorat Jenderal Sumber Daya Air &bull; Kementerian Pekerjaan Umum</div>
                </div>
            </a>

            {{-- Kanan: Tombol Masuk / Profil --}}
            <div class="peta-navbar-right">
                @auth
                    {{-- Logged In: Dropdown Profil --}}
                    <div class="dropdown">
                        <div class="peta-profile-btn" id="petaUserDrop" data-toggle="dropdown"
                             aria-haspopup="true" aria-expanded="false">
                            <div class="peta-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <span>{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down ml-1" style="font-size:11px; opacity:0.6;"></i>
                        </div>
                        <div class="dropdown-menu dropdown-menu-right shadow py-2"
                             aria-labelledby="petaUserDrop" style="min-width:180px; border-radius:8px; border:1px solid #e2e8f0;">
                            @if(in_array(Auth::user()->role, ['superadmin', 'admin']))
                            <a class="dropdown-item py-2 d-flex align-items-center"
                               href="{{ route('admin.panel') }}">
                                <i class="fas fa-cog text-muted mr-2" style="width:16px;"></i>
                                <span>Admin Panel</span>
                            </a>
                            <div class="dropdown-divider my-1"></div>
                            @endif
                            <a class="dropdown-item py-2 d-flex align-items-center text-danger"
                               href="#" data-toggle="modal" data-target="#logoutModal">
                                <i class="fas fa-sign-out-alt mr-2" style="width:16px;"></i>
                                <span>Keluar / Logout</span>
                            </a>
                        </div>
                    </div>
                @else
                    {{-- Tamu: Tombol Masuk --}}
                    <a href="{{ route('login') }}" class="peta-btn-masuk">
                        <i class="fas fa-sign-in-alt mr-1"></i> Masuk
                    </a>
                @endauth
            </div>
        </div>
    </header>


    {{-- ============================================================
         HERO BANNER (Banner Biru dengan Judul & Lokasi)
         ============================================================ --}}
    <section class="peta-hero">
        <div class="peta-hero-inner">
            <div class="peta-hero-content">
                <div class="peta-hero-badge">
                    <i class="fas fa-chart-bar mr-1"></i> DATA SPASIAL BWS KALIMANTAN I
                </div>
                <h1 class="peta-hero-title">Peta Daerah Irigasi &amp; Jaringan SDA.</h1>
                <p class="peta-hero-desc">
                    Temukan sebaran Daerah Irigasi, Jaringan Saluran, dan Bangunan Air
                    di Kalimantan Barat dalam satu peta.
                </p>
            </div>
            <div class="peta-hero-location">
                <div class="peta-hero-loc-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div>
                    <div class="peta-hero-loc-region">Kalimantan Barat</div>
                    <div class="peta-hero-loc-sub">BWS Kalimantan I | Pontianak</div>
                </div>
            </div>
        </div>
    </section>


    {{-- ============================================================
         FLASH ALERTS
         ============================================================ --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mx-3 mt-3 shadow-sm" role="alert" style="border-radius:8px;">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mx-3 mt-3 shadow-sm" role="alert" style="border-radius:8px;">
        <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
    @endif


    {{-- ============================================================
         LAYOUT UTAMA: SIDEBAR FILTER KIRI + AREA PETA/TABEL
         ============================================================ --}}
    <div class="peta-v2-container">

        {{-- ========================
             SIDEBAR FILTER KIRI
             ======================== --}}
        <aside class="peta-v2-sidebar">

            {{-- Header Sidebar --}}
            <div class="psb-header">
                <div class="psb-header-icon">
                    <i class="fas fa-search"></i>
                </div>
                <div>
                    <div class="psb-header-title">Jelajahi objek irigasi</div>
                    <div class="psb-header-sub">Mulai dari nama atau No Foto spasial</div>
                </div>
            </div>

            {{-- Pencarian --}}
            <div class="psb-section">
                <label class="psb-label">Cari pos atau daerah irigasi</label>
                <div class="psb-search-wrap">
                    <input type="text"
                           id="cari-sidebar"
                           class="psb-search-input"
                           placeholder="Nama pos, DIR, atau file..."
                           onkeyup="filterTabelKoordinat(this.value); syncSearchInputs(this.value);">
                </div>
                <div class="psb-search-hint">
                    Pilih hasil pencarian untuk melihat detail. Maksimal 150 karakter
                </div>
            </div>

            {{-- Wilayah Kabupaten --}}
            <div class="psb-section">
                <label class="psb-label">Wilayah kabupaten</label>
                <div class="psb-select-wrap">
                    <i class="fas fa-map-marker-alt psb-select-icon"></i>
                    <select class="psb-select" id="filterWilayah" onchange="filterByWilayah(this.value)">
                        <option value="">Semua kabupaten (Kalbar)</option>
                        <option value="pontianak">Pontianak</option>
                        <option value="kubu raya">Kubu Raya</option>
                        <option value="mempawah">Mempawah</option>
                        <option value="landak">Landak</option>
                        <option value="sambas">Sambas</option>
                        <option value="bengkayang">Bengkayang</option>
                        <option value="singkawang">Singkawang</option>
                        <option value="ketapang">Ketapang</option>
                        <option value="kayong utara">Kayong Utara</option>
                    </select>
                </div>
            </div>

            {{-- Kategori Layer --}}
            <div class="psb-section">
                <div class="psb-cat-header">
                    <label class="psb-label mb-0">Tampilkan kategori</label>
                    <a href="#" class="psb-cat-link" onclick="scrollToLegend(event)">Layer peta</a>
                </div>

                {{-- Toggle: Lahan Baku --}}
                <div class="psb-cat-item" id="cat-baku">
                    <div class="psb-cat-icon" style="background:#dcfce7; color:#166534;">
                        <i class="fas fa-map"></i>
                    </div>
                    <div class="psb-cat-info">
                        <div class="psb-cat-name">Lahan Baku</div>
                        <div class="psb-cat-desc">Daerah Irigasi Baku</div>
                    </div>
                    <button class="psb-toggle active" id="toggle-baku"
                            onclick="toggleCategoryLayer('baku', this)" title="Tampilkan/Sembunyikan">
                        <i class="fas fa-check"></i>
                    </button>
                </div>

                {{-- Toggle: Lahan Fungsional --}}
                <div class="psb-cat-item" id="cat-fungsional">
                    <div class="psb-cat-icon" style="background:#dbeafe; color:#1e40af;">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div class="psb-cat-info">
                        <div class="psb-cat-name">Lahan Fungsional</div>
                        <div class="psb-cat-desc">Aset Terukur</div>
                    </div>
                    <button class="psb-toggle active" id="toggle-fungsional"
                            onclick="toggleCategoryLayer('fungsional', this)" title="Tampilkan/Sembunyikan">
                        <i class="fas fa-check"></i>
                    </button>
                </div>

                {{-- Toggle: Lahan Potensial --}}
                <div class="psb-cat-item" id="cat-potensial">
                    <div class="psb-cat-icon" style="background:#ffedd5; color:#9a3412;">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <div class="psb-cat-info">
                        <div class="psb-cat-name">Lahan Potensial</div>
                        <div class="psb-cat-desc">Rencana Pengembangan</div>
                    </div>
                    <button class="psb-toggle active" id="toggle-potensial"
                            onclick="toggleCategoryLayer('potensial', this)" title="Tampilkan/Sembunyikan">
                        <i class="fas fa-check"></i>
                    </button>
                </div>

                {{-- Toggle: Jaringan Saluran --}}
                <div class="psb-cat-item" id="cat-saluran">
                    <div class="psb-cat-icon" style="background:#cffafe; color:#155e75;">
                        <i class="fas fa-water"></i>
                    </div>
                    <div class="psb-cat-info">
                        <div class="psb-cat-name">Jaringan Saluran</div>
                        <div class="psb-cat-desc">Saluran Irigasi</div>
                    </div>
                    <button class="psb-toggle active" id="toggle-saluran"
                            onclick="toggleCategoryLayer('saluran', this)" title="Tampilkan/Sembunyikan">
                        <i class="fas fa-check"></i>
                    </button>
                </div>

                {{-- Toggle: Bangunan Air --}}
                <div class="psb-cat-item" id="cat-bangunan">
                    <div class="psb-cat-icon" style="background:#fee2e2; color:#991b1b;">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="psb-cat-info">
                        <div class="psb-cat-name">Bangunan Air</div>
                        <div class="psb-cat-desc">Bangunan Air &amp; Bangunan</div>
                    </div>
                    <button class="psb-toggle active" id="toggle-bangunan"
                            onclick="toggleCategoryLayer('bangunan', this)" title="Tampilkan/Sembunyikan">
                        <i class="fas fa-check"></i>
                    </button>
                </div>
            </div>

            {{-- Info Footer --}}
            <div class="psb-footer-info">
                <i class="fas fa-info-circle mr-1"></i>
                Klik ikon di peta untuk melihat info, luas, dan koordinat akurat daerah irigasi
            </div>

            {{-- Tombol GIS (Khusus Admin) --}}
            @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'superadmin']))
            <div class="psb-admin-actions">
                <button type="button" class="psb-btn-admin" data-toggle="modal" data-target="#modalKelolaGeojson">
                    <i class="fas fa-folder-open"></i>
                    <span>Kelola File GIS</span>
                    <span class="badge badge-primary ml-auto" id="sidebar-file-count">{{ count($fileList ?? []) }}</span>
                </button>
                <button type="button" class="psb-btn-admin psb-btn-primary" data-toggle="modal" data-target="#modalUploadGeojson">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <span>Upload File GIS</span>
                </button>
            </div>
            @endif

        </aside>


        {{-- ========================
             AREA PETA & TABEL
             ======================== --}}
        <main class="peta-v2-main">

            {{-- TAB BAR --}}
            <div class="peta-tab-bar">
                {{-- Breadcrumb Kiri --}}
                <div class="ptb-breadcrumb">
                    <i class="fas fa-map-marked-alt mr-1 text-primary" style="font-size:13px;"></i>
                    <span>Peta Daerah Irigasi</span>
                    <i class="fas fa-chevron-right mx-1" style="font-size:10px; color:#94a3b8;"></i>
                    <span>Kalimantan Barat</span>
                </div>

                {{-- Tab Kanan --}}
                <div class="ptb-tabs">
                    <button class="ptb-tab active" id="tab-peta" onclick="switchPetaTab('peta')">
                        <i class="fas fa-map mr-1"></i> Peta Interaktif
                    </button>
                    <button class="ptb-tab" id="tab-tabel" onclick="switchPetaTab('tabel')">
                        <i class="fas fa-table mr-1"></i>
                        Buka Tabel Data
                        <span class="ptb-badge" id="ptb-file-count">{{ count($fileList) }}</span>
                    </button>
                    <button class="ptb-btn-reset" id="btnResetPeta" onclick="resetPetaView()" title="Reset tampilan peta">
                        <i class="fas fa-redo-alt mr-1"></i> Reset
                    </button>
                </div>
            </div>


            {{-- SECTION PETA --}}
            <div id="section-map" class="peta-v2-map-wrap">
                <div class="bws-map-loading" id="map-loading">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>Memuat koordinat GIS ArcMap...</span>
                </div>
                <div id="map"></div>
            </div>


            {{-- SECTION TABEL (tersembunyi, tampil saat klik tab Tabel) --}}
            <div id="section-table" class="peta-v2-table-wrap" style="display:none;">
                <div class="bws-table-toolbar">
                    <div class="bws-table-title">
                        <span>Daftar Pos Daerah Irigasi &amp; Data Spasial</span>
                        <span class="badge badge-light border text-muted px-2 py-1"
                              style="font-size:12px;" id="badge-total-file-tabel">
                            {{ count($fileList) }} File
                        </span>
                    </div>

                    <div class="bws-table-controls">
                        {{-- Pencarian --}}
                        <div class="bws-search-input-wrap">
                            <i class="fas fa-search"></i>
                            <input type="text"
                                   id="cari-tabel-koordinat"
                                   placeholder="Cari nama pos/objek/file..."
                                   onkeyup="filterTabelKoordinat(this.value); syncSearchInputs(this.value);">
                        </div>

                        {{-- Tombol Admin --}}
                        @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'superadmin']))
                        <button type="button" class="btn-export-green" onclick="exportDataToCsv()"
                                title="Unduh data dalam format CSV/Excel">
                            <i class="fas fa-file-excel"></i>
                            <span>Export CSV</span>
                        </button>
                        <div class="dropdown d-inline-block">
                            <button type="button"
                                    class="btn btn-sm btn-primary dropdown-toggle font-weight-bold shadow-sm px-3"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                    style="height:38px; border-radius:6px; font-size:13px;">
                                <i class="fas fa-file-download mr-1"></i>
                                <span>Unduh GeoJSON</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right shadow border-0 py-2"
                                 style="border-radius:8px; min-width:250px; z-index:1060;">
                                <div class="dropdown-header text-uppercase font-weight-bold text-xs text-muted">
                                    Unduh Arsip GeoJSON (.ZIP)
                                </div>
                                <a class="dropdown-item py-2 d-flex align-items-center justify-content-between"
                                   href="{{ route('peta.download-category', 'all') }}">
                                    <span><i class="fas fa-archive text-primary mr-2"></i> Semua Data GIS</span>
                                    <span class="badge badge-primary px-2">{{ count($fileList) }}</span>
                                </a>
                                <div class="dropdown-divider my-1"></div>
                                <a class="dropdown-item py-2 d-flex align-items-center justify-content-between"
                                   href="{{ route('peta.download-category', 'baku') }}">
                                    <span class="d-flex align-items-center">
                                        <span class="bws-flyout-badge mr-2" style="background:#27ae60;"></span> DIR Baku
                                    </span>
                                    <span class="badge badge-light border text-muted">9 File</span>
                                </a>
                                <a class="dropdown-item py-2 d-flex align-items-center justify-content-between"
                                   href="{{ route('peta.download-category', 'fungsional') }}">
                                    <span class="d-flex align-items-center">
                                        <span class="bws-flyout-badge mr-2" style="background:#2980b9;"></span> DIR Fungsional
                                    </span>
                                    <span class="badge badge-light border text-muted">6 File</span>
                                </a>
                                <a class="dropdown-item py-2 d-flex align-items-center justify-content-between"
                                   href="{{ route('peta.download-category', 'potensial') }}">
                                    <span class="d-flex align-items-center">
                                        <span class="bws-flyout-badge mr-2" style="background:#e67e22;"></span> DIR Potensial
                                    </span>
                                    <span class="badge badge-light border text-muted">10 File</span>
                                </a>
                                <div class="dropdown-divider my-1"></div>
                                <a class="dropdown-item py-2 d-flex align-items-center justify-content-between"
                                   href="{{ route('peta.download-category', 'saluran') }}">
                                    <span class="d-flex align-items-center">
                                        <span class="bws-flyout-badge mr-2" style="background:#0984e3;"></span> Saluran Irigasi
                                    </span>
                                    <span class="badge badge-light border text-muted">15 File</span>
                                </a>
                                <a class="dropdown-item py-2 d-flex align-items-center justify-content-between"
                                   href="{{ route('peta.download-category', 'bangunan') }}">
                                    <span class="d-flex align-items-center">
                                        <span class="bws-flyout-badge mr-2" style="background:#d63031;"></span> Bangunan Air
                                    </span>
                                    <span class="badge badge-light border text-muted">14 File</span>
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="bws-table-responsive">
                    <table class="bws-custom-table" id="tabel-lokasi">
                        <thead>
                            <tr>
                                <th width="5%"  class="text-center">No.</th>
                                <th width="26%">Nama Pos / Objek</th>
                                <th width="17%">Wilayah / Kategori</th>
                                <th width="12%" class="text-center">Latitude (Y)</th>
                                <th width="12%" class="text-center">Longitude (X)</th>
                                <th width="16%">Sumber File GIS</th>
                                <th width="12%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-lokasi">
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-spinner fa-spin text-primary mr-2"></i>
                                    Membaca data koordinat GIS ArcMap...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="bws-table-footer">
                    <span id="tabel-footer-info">Memuat data koordinat...</span>
                    <span>
                        <i class="fas fa-sync-alt text-success mr-1"></i>
                        Otomatis sinkron dengan file yang diunggah
                    </span>
                </div>
            </div>

        </main>
    </div>


    {{-- MODAL POPUP (Upload, Kelola, Logout) --}}
    @include('dashboard.partials._modals')


    {{-- ============================================================
         SCRIPTS
         ============================================================ --}}
    <script src="{{ asset('sbadmin/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('sbadmin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.9.0/proj4.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

    {{-- Data dari Laravel ke JavaScript --}}
    <script>
        window.availableTypes = {!! json_encode($availableTypes) !!};
        window.geojsonFiles   = {!! json_encode($geojsonFiles ?? []) !!};
        window.fileList       = {!! json_encode($fileList ?? []) !!};
        window.canManageGis   = {{ (Auth::check() && in_array(Auth::user()->role, ['admin', 'superadmin'])) ? 'true' : 'false' }};
    </script>

    <script src="{{ asset('vendor/shpjs/shp.js') }}"></script>
    <script src="{{ asset('vendor/shpjs/togeojson.umd.js') }}"></script>
    <script src="{{ asset('js/peta-map.js') }}?v={{ filemtime(public_path('js/peta-map.js')) }}"></script>

    {{-- Script inline: Tab Switcher & Category Toggle & Wilayah Filter --}}
    <script>
    /* ===============================================
       TAB SWITCHER: Peta Interaktif / Tabel Data
       =============================================== */
    window.switchPetaTab = function(tab) {
        var mapSec = document.getElementById('section-map');
        var tblSec = document.getElementById('section-table');
        var tabPeta  = document.getElementById('tab-peta');
        var tabTabel = document.getElementById('tab-tabel');

        if (tab === 'peta') {
            mapSec.style.display = 'block';
            tblSec.style.display = 'none';
            tabPeta.classList.add('active');
            tabTabel.classList.remove('active');
            setTimeout(function() { if (window.map) window.map.invalidateSize(); }, 150);
        } else {
            mapSec.style.display = 'none';
            tblSec.style.display = 'block';
            tabPeta.classList.remove('active');
            tabTabel.classList.add('active');
        }
    };

    /* ===============================================
       RESET PETA: Kembali ke tampilan awal
       =============================================== */
    window.resetPetaView = function() {
        if (window.map) {
            window.map.setView([-0.02, 109.33], 8);
        }
        switchPetaTab('peta');
        // Reset semua toggle kategori ke aktif
        ['baku','fungsional','potensial','saluran','bangunan'].forEach(function(cat) {
            var btn = document.getElementById('toggle-' + cat);
            if (btn && !btn.classList.contains('active')) {
                toggleCategoryLayer(cat, btn);
            }
        });
    };

    /* ===============================================
       CATEGORY LAYER TOGGLE
       =============================================== */
    window.toggleCategoryLayer = function(category, btn) {
        var isActive = btn.classList.contains('active');
        var layerMap = {
            'baku'      : window.layerBaku,
            'fungsional': window.layerFungsional,
            'potensial' : window.layerPotensial,
            'saluran'   : window.layerSaluran,
            'bangunan'  : window.layerBangunan
        };
        var layer = layerMap[category];

        if (isActive) {
            // Sembunyikan layer
            btn.classList.remove('active');
            btn.innerHTML = '<i class="fas fa-times"></i>';
            btn.title = 'Klik untuk tampilkan';
            var catItem = document.getElementById('cat-' + category);
            if (catItem) catItem.classList.add('disabled');
            if (layer && window.map && window.map.hasLayer(layer)) {
                window.map.removeLayer(layer);
            }
        } else {
            // Tampilkan layer
            btn.classList.add('active');
            btn.innerHTML = '<i class="fas fa-check"></i>';
            btn.title = 'Klik untuk sembunyikan';
            var catItem2 = document.getElementById('cat-' + category);
            if (catItem2) catItem2.classList.remove('disabled');
            if (layer && window.map && !window.map.hasLayer(layer)) {
                window.map.addLayer(layer);
            }
        }
    };

    /* ===============================================
       FILTER WILAYAH (placeholder — extend sesuai data)
       =============================================== */
    window.filterByWilayah = function(wilayah) {
        if (typeof filterTabelKoordinat === 'function') {
            filterTabelKoordinat(wilayah);
        }
    };

    /* ===============================================
       SINKRON INPUT PENCARIAN (Sidebar <-> Tabel)
       =============================================== */
    window.syncSearchInputs = function(value) {
        var s1 = document.getElementById('cari-sidebar');
        var s2 = document.getElementById('cari-tabel-koordinat');
        if (s1 && s1 !== document.activeElement) s1.value = value;
        if (s2 && s2 !== document.activeElement) s2.value = value;
    };

    /* ===============================================
       SCROLL KE LEGENDA PETA
       =============================================== */
    window.scrollToLegend = function(e) {
        e.preventDefault();
        switchPetaTab('peta');
        setTimeout(function() {
            var legend = document.querySelector('.map-legend');
            if (legend) legend.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 200);
    };

    /* ===============================================
       FOKUS dari TABEL: Scroll ke peta tab
       =============================================== */
    // Override Fokus tombol agar juga switch tab ke peta
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('click', function(e) {
            if (e.target && e.target.closest('.btn-focus-row')) {
                switchPetaTab('peta');
            }
        }, true);
    });
    </script>

</body>
</html>
