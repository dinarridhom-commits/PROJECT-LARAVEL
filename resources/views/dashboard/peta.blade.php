<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>BALAI WILAYAH SUNGAI KALIMANTAN I - Peta Daerah Irigasi (DIR)</title>

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
         1. TOPBAR & SUB-NAVBAR HORIZONTAL BWS KALIMANTAN I
         Berisi: Logo PU, Teks Instansi, Fullscreen, Notifikasi,
         Profil User, serta Menu Horizontal (Dashboard, Master Data
         Infrastruktur, Manajemen User, Log Aktivitas).
         Partial: resources/views/layout/_top_nav.blade.php
         ============================================================ --}}
    @include('layout._top_nav')


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

                        {{-- Tombol Export CSV & Unduh GeoJSON (Khusus Admin & Superadmin) --}}
                        @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'superadmin']))
                        {{-- Export ke CSV / Excel --}}
                        <button type="button"
                                class="btn-export-green"
                                onclick="exportDataToCsv()"
                                title="Unduh data dalam format CSV/Excel">
                            <i class="fas fa-file-excel"></i>
                            <span>Export CSV</span>
                        </button>

                        {{-- Unduh / Export GeoJSON Per Kategori (.ZIP) --}}
                        <div class="dropdown d-inline-block">
                            <button type="button"
                                    class="btn btn-sm btn-primary dropdown-toggle font-weight-bold shadow-sm px-3"
                                    data-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false"
                                    style="height: 38px; border-radius: 6px; font-size: 13px;">
                                <i class="fas fa-file-download mr-1"></i>
                                <span>Unduh GeoJSON</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right shadow border-0 py-2" style="border-radius: 8px; min-width: 250px; z-index: 1060;">
                                <div class="dropdown-header text-uppercase font-weight-bold text-xs text-muted">
                                    Unduh Arsip GeoJSON (.ZIP)
                                </div>
                                <a class="dropdown-item py-2 d-flex align-items-center justify-content-between" href="{{ route('peta.download-category', 'all') }}">
                                    <span><i class="fas fa-archive text-primary mr-2"></i> Semua Data GIS</span>
                                    <span class="badge badge-primary px-2">{{ count($fileList) }}</span>
                                </a>
                                <div class="dropdown-divider my-1"></div>
                                <a class="dropdown-item py-2 d-flex align-items-center justify-content-between" href="{{ route('peta.download-category', 'baku') }}">
                                    <span class="d-flex align-items-center"><span class="bws-flyout-badge mr-2" style="background:#27ae60;"></span> DIR Baku</span>
                                    <span class="badge badge-light border text-muted">9 File</span>
                                </a>
                                <a class="dropdown-item py-2 d-flex align-items-center justify-content-between" href="{{ route('peta.download-category', 'fungsional') }}">
                                    <span class="d-flex align-items-center"><span class="bws-flyout-badge mr-2" style="background:#2980b9;"></span> DIR Fungsional</span>
                                    <span class="badge badge-light border text-muted">6 File</span>
                                </a>
                                <a class="dropdown-item py-2 d-flex align-items-center justify-content-between" href="{{ route('peta.download-category', 'potensial') }}">
                                    <span class="d-flex align-items-center"><span class="bws-flyout-badge mr-2" style="background:#e67e22;"></span> DIR Potensial</span>
                                    <span class="badge badge-light border text-muted">10 File</span>
                                </a>
                                <div class="dropdown-divider my-1"></div>
                                <a class="dropdown-item py-2 d-flex align-items-center justify-content-between" href="{{ route('peta.download-category', 'saluran') }}">
                                    <span class="d-flex align-items-center"><span class="bws-flyout-badge mr-2" style="background:#0984e3;"></span> Saluran Irigasi</span>
                                    <span class="badge badge-light border text-muted">15 File</span>
                                </a>
                                <a class="dropdown-item py-2 d-flex align-items-center justify-content-between" href="{{ route('peta.download-category', 'bangunan') }}">
                                    <span class="d-flex align-items-center"><span class="bws-flyout-badge mr-2" style="background:#d63031;"></span> Bangunan Air</span>
                                    <span class="badge badge-light border text-muted">14 File</span>
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Tabel Data GIS (Dark Header) --}}
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
        window.fileList       = {!! json_encode($fileList ?? []) !!};
        window.canManageGis   = {{ (Auth::check() && in_array(Auth::user()->role, ['admin', 'superadmin'])) ? 'true' : 'false' }};
    </script>

    {{-- Konverter Otomatis File Spasial: Shapefile (.zip) & KML/KMZ ke GeoJSON --}}
    <script src="{{ asset('vendor/shpjs/shp.js') }}"></script>
    <script src="{{ asset('vendor/shpjs/togeojson.umd.js') }}"></script>

    {{-- JavaScript utama peta (dipisah ke public/js/peta-map.js) --}}
    <script src="{{ asset('js/peta-map.js') }}?v={{ filemtime(public_path('js/peta-map.js')) }}"></script>

</body>
</html>
