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
                    {{-- Logged In: Dropdown Profil & Aksi Admin --}}
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
                             aria-labelledby="petaUserDrop" style="min-width:210px; border-radius:10px; border:1px solid #e2e8f0;">
                            <div class="dropdown-header text-uppercase font-weight-bold text-xs text-muted">
                                Akun Terdaftar
                            </div>
                            @if(in_array(Auth::user()->role, ['superadmin', 'admin']))
                            <a class="dropdown-item py-2 d-flex align-items-center"
                               href="{{ route('admin.panel') }}">
                                <i class="fas fa-cog text-muted mr-2" style="width:16px;"></i>
                                <span>Admin Panel</span>
                            </a>
                            <div class="dropdown-divider my-1"></div>
                            <div class="dropdown-header text-uppercase font-weight-bold text-xs text-muted">
                                Pengelolaan Spasial
                            </div>
                            <a class="dropdown-item py-2 d-flex align-items-center"
                               href="#" data-toggle="modal" data-target="#modalKelolaGeojson">
                                <i class="fas fa-folder-open text-primary mr-2" style="width:16px;"></i>
                                <span>Kelola File GIS</span>
                                <span class="badge badge-primary ml-auto">{{ count($fileList ?? []) }}</span>
                            </a>
                            <a class="dropdown-item py-2 d-flex align-items-center"
                               href="#" data-toggle="modal" data-target="#modalUploadGeojson">
                                <i class="fas fa-cloud-upload-alt text-success mr-2" style="width:16px;"></i>
                                <span>Upload File GIS</span>
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
                    di Kalimantan Barat dalam satu peta interaktif.
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
         LAYOUT UTAMA: PETA INTERAKTIF LAYAR PENUH DENGAN FLOATING SEARCH
         ============================================================ --}}
    <div class="peta-v2-container">

        <main class="peta-v2-main">

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show m-2 shadow-sm" role="alert" style="border-radius:6px; font-size:12px; position:absolute; top:10px; right:20px; z-index:1050; max-width:400px;">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                <button type="button" class="close py-2" data-dismiss="alert"><span>&times;</span></button>
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show m-2 shadow-sm" role="alert" style="border-radius:6px; font-size:12px; position:absolute; top:10px; right:20px; z-index:1050; max-width:400px;">
                <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
                <button type="button" class="close py-2" data-dismiss="alert"><span>&times;</span></button>
            </div>
            @endif

            {{-- SECTION PETA DENGAN FLOATING SEARCH BAR --}}
            <div id="section-map" class="peta-v2-map-wrap">

                {{-- Indikator Loading GIS --}}
                <div class="bws-map-loading" id="map-loading">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>Memuat koordinat GIS ArcMap...</span>
                </div>

                {{-- WIDGET PENCARIAN MODERN MENGAMBANG (FLOATING GLASS SEARCH BAR) --}}
                <div class="bws-floating-search-bar" id="bwsFloatingSearch">
                    <div class="bws-search-input-box">
                        <i class="fas fa-search bws-search-icon"></i>
                        <input type="text"
                               id="peta-search-input"
                               class="bws-search-input"
                               placeholder="Cari pos atau daerah irigasi..."
                               autocomplete="off"
                               spellcheck="false">
                        <button type="button"
                                class="bws-search-clear-btn"
                                id="peta-search-clear"
                                title="Hapus pencarian"
                                style="display:none;">
                            <i class="fas fa-times"></i>
                        </button>
                        <div class="bws-search-divider"></div>
                        <button type="button"
                                class="bws-search-reset-btn"
                                id="peta-search-reset"
                                onclick="resetPetaView()"
                                title="Reset posisi peta ke Kalimantan Barat">
                            <i class="fas fa-redo-alt"></i>
                            <span>Reset</span>
                        </button>
                    </div>

                    {{-- DROPDOWN HASIL PENCARIAN REALTIME --}}
                    <div class="bws-search-results-dropdown" id="peta-search-dropdown" style="display:none;">
                        <div class="bws-search-dropdown-header">
                            <span id="peta-search-count">0 hasil ditemukan</span>
                            <small class="text-muted">Klik item untuk fokus ke lokasi</small>
                        </div>
                        <div class="bws-search-results-list" id="peta-search-list">
                            {{-- Diisi secara dinamis via JavaScript saat user mengetik --}}
                        </div>
                    </div>
                </div>

                {{-- ELEMEN CANVAS PETA LEAFLET --}}
                <div id="map"></div>
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

    {{-- Script inline: Reset View Helper --}}
    <script>
    /* ===============================================
       RESET PETA: Kembali ke koordinat Kalimantan Barat
       =============================================== */
    window.resetPetaView = function() {
        if (window.map) {
            window.map.setView([-0.0263, 109.3425], 8);
        }
        var searchInput = document.getElementById('peta-search-input');
        if (searchInput) searchInput.value = '';
        var clearBtn = document.getElementById('peta-search-clear');
        if (clearBtn) clearBtn.style.display = 'none';
        var dropdown = document.getElementById('peta-search-dropdown');
        if (dropdown) dropdown.style.display = 'none';
    };
    </script>

</body>
</html>
