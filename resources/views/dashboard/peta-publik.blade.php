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

            {{-- Kanan: Petunjuk Pengguna, Tentang Layanan, & Tombol Masuk --}}
            <div class="peta-navbar-right">
                <button type="button" class="peta-nav-btn" data-toggle="modal" data-target="#modalPetunjukPengguna" title="Petunjuk Penggunaan WebGIS">
                    <i class="fas fa-book-reader text-primary"></i>
                    <span>Petunjuk Pengguna</span>
                </button>
                <button type="button" class="peta-nav-btn" data-toggle="modal" data-target="#modalTentangLayanan" title="Tentang Layanan BWS Kalimantan I">
                    <i class="fas fa-info-circle text-info"></i>
                    <span>Tentang Layanan</span>
                </button>
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
                <div class="flex-grow-1">
                    <div class="psb-header-title">Jelajahi objek irigasi</div>
                    <div class="psb-header-sub">Mulai dari nama atau No Foto spasial</div>
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

        </aside>


        {{-- ========================
             AREA PETA
             ======================== --}}
        <main class="peta-v2-main">

            {{-- TAB BAR --}}
            <div class="peta-tab-bar">
                {{-- Breadcrumb Kiri --}}
                <div class="ptb-breadcrumb">
                    <button type="button" class="ptb-btn-sidebar" id="btnExpandSidebar" onclick="toggleSidebarView()" title="Buka Sidebar">
                        <i class="fas fa-indent mr-1"></i> Buka Sidebar
                    </button>
                    <i class="fas fa-map-marked-alt mr-1 text-primary" style="font-size:13px;"></i>
                    <span>Peta Daerah Irigasi</span>
                    <i class="fas fa-chevron-right mx-1" style="font-size:10px; color:#94a3b8;"></i>
                    <span>Kalimantan Barat</span>
                </div>

                {{-- Tab Kanan (Publik: Peta Interaktif, Panduan, Layanan, & Reset) --}}
                <div class="ptb-tabs">
                    <button class="ptb-tab active" id="tab-peta" onclick="switchPetaTab('peta')">
                        <i class="fas fa-map mr-1"></i> Peta Interaktif
                    </button>
                    <button type="button" class="ptb-tab-link" data-toggle="modal" data-target="#modalPetunjukPengguna" title="Petunjuk Penggunaan WebGIS">
                        <i class="fas fa-book-reader mr-1 text-primary"></i> Panduan
                    </button>
                    <button type="button" class="ptb-tab-link" data-toggle="modal" data-target="#modalTentangLayanan" title="Tentang Layanan BWS Kalimantan I">
                        <i class="fas fa-info-circle mr-1 text-info"></i> Layanan
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
         MODAL 1: PETUNJUK PENGGUNA (PANDUAN INTERAKTIF WEBGIS)
         ============================================================ --}}
    <div class="modal fade" id="modalPetunjukPengguna" tabindex="-1" role="dialog" aria-labelledby="titlePetunjukPengguna" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden; box-shadow: 0 20px 50px rgba(15,23,42,0.3);">
                
                {{-- Header Modal --}}
                <div class="modal-guide-header">
                    <div class="d-flex align-items-center">
                        <div class="modal-guide-icon">
                            <i class="fas fa-book-reader"></i>
                        </div>
                        <div>
                            <h5 class="modal-guide-title" id="titlePetunjukPengguna">Petunjuk Penggunaan WebGIS</h5>
                            <p class="modal-guide-sub">Panduan resmi eksplorasi peta, pencarian objek irigasi, dan interpretasi layer data spasial.</p>
                        </div>
                    </div>
                    <button type="button" class="modal-guide-close" data-dismiss="modal" aria-label="Close" title="Tutup Panduan">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Navigasi Tab Panduan --}}
                <div class="guide-nav-pills">
                    <button type="button" class="guide-pill-btn active" onclick="switchGuideTab('guide-navigasi', this)">
                        <i class="fas fa-hand-pointer"></i> <span>1. Navigasi Peta</span>
                    </button>
                    <button type="button" class="guide-pill-btn" onclick="switchGuideTab('guide-pencarian', this)">
                        <i class="fas fa-search"></i> <span>2. Pencarian Objek</span>
                    </button>
                    <button type="button" class="guide-pill-btn" onclick="switchGuideTab('guide-layer', this)">
                        <i class="fas fa-layer-group"></i> <span>3. Lapisan &amp; Warna</span>
                    </button>
                    <button type="button" class="guide-pill-btn" onclick="switchGuideTab('guide-popup', this)">
                        <i class="fas fa-info-circle"></i> <span>4. Detail Informasi</span>
                    </button>
                </div>

                {{-- Body Modal --}}
                <div class="modal-body p-4" style="background: #f8fafc; font-size: 13px;">

                    {{-- TAB 1: NAVIGASI PETA --}}
                    <div id="guide-navigasi" class="guide-tab-pane">
                        <div class="alert alert-primary d-flex align-items-center py-2 px-3 mb-3" style="border-radius: 10px; font-size: 12.5px;">
                            <i class="fas fa-lightbulb mr-2 text-warning" style="font-size: 16px;"></i>
                            <span>Peta WebGIS ini interaktif dan dapat diakses menggunakan mouse, touchpad, maupun layar sentuh (touchscreen).</span>
                        </div>

                        <div class="guide-card-step">
                            <div class="guide-step-num">1</div>
                            <div>
                                <div class="guide-step-title"><i class="fas fa-arrows-alt text-primary mr-1"></i> Menggeser Wilayah Peta (Pan)</div>
                                <p class="guide-step-desc">Klik dan tahan tombol kiri mouse, lalu geser ke segala arah untuk menjelajahi wilayah Kalimantan Barat. Di perangkat seluler, usap layar menggunakan satu jari.</p>
                            </div>
                        </div>

                        <div class="guide-card-step">
                            <div class="guide-step-num">2</div>
                            <div>
                                <div class="guide-step-title"><i class="fas fa-search-plus text-primary mr-1"></i> Memperbesar &amp; Memperkecil (Zoom)</div>
                                <p class="guide-step-desc">Gunakan roda scroll pada mouse, lakukan cubit layar (pinch) pada smartphone, atau klik tombol <b>[+]</b> dan <b>[-]</b> di pojok kiri atas peta untuk menyesuaikan tingkat kedalaman peta.</p>
                            </div>
                        </div>

                        <div class="guide-card-step">
                            <div class="guide-step-num">3</div>
                            <div>
                                <div class="guide-step-title"><i class="fas fa-layer-group text-primary mr-1"></i> Mengganti Tampilan Peta Dasar (Basemap)</div>
                                <p class="guide-step-desc">Arahkan kursor ke ikon layer di <b>pojok kanan atas peta</b> untuk memilih antara <b>Peta Jalan (OpenStreetMap)</b> atau <b>Citra Satelit (Google Hybrid)</b> sesuai kebutuhan analisis.</p>
                            </div>
                        </div>

                        <div class="guide-card-step">
                            <div class="guide-step-num">4</div>
                            <div>
                                <div class="guide-step-title"><i class="fas fa-redo-alt text-primary mr-1"></i> Mengembalikan Posisi Awal (Reset)</div>
                                <p class="guide-step-desc">Jika Anda ingin kembali ke titik fokus awal seluruh wilayah Kalimantan Barat, klik tombol <b>"Reset"</b> pada bar navigasi atas peta.</p>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 2: PENCARIAN OBJEK --}}
                    <div id="guide-pencarian" class="guide-tab-pane" style="display: none;">
                        <div class="alert alert-info d-flex align-items-center py-2 px-3 mb-3" style="border-radius: 10px; font-size: 12.5px;">
                            <i class="fas fa-search mr-2 text-primary" style="font-size: 16px;"></i>
                            <span>Fitur pencarian di sidebar kiri mempermudah menemukan lokasi Daerah Irigasi atau Pos secara instan.</span>
                        </div>

                        <div class="guide-card-step">
                            <div class="guide-step-num"><i class="fas fa-keyboard"></i></div>
                            <div>
                                <div class="guide-step-title">1. Ketik Kata Kunci di Sidebar Kiri</div>
                                <p class="guide-step-desc">Ketik nama pos, nama Daerah Irigasi (DIR), atau kata kunci (contoh: <i>"Kakap"</i>, <i>"KRW"</i>, <i>"Sungai"</i>) pada kolom <b>"Cari pos atau daerah irigasi"</b>.</p>
                            </div>
                        </div>

                        <div class="guide-card-step">
                            <div class="guide-step-num"><i class="fas fa-mouse-pointer"></i></div>
                            <div>
                                <div class="guide-step-title">2. Pilih Hasil dari Rekomendasi Cepat</div>
                                <p class="guide-step-desc">Daftar rekomendasi otomatis akan muncul secara real-time. Klik pada salah satu hasil pencarian yang Anda tuju.</p>
                            </div>
                        </div>

                        <div class="guide-card-step">
                            <div class="guide-step-num"><i class="fas fa-crosshairs"></i></div>
                            <div>
                                <div class="guide-step-title">3. Peta Otomatis Mengarahkan &amp; Menyorot Lokasi</div>
                                <p class="guide-step-desc">Kamera peta akan langsung terbang (<i>flyTo</i>) menuju lokasi objek tersebut, memperbesar tampilan, dan membuka jendela rincian informasi objek.</p>
                            </div>
                        </div>

                        <div class="guide-card-step">
                            <div class="guide-step-num"><i class="fas fa-eye-slash"></i></div>
                            <div>
                                <div class="guide-step-title">4. Sembunyikan Sidebar untuk Tampilan Penuh</div>
                                <p class="guide-step-desc">Klik tombol tanda panah <b>[&lt;]</b> di samping judul sidebar untuk menyembunyikan sidebar agar bidang pandang peta menjadi luas dan maksimal.</p>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 3: LAPISAN & WARNA (LAYER) --}}
                    <div id="guide-layer" class="guide-tab-pane" style="display: none;">
                        <p class="text-muted mb-3">Setiap warna dan garis pada peta mewakili klasifikasi infrastruktur sumber daya air berdasarkan data spasial ArcMap BWS Kalimantan I:</p>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="bg-white p-3 rounded shadow-sm border h-100">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="guide-swatch-box mr-2" style="background:#27ae60; border:1px solid #1b7a43;"></span>
                                        <b class="text-dark">Daerah Irigasi Baku (Hijau)</b>
                                    </div>
                                    <p class="text-muted small mb-0">Luas potensi areal pertanian yang secara teknis dapat dialiri oleh sistem jaringan irigasi yang tersedia.</p>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="bg-white p-3 rounded shadow-sm border h-100">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="guide-swatch-box mr-2" style="background:#2980b9; border:1px solid #1a5276;"></span>
                                        <b class="text-dark">Daerah Irigasi Fungsional (Biru)</b>
                                    </div>
                                    <p class="text-muted small mb-0">Luas areal pertanian yang saat ini telah benar-benar terairi dan ditanami secara aktif oleh para petani.</p>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="bg-white p-3 rounded shadow-sm border h-100">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="guide-swatch-box mr-2" style="background:#e67e22; border:1px solid #b95c00;"></span>
                                        <b class="text-dark">Daerah Irigasi Potensial (Oranye)</b>
                                    </div>
                                    <p class="text-muted small mb-0">Areal lahan yang memiliki potensi kelayakan untuk dikembangkan menjadi daerah irigasi produktif baru di masa depan.</p>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="bg-white p-3 rounded shadow-sm border h-100">
                                    <div class="d-flex align-items-center mb-2">
                                        <span style="display:inline-block; width:16px; height:4px; background:#0984e3; border-radius:2px; margin-right:8px;"></span>
                                        <b class="text-dark">Jaringan Saluran Air (Garis Biru)</b>
                                    </div>
                                    <p class="text-muted small mb-0">Trase jaringan saluran pembawa primer, sekunder, suplesi, dan saluran pembuang air irigasi.</p>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="bg-white p-3 rounded shadow-sm border">
                                    <div class="d-flex align-items-center mb-2">
                                        <span style="display:inline-block; width:12px; height:12px; background:#d63031; border-radius:50%; margin-right:8px;"></span>
                                        <b class="text-dark">Bangunan Air / Bangunan Pelengkap (Titik Merah)</b>
                                    </div>
                                    <p class="text-muted small mb-0">Titik lokasi infrastruktur fisik seperti bendung gerak/tetap, pintu bagi, pintu sadap, gorong-gorong, talang, siphon, dan bangunan pengukur debit.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 4: DETAIL INFORMASI POPUP --}}
                    <div id="guide-popup" class="guide-tab-pane" style="display: none;">
                        <div class="bg-white p-3 rounded shadow-sm border mb-3">
                            <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-mouse-pointer text-primary mr-1"></i> Klik Objek untuk Melihat Data Rinci</h6>
                            <p class="text-muted small mb-3">Saat Anda mengklik poligon daerah irigasi atau titik bangunan air pada peta, kartu informasi interaktif akan terbuka dengan atribut data resmi:</p>

                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <div class="p-2 border rounded bg-light">
                                        <b class="text-primary d-block small">Nama &amp; Kewenangan DIR</b>
                                        <span class="text-muted" style="font-size:11.5px;">Menampilkan nama resmi daerah irigasi serta kewenangan pengelolaan (Pusat / BWS, Provinsi, atau Kabupaten).</span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="p-2 border rounded bg-light">
                                        <b class="text-primary d-block small">Luasan Baku &amp; Fungsional (Ha)</b>
                                        <span class="text-muted" style="font-size:11.5px;">Angka luasan terukur hasil digitasi pemetaan spasial geospasial ArcMap.</span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="p-2 border rounded bg-light">
                                        <b class="text-primary d-block small">Debit Rencana &amp; Realisasi</b>
                                        <span class="text-muted" style="font-size:11.5px;">Kapasitas debit air yang direncanakan dan dialirkan melalui sistem jaringan.</span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="p-2 border rounded bg-light">
                                        <b class="text-primary d-block small">Kelembagaan P3A / GP3A</b>
                                        <span class="text-muted" style="font-size:11.5px;">Informasi jumlah dan status kelembagaan Perkumpulan Petani Pemakai Air setempat.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Footer Modal --}}
                <div class="modal-footer bg-white border-top py-2 px-4 d-flex justify-content-between align-items-center">
                    <small class="text-muted"><i class="fas fa-shield-alt text-success mr-1"></i> Standar Operasional Prosedur WebGIS BWSK I</small>
                    <button type="button" class="btn btn-primary px-4 font-weight-bold" data-dismiss="modal" style="border-radius: 8px; font-size: 13px;">
                        <i class="fas fa-check mr-1"></i> Mengerti &amp; Tutup
                    </button>
                </div>

            </div>
        </div>
    </div>


    {{-- ============================================================
         MODAL 2: TENTANG LAYANAN (PROFIL & INFORMASI BWS KALIMANTAN I)
         ============================================================ --}}
    <div class="modal fade" id="modalTentangLayanan" tabindex="-1" role="dialog" aria-labelledby="titleTentangLayanan" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden; box-shadow: 0 20px 50px rgba(15,23,42,0.3);">
                
                {{-- Header Modal --}}
                <div class="modal-guide-header header-info">
                    <div class="d-flex align-items-center">
                        <div class="modal-guide-icon" style="color: #38bdf8;">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div>
                            <h5 class="modal-guide-title" id="titleTentangLayanan">Tentang Layanan WebGIS BWS Kalimantan I</h5>
                            <p class="modal-guide-sub">Sistem Informasi Geospasial Infrastruktur Sumber Daya Air Provinsi Kalimantan Barat.</p>
                        </div>
                    </div>
                    <button type="button" class="modal-guide-close" data-dismiss="modal" aria-label="Close" title="Tutup Layanan">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Body Modal --}}
                <div class="modal-body p-4" style="background: #f8fafc; font-size: 13px;">

                    {{-- Profil Lembaga --}}
                    <div class="bg-white p-3 rounded shadow-sm border mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <img src="{{ asset('sbadmin/img/logo-pu.jpeg') }}" alt="Logo Kementerian PU" style="width: 44px; height: 44px; border-radius: 8px; margin-right: 14px; object-fit: contain;">
                            <div>
                                <h6 class="font-weight-bold text-dark mb-0" style="font-size: 15px;">Balai Wilayah Sungai Kalimantan I Pontianak</h6>
                                <span class="text-muted small">Direktorat Jenderal Sumber Daya Air &bull; Kementerian Pekerjaan Umum</span>
                            </div>
                        </div>
                        <p class="text-secondary mb-0" style="line-height: 1.6; font-size: 12.5px;">
                            WebGIS BWS Kalimantan I adalah platform digital spasial resmi yang menyajikan data pemetaan jaringan irigasi, saluran pembawa/pembuang, dan bangunan air di wilayah Provinsi Kalimantan Barat. Platform ini dibangun untuk mendukung prinsip keterbukaan informasi publik, akurasi perencanaan teknis, dan efektivitas operasi pemeliharaan sumber daya air.
                        </p>
                    </div>

                    {{-- 4 Pilar Layanan --}}
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <div class="service-feature-card">
                                <div class="service-icon-wrap">
                                    <i class="fas fa-map-marked-alt"></i>
                                </div>
                                <div class="service-title">1. Inventarisasi Spasial Akurat</div>
                                <p class="service-desc">Pemetaan batas Daerah Irigasi (DIR), luasan potensial baku, dan trace saluran irigasi yang terintegrasi dengan koordinat spasial global.</p>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="service-feature-card">
                                <div class="service-icon-wrap" style="background: #ecfdf5; color: #059669;">
                                    <i class="fas fa-water"></i>
                                </div>
                                <div class="service-title">2. Pemantauan Bangunan Air</div>
                                <p class="service-desc">Katalog terpadu bangunan bagi, sadap, bendung, pelimpah, dan bangunan pelengkap guna menunjang kesiapan suplai air irigasi pertanian.</p>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="service-feature-card">
                                <div class="service-icon-wrap" style="background: #fef3c7; color: #d97706;">
                                    <i class="fas fa-users-cog"></i>
                                </div>
                                <div class="service-title">3. Pembinaan Kelembagaan P3A</div>
                                <p class="service-desc">Penyajian informasi status keaktifan Perkumpulan Petani Pemakai Air (P3A/GP3A/IP3A) sebagai mitra operasional di lapangan.</p>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="service-feature-card">
                                <div class="service-icon-wrap" style="background: #f3e8ff; color: #7e22ce;">
                                    <i class="fas fa-globe-asia"></i>
                                </div>
                                <div class="service-title">4. Kebijakan Satu Peta (One Map)</div>
                                <p class="service-desc">Standardisasi format data geospasial (SHP/GeoJSON) sesuai standar Badan Informasi Geospasial (BIG) dan Kementerian PUPR.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Dasar Hukum & Standar --}}
                    <div class="bg-white p-3 rounded shadow-sm border mb-3">
                        <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-balance-scale text-primary mr-1"></i> Dasar Hukum &amp; Ketentuan Teknis</h6>
                        <ul class="text-muted small pl-3 mb-0" style="line-height: 1.6;">
                            <li>Undang-Undang Republik Indonesia Nomor 17 Tahun 2019 tentang Sumber Daya Air.</li>
                            <li>Peraturan Pemerintah No. 20 Tahun 2006 tentang Irigasi.</li>
                            <li>Peraturan Menteri PUPR No. 14/PRT/M/2015 tentang Kriteria dan Penetapan Status Daerah Irigasi.</li>
                            <li>Peraturan Presiden No. 23 Tahun 2021 tentang Perubahan atas Perpres No. 9 Tahun 2016 tentang Kebijakan Satu Peta.</li>
                        </ul>
                    </div>

                    {{-- Kontak & Layanan Informasi --}}
                    <div class="bg-white p-3 rounded shadow-sm border">
                        <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-building text-primary mr-1"></i> Kantor &amp; Layanan Pengaduan Resmi</h6>
                        <div class="row small text-muted">
                            <div class="col-md-6 mb-2">
                                <i class="fas fa-map-marker-alt text-danger mr-1"></i> <b>Alamat:</b> Jl. Kantor No. 1, Kota Pontianak, Kalimantan Barat
                            </div>
                            <div class="col-md-6 mb-2">
                                <i class="fas fa-envelope text-primary mr-1"></i> <b>Email:</b> bws-kalimantan1@pu.go.id
                            </div>
                            <div class="col-md-6">
                                <i class="fas fa-globe text-info mr-1"></i> <b>Website:</b> sda.pu.go.id / bws-kalimantan1
                            </div>
                            <div class="col-md-6">
                                <i class="fas fa-clock text-warning mr-1"></i> <b>Jam Operasional:</b> Senin - Jumat (08.00 - 16.00 WIB)
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Footer Modal --}}
                <div class="modal-footer bg-white border-top py-2 px-4 d-flex justify-content-between align-items-center">
                    <small class="text-muted">&copy; 2026 Kementerian Pekerjaan Umum &bull; Ditjen Sumber Daya Air</small>
                    <button type="button" class="btn btn-secondary px-4 font-weight-bold" data-dismiss="modal" style="border-radius: 8px; font-size: 13px;">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    </div>


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

