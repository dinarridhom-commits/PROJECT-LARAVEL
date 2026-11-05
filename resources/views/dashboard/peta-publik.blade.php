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

    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>

    {{-- CSS Khusus Peta --}}
    <link rel="stylesheet" href="{{ asset('css/peta.css') }}?v={{ filemtime(public_path('css/peta.css')) }}">
</head>
<body class="page-peta-v2">

    {{-- ============================================================
         NAVBAR PUTIH — HALAMAN PUBLIK (Belum Login)
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

            {{-- Kanan: Tombol Masuk (selalu tampil di halaman publik) --}}
            <div class="peta-navbar-right">
                <a href="{{ route('login') }}" class="peta-btn-masuk">
                    <i class="fas fa-sign-in-alt mr-1"></i> Masuk
                </a>
            </div>
        </div>
    </header>


    {{-- ============================================================
         HERO BANNER
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
         LAYOUT UTAMA: SIDEBAR KIRI + AREA PETA
         ============================================================ --}}
    <div class="peta-v2-container">

        {{-- ========================
             SIDEBAR FILTER KIRI (PUBLIK — hanya Search)
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
                           autocomplete="off">
                    <button type="button" id="cari-sidebar-clear" class="psb-search-clear" title="Hapus pencarian">
                        <i class="fas fa-times"></i>
                    </button>
                    {{-- Dropdown Hasil Pencarian Autocomplete --}}
                    <div id="cari-sidebar-dropdown" class="psb-search-dropdown" style="display:none;">
                        <div class="psb-search-dropdown-header">
                            <span id="cari-sidebar-count">0 hasil</span>
                            <span class="text-muted" style="font-size:10px;">Klik untuk lihat di peta</span>
                        </div>
                        <div id="cari-sidebar-list" class="psb-search-list"></div>
                    </div>
                </div>
                <div class="psb-search-hint">
                    Pilih hasil pencarian untuk melihat detail. Maksimal 150 karakter
                </div>
            </div>

            {{-- Info Footer --}}
            <div class="psb-footer-info">
                <i class="fas fa-info-circle mr-1"></i>
                Klik ikon di peta untuk melihat info, luas, dan koordinat akurat daerah irigasi
            </div>

        </aside>


        {{-- ========================
             AREA PETA
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

                {{-- Tab Kanan (Publik: hanya Peta Interaktif & Reset) --}}
                <div class="ptb-tabs">
                    <button class="ptb-tab active" id="tab-peta" onclick="switchPetaTab('peta')">
                        <i class="fas fa-map mr-1"></i> Peta Interaktif
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

        </main>
    </div>


    {{-- MODAL LOGOUT (tidak dipakai di halaman publik, tapi tetap include agar tidak error jika modal dipanggil) --}}
    {{-- @include('dashboard.partials._modals') --}}


    {{-- ============================================================
         SCRIPTS
         ============================================================ --}}
    <script src="{{ asset('sbadmin/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('sbadmin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.9.0/proj4.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

    {{-- Data dari Laravel ke JavaScript --}}
    <script>
        window.availableTypes = {!! json_encode($availableTypes) !!};
        window.geojsonFiles   = {!! json_encode($geojsonFiles ?? []) !!};
        window.fileList       = {!! json_encode($fileList ?? []) !!};
        window.canManageGis   = false; {{-- Halaman publik tidak bisa manage GIS --}}
    </script>

    <script src="{{ asset('vendor/shpjs/shp.js') }}"></script>
    <script src="{{ asset('vendor/shpjs/togeojson.umd.js') }}"></script>
    <script src="{{ asset('js/peta-map.js') }}?v={{ filemtime(public_path('js/peta-map.js')) }}"></script>

    <script>
    /* Tab Switcher (hanya peta, tidak ada tabel di halaman publik) */
    window.switchPetaTab = function(tab) {
        var mapSec = document.getElementById('section-map');
        if (mapSec) mapSec.style.display = 'block';
        setTimeout(function() { if (window.map) window.map.invalidateSize(); }, 150);
    };

    /* Reset Peta */
    window.resetPetaView = function() {
        if (window.map) {
            window.map.setView([-0.02, 109.33], 8);
            if (window.layerBaku && !window.map.hasLayer(window.layerBaku)) window.map.addLayer(window.layerBaku);
            if (window.layerFungsional && !window.map.hasLayer(window.layerFungsional)) window.map.addLayer(window.layerFungsional);
            if (window.layerPotensial && !window.map.hasLayer(window.layerPotensial)) window.map.addLayer(window.layerPotensial);
            if (window.layerSaluran && window.map.hasLayer(window.layerSaluran)) window.map.removeLayer(window.layerSaluran);
            if (window.layerBangunan && window.map.hasLayer(window.layerBangunan)) window.map.removeLayer(window.layerBangunan);
        }
        var inp = document.getElementById('cari-sidebar');
        if (inp) inp.value = '';
        var clr = document.getElementById('cari-sidebar-clear');
        if (clr) clr.style.display = 'none';
        var drop = document.getElementById('cari-sidebar-dropdown');
        if (drop) drop.style.display = 'none';
    };

    /* Sinkron Input Pencarian */
    window.syncSearchInputs = function(value) {
        var s1 = document.getElementById('cari-sidebar');
        if (s1 && s1 !== document.activeElement) s1.value = value;
    };

    /* Scroll ke Legenda */
    window.scrollToLegend = function(e) {
        if (e) e.preventDefault();
        var legend = document.querySelector('.map-legend');
        if (legend) legend.scrollIntoView({ behavior: 'smooth', block: 'center' });
    };
    </script>

</body>
</html>

