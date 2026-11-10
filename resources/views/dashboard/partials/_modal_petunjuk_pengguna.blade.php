{{-- ============================================================
     MODAL 1: PETUNJUK PENGGUNA (PANDUAN INTERAKTIF WEBGIS)
     File  : resources/views/dashboard/partials/_modal_petunjuk_pengguna.blade.php
     Dipanggil dari : peta-publik.blade.php via @include(...)
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

