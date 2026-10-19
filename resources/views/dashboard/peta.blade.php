{{--
    peta.blade.php
    Halaman Peta Daerah Irigasi (DIR) - BWS Kalimantan I
    Menggunakan layout master SB Admin (sidebar + navbar sudah dari master)
--}}
@extends('layout.master')

@section('title', 'Peta Daerah Irigasi (DIR) - BWS Kalimantan I')

{{-- Tambahkan CSS khusus peta setelah CSS master --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/peta.css') }}">
    <style>
        /*
         * Override kecil agar layout peta muat di dalam container-fluid master
         * bws-page-container tidak perlu min-height penuh lagi karena
         * sudah ada wrapper dari master layout
         */
        .bws-page-container {
            min-height: calc(100vh - 200px);
            margin: -1.5rem -0.75rem 0 -0.75rem; /* Negatif agar keluar dari padding container-fluid */
        }

        /* Penyesuaian tinggi peta agar pas di dalam master layout */
        #map {
            height: 420px;
        }

        /* Sidebar filter sedikit lebih kompak karena sudah ada nav sidebar master */
        .bws-sidebar {
            width: 260px;
            min-width: 260px;
            max-width: 260px;
        }
    </style>
@endpush


@section('konten')

    {{-- ============================================================
         LAYOUT UTAMA: PANEL FILTER KIRI + KONTEN PETA & TABEL KANAN
         Catatan: Navbar & Sidebar navigasi sudah dari layout.master.
         Di sini hanya panel filter GIS + konten peta + tabel.
         ============================================================ --}}
    <div class="bws-page-container">

        {{-- PANEL FILTER KIRI (Dashboard Type, Tanggal, View, tombol GIS)
             Partial: resources/views/dashboard/partials/_sidebar.blade.php --}}
        @include('dashboard.partials._sidebar')


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

@endsection


{{-- Script khusus peta, di-push ke @stack('scripts') di master.blade.php --}}
@push('scripts')
    {{-- Proj4js: Konversi sistem koordinat ArcMap/ESRI ke WGS84
         (belum dimuat di master.blade.php, jadi perlu ditambah di sini) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.9.0/proj4.js"></script>

    {{-- Data dari Laravel dikirim ke JavaScript sebagai variabel global.
         Harus diletakkan SEBELUM peta-map.js agar bisa diakses oleh script tersebut.
         - availableTypes : kategori layer GIS yang tersedia (baku/fungsional/potensial/dll)
         - geojsonFiles   : daftar nama file GeoJSON di folder public/geojson/ --}}
    <script>
        window.availableTypes = {!! json_encode($availableTypes) !!};
        window.geojsonFiles   = {!! json_encode($geojsonFiles ?? []) !!};
    </script>

    {{-- JavaScript utama peta (dipisah ke public/js/peta-map.js)
         Catatan: jQuery, Bootstrap, dan Leaflet.js sudah dimuat di master.blade.php
         sehingga tidak perlu dimuat ulang di sini. --}}
    <script src="{{ asset('js/peta-map.js') }}"></script>
@endpush
