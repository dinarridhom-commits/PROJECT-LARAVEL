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
<body class="page-peta-v2 page-peta-publik">

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

            {{-- Kanan: Link Tentang Layanan, Petunjuk Pengguna, & Tombol Masuk --}}
            <div class="peta-navbar-right">
                <button type="button" class="peta-top-link" data-toggle="modal" data-target="#modalTentangLayanan">
                    Tentang Layanan
                </button>
                <button type="button" class="peta-top-link" data-toggle="modal" data-target="#modalPetunjukPengguna">
                    Petunjuk Pengguna
                </button>
                <a href="{{ route('login') }}" class="peta-btn-masuk-outline">
                    <i class="fas fa-sign-in-alt"></i> Masuk
                </a>
            </div>
        </div>
    </header>


    {{-- ============================================================
         HERO BANNER (GAYA HIJAU ELEGAN SESUAI REFERENSI)
         ============================================================ --}}
    <section class="peta-hero-wrap">
        <div class="peta-hero-card">
            <div class="peta-hero-content">
                <div class="peta-hero-badge">
                    <span class="badge-dot"></span> DATA SPASIAL BWS KALIMANTAN I
                </div>
                <h1 class="peta-hero-title">Peta Daerah Irigasi &amp; Jaringan SDA.</h1>
                <p class="peta-hero-desc">
                    Temukan sebaran Daerah Irigasi, Jaringan Saluran Air, dan Bangunan Air di Kalimantan Barat dalam satu peta.
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
                <div class="psb-header-text">
                    <div class="psb-header-title">Jelajahi objek irigasi</div>
                    <div class="psb-header-sub">Cari pos atau daerah irigasi</div>
                </div>
                <button type="button" class="psb-btn-collapse" id="btnCollapseSidebar" onclick="toggleSidebarView()" title="Sembunyikan Sidebar">
                    <i class="fas fa-chevron-left"></i>
                </button>
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

            {{-- Kategori & Layer Peta (Sesuai Gambar No 1) --}}
            <div class="psb-categories-section">
                <div class="psb-categories-header">
                    <span class="psb-categories-title">Tampilkan kategori</span>
                    <span class="psb-categories-subtitle">Layer peta</span>
                </div>
                <div class="psb-category-list">
                    {{-- 1. DIR Baku (Hijau) --}}
                    <div class="psb-category-item active" id="cat-baku" onclick="toggleSidebarCategory('baku', event)">
                        <div class="psb-category-left">
                            <div class="psb-category-icon cat-icon-baku">
                                <i class="fas fa-seedling"></i>
                            </div>
                            <span class="psb-category-name">Daerah Irigasi Baku</span>
                        </div>
                        <button type="button" class="psb-category-toggle-btn active" id="btn-toggle-baku" aria-label="Toggle DIR Baku">
                            <i class="fas fa-check"></i>
                        </button>
                    </div>

                    {{-- 2. DIR Fungsional (Biru) --}}
                    <div class="psb-category-item active" id="cat-fungsional" onclick="toggleSidebarCategory('fungsional', event)">
                        <div class="psb-category-left">
                            <div class="psb-category-icon cat-icon-fungsional">
                                <i class="fas fa-water"></i>
                            </div>
                            <span class="psb-category-name">Daerah Irigasi Fungsional</span>
                        </div>
                        <button type="button" class="psb-category-toggle-btn active" id="btn-toggle-fungsional" aria-label="Toggle DIR Fungsional">
                            <i class="fas fa-check"></i>
                        </button>
                    </div>

                    {{-- 3. DIR Potensial (Oranye) --}}
                    <div class="psb-category-item active" id="cat-potensial" onclick="toggleSidebarCategory('potensial', event)">
                        <div class="psb-category-left">
                            <div class="psb-category-icon cat-icon-potensial">
                                <i class="fas fa-chart-area"></i>
                            </div>
                            <span class="psb-category-name">Daerah Irigasi Potensial</span>
                        </div>
                        <button type="button" class="psb-category-toggle-btn active" id="btn-toggle-potensial" aria-label="Toggle DIR Potensial">
                            <i class="fas fa-check"></i>
                        </button>
                    </div>

                    {{-- 4. Jaringan Saluran Irigasi (Ungu/Cyan) --}}
                    <div class="psb-category-item disabled" id="cat-saluran" onclick="toggleSidebarCategory('saluran', event)">
                        <div class="psb-category-left">
                            <div class="psb-category-icon cat-icon-saluran">
                                <i class="fas fa-route"></i>
                            </div>
                            <span class="psb-category-name">Jaringan Saluran Irigasi</span>
                        </div>
                        <button type="button" class="psb-category-toggle-btn" id="btn-toggle-saluran" aria-label="Toggle Saluran">
                            <i class="fas fa-check"></i>
                        </button>
                    </div>

                    {{-- 5. Bangunan Air (Merah/Pink) --}}
                    <div class="psb-category-item disabled" id="cat-bangunan" onclick="toggleSidebarCategory('bangunan', event)">
                        <div class="psb-category-left">
                            <div class="psb-category-icon cat-icon-bangunan">
                                <i class="fas fa-archway"></i>
                            </div>
                            <span class="psb-category-name">Bangunan Air / Pelengkap</span>
                        </div>
                        <button type="button" class="psb-category-toggle-btn" id="btn-toggle-bangunan" aria-label="Toggle Bangunan Air">
                            <i class="fas fa-check"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Footer Identitas Lembaga Sesuai Gambar Referensi --}}
            <div class="psb-footer mt-auto pt-3">
                <div class="psb-footer-box">
                    <i class="fas fa-landmark psb-footer-icon"></i>
                    <div>
                        <div class="psb-footer-title">BWS Kalimantan I Pontianak</div>
                        <div class="psb-footer-sub">Sistem Informasi Geospasial SDA</div>
                    </div>
                </div>
            </div>

        </aside>


        {{-- ========================
             AREA PETA
             ======================== --}}
        <main class="peta-v2-main">

            {{-- TAB BAR --}}
            <div class="peta-tab-bar">
                {{-- Breadcrumb Kiri (Sesuai Desain Gambar Referensi: • Peta Daerah Irigasi / Kalimantan Barat) --}}
                <div class="ptb-breadcrumb">
                    <button type="button" class="ptb-btn-sidebar" id="btnExpandSidebar" onclick="toggleSidebarView()" title="Buka Sidebar">
                        <i class="fas fa-indent mr-1"></i> Buka Sidebar
                    </button>
                    <span class="ptb-bullet-dot"></span>
                    <span class="ptb-title-main">Peta Daerah Irigasi</span>
                    <span class="ptb-sep">/</span>
                    <span class="ptb-title-sub">Kalimantan Barat</span>
                </div>

                {{-- Tab Kanan --}}
                <div class="ptb-tabs">
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

            {{-- Footer Status Peta Sesuai Gambar Referensi --}}
            <div class="peta-map-footer">
                <span class="pmf-left">Peta siap. Silakan cari pos atau nama daerah irigasi.</span>
                <span class="pmf-right">Geser untuk menjelajah <span class="pmf-dot">&bull;</span> Klik objek untuk detail</span>
            </div>

        </main>
    </div>


    {{-- ============================================================
         SECTION TENTANG LAYANAN (PERSIS SEPERTI GAMBAR REFERENSI)
         ============================================================ --}}
    <section class="peta-about-section">
        <div class="peta-about-card">
            <div class="peta-about-brand">
                <div class="peta-about-logo-box">
                    <img src="{{ asset('sbadmin/img/logo-pu.jpeg') }}" alt="Logo BWS Kalimantan I">
                </div>
                <div>
                    <div class="peta-about-brand-title">WEBGIS</div>
                    <div class="peta-about-brand-sub">BWS KALIMANTAN I</div>
                </div>
            </div>
            <div class="peta-about-content">
                <span class="peta-about-badge">TENTANG LAYANAN</span>
                <h2 class="peta-about-title">
                    Informasi daerah irigasi &amp; bangunan air, lebih dekat dan mudah dipahami.
                </h2>
                <p class="peta-about-desc">
                    WebGIS BWS Kalimantan I merupakan sistem informasi data geospasial Balai Wilayah Sungai Kalimantan I Pontianak. Halaman ini menyajikan peta sebaran Daerah Irigasi (DIR), jaringan saluran air, dan titik bangunan air di wilayah Provinsi Kalimantan Barat.
                </p>
                <a href="javascript:void(0)" class="peta-about-link" data-toggle="modal" data-target="#modalPetunjukPengguna">
                    <span>Cara menggunakan peta</span>
                    <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </section>


    {{-- ============================================================
         MODAL 1: PETUNJUK PENGGUNA
         File terpisah: dashboard/partials/_modal_petunjuk_pengguna.blade.php
         ============================================================ --}}
    @include('dashboard.partials._modal_petunjuk_pengguna')


    {{-- ============================================================
         MODAL 2: TENTANG LAYANAN
         File terpisah: dashboard/partials/_modal_tentang_layanan.blade.php
         ============================================================ --}}
    @include('dashboard.partials._modal_tentang_layanan')


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
    /* Toggle Sembunyikan / Tampilkan Sidebar (Peta Menyesuaikan Penuh) */
    window.toggleSidebarView = function() {
        var container = document.querySelector('.peta-v2-container');
        if (!container) return;
        container.classList.toggle('sidebar-collapsed');
        if (window.map) {
            window.map.invalidateSize();
            var t = 0;
            var timer = setInterval(function() {
                if (window.map) window.map.invalidateSize();
                t += 40;
                if (t >= 320) clearInterval(timer);
            }, 40);
        }
    };

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

    /* Toggle Kategori Sidebar (Sesuai Gambar 1) */
    window.toggleSidebarCategory = function(category, event) {
        if (event) event.stopPropagation();
        var item = document.getElementById('cat-' + category);
        var btn = document.getElementById('btn-toggle-' + category);
        if (!item || !btn) return;

        var layerMap = {
            'baku'      : window.layerBaku,
            'fungsional': window.layerFungsional,
            'potensial' : window.layerPotensial,
            'saluran'   : window.layerSaluran,
            'bangunan'  : window.layerBangunan
        };
        var layer = layerMap[category];
        var isCurrentlyActive = item.classList.contains('active');

        if (isCurrentlyActive) {
            item.classList.remove('active');
            item.classList.add('disabled');
            btn.classList.remove('active');
            if (layer && window.map && window.map.hasLayer(layer)) {
                window.map.removeLayer(layer);
            }
        } else {
            item.classList.remove('disabled');
            item.classList.add('active');
            btn.classList.add('active');
            if (layer && window.map && !window.map.hasLayer(layer)) {
                window.map.addLayer(layer);
            }
        }
    };

    /* Switch Tab di dalam Modal Panduan Pengguna */
    window.switchGuideTab = function(tabId, btn) {
        var panes = document.querySelectorAll('.guide-tab-pane');
        panes.forEach(function(p) { p.style.display = 'none'; });

        var target = document.getElementById(tabId);
        if (target) target.style.display = 'block';

        var buttons = document.querySelectorAll('.guide-pill-btn');
        buttons.forEach(function(b) { b.classList.remove('active'); });

        if (btn) btn.classList.add('active');
    };
    </script>

</body>
</html>

