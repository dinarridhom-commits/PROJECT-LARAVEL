<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>BWS KALIMANTAN I - Peta Daerah Irigasi (DIR)</title>

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

    {{-- CSS Khusus Peta (dipisah ke public/css/peta.css) --}}
    <link rel="stylesheet" href="{{ asset('css/peta.css') }}?v={{ filemtime(public_path('css/peta.css')) }}">
</head>
<body>

    {{-- ============================================================
         1. TOPBAR NAVBAR RESMI BWS KALIMANTAN I
         Berisi: tombol reset peta, logo PU, nama instansi,
         tombol fullscreen, notifikasi jumlah file, dan profil user.
         ============================================================ --}}
    <header class="bws-navbar">
        <div class="bws-navbar-inner">
            <div class="bws-nav-left">
                {{-- Tombol Reset / Home Tampilan Peta --}}
                <button type="button" class="bws-back-btn" onclick="resetMapView()" title="Reset Tampilan Peta ke Awal">
                    <i class="fas fa-chevron-left"></i>
                </button>

                {{-- Logo PU & Teks Instansi --}}
                <div class="bws-brand-wrapper">
                    <img src="{{ asset('sbadmin/img/logo-pu.jpeg') }}" alt="Logo Kementerian PU" class="bws-logo-pu">
                    <div class="bws-brand-text">
                        <div class="bws-brand-title">WEBGIS BWS KALIMANTAN I</div>
                        <div class="bws-brand-subtitle">Direktorat Jenderal Sumber Daya Air</div>
                        <div class="bws-brand-dept">Kementerian Pekerjaan Umum</div>
                    </div>
                </div>
            </div>

            <div class="bws-nav-right">
                {{-- Tombol Fullscreen --}}
                <button type="button" class="bws-header-btn" id="btnFullscreenToggle" title="Tampilan Layar Penuh">
                    <i class="fas fa-expand"></i>
                </button>

                {{-- Badge jumlah file GIS aktif --}}
                <div class="position-relative">
                    <button type="button" class="bws-header-btn" title="Total File GIS Tersimpan">
                        <i class="fas fa-bell"></i>
                        <span class="bws-badge-pill" id="header-file-count">{{ count($fileList ?? []) }}</span>
                    </button>
                </div>

                {{-- Dropdown profil user --}}
                <div class="dropdown">
                    <div class="bws-profile-pill" id="userMenuDropdown"
                         data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="bws-profile-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <span class="font-weight-bold">{{ Auth::user()->name ?? 'Admin Dinar' }}</span>
                        <i class="fas fa-chevron-down ml-1 small text-white-50"></i>
                    </div>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in py-2"
                         aria-labelledby="userMenuDropdown">
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
                        <a class="dropdown-item text-danger py-2" href="#"
                           data-toggle="modal" data-target="#logoutModal">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2"></i>Keluar / Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
        {{-- Garis Aksen Emas PU --}}
        <div class="bws-accent-strip"></div>
    </header>


    {{-- ============================================================
         2. LAYOUT UTAMA: SIDEBAR FILTER KIRI + KONTEN PETA & TABEL
         ============================================================ --}}
    <div class="bws-page-container">

        {{-- PANEL FILTER KIRI (Dashboard Type, Tanggal, View, tombol GIS)
             Partial: resources/views/layout/_sidebar.blade.php --}}
        @include('layout._sidebar')


        {{-- AREA KONTEN UTAMA (SISI KANAN) --}}
        <main class="bws-content">

            {{-- Breadcrumb --}}
            <div class="bws-breadcrumb">
                <span class="bws-breadcrumb-parent">Dashboard Data</span>
                <i class="fas fa-chevron-right bws-breadcrumb-arrow"></i>
                <span class="bws-breadcrumb-current">Peta Daerah Irigasi (DIR)</span>
            </div>

            {{-- Flash Alert Sukses --}}
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm mb-0" role="alert">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            {{-- Flash Alert Error --}}
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-0" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif


            {{-- SECTION 1: MAP CARD (Leaflet Interactive Map) --}}
            <div class="bws-map-card" id="section-map">
                <div class="bws-map-loading" id="map-loading">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>Memuat koordinat GIS ArcMap...</span>
                </div>
                <div id="map"></div>
            </div>


            {{-- SECTION 2: DATA TABLE CARD --}}
            <div class="bws-table-card" id="section-table">
                <div class="bws-table-toolbar">
                    <div class="bws-table-title">
                        <span>Daftar Pos Daerah Irigasi &amp; Data Spasial</span>
                        <span class="badge badge-light border text-muted px-2 py-1"
                              style="font-size:12px;" id="badge-total-file-tabel">
                            {{ count($fileList) }} File
                        </span>
                    </div>

                    <div class="bws-table-controls">
                        {{-- Pencarian Realtime --}}
                        <div class="bws-search-input-wrap">
                            <i class="fas fa-search"></i>
                            <input type="text"
                                   id="cari-tabel-koordinat"
                                   placeholder="Cari nama pos/objek/file..."
                                   onkeyup="filterTabelKoordinat(this.value)">
                        </div>

                        {{-- Export ke CSV --}}
                        <button type="button"
                                class="btn-export-green"
                                onclick="exportDataToCsv()"
                                title="Unduh data dalam format CSV/Excel">
                            <i class="fas fa-file-excel"></i>
                            <span>Export Data</span>
                        </button>
                    </div>
                </div>

                {{-- Tabel Data GIS (Dark Header) --}}
                <div class="bws-table-responsive">
                    <table class="bws-custom-table" id="tabel-lokasi">
                        <thead>
                            <tr>
                                <th width="5%"  class="text-center">No.</th>
                                <th width="28%">Nama Pos / Objek</th>
                                <th width="18%">Wilayah / Kategori</th>
                                <th width="12%" class="text-center">Latitude (Y)</th>
                                <th width="12%" class="text-center">Longitude (X)</th>
                                <th width="17%">Sumber File GIS</th>
                                <th width="8%"  class="text-center">Aksi</th>
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


    {{-- MODAL POPUP (Upload GeoJSON, Kelola File GIS, Logout)
         Partial: resources/views/dashboard/partials/_modals.blade.php --}}
    @include('dashboard.partials._modals')


    {{-- ============================================================
         SCRIPTS
         ============================================================ --}}

    {{-- Core Libraries --}}
    <script src="{{ asset('sbadmin/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('sbadmin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.9.0/proj4.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

    {{-- Data dari Laravel dikirim ke JavaScript sebagai variabel global.
         Harus diletakkan SEBELUM peta-map.js agar bisa diakses oleh script tersebut. --}}
    <script>
        window.availableTypes = {!! json_encode($availableTypes) !!};
        window.geojsonFiles   = {!! json_encode($geojsonFiles ?? []) !!};
    </script>

    {{-- JavaScript utama peta (dipisah ke public/js/peta-map.js) --}}
    <script src="{{ asset('js/peta-map.js') }}?v={{ filemtime(public_path('js/peta-map.js')) }}"></script>

</body>
</html>
