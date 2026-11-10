{{-- ============================================================
     MODAL 2: TENTANG LAYANAN (PROFIL & INFORMASI BWS KALIMANTAN I)
     File  : resources/views/dashboard/partials/_modal_tentang_layanan.blade.php
     Dipanggil dari : peta-publik.blade.php via @include(...)
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
                        <img src="{{ asset('sbadmin/img/logo-pu.jpeg') }}" alt="Logo Kementerian PU"
                             style="width: 44px; height: 44px; border-radius: 8px; margin-right: 14px; object-fit: contain;">
                        <div>
                            <h6 class="font-weight-bold text-dark mb-0" style="font-size: 15px;">Balai Wilayah Sungai Kalimantan I Pontianak</h6>
                            <span class="text-muted small">Direktorat Jenderal Sumber Daya Air &bull; Kementerian Pekerjaan Umum</span>
                        </div>
                    </div>
                    <p class="text-secondary mb-0" style="line-height: 1.6; font-size: 12.5px;">
                        WebGIS BWS Kalimantan I adalah platform digital spasial resmi yang menyajikan data pemetaan jaringan irigasi,
                        saluran pembawa/pembuang, dan bangunan air di wilayah Provinsi Kalimantan Barat. Platform ini dibangun untuk
                        mendukung prinsip keterbukaan informasi publik, akurasi perencanaan teknis, dan efektivitas operasi pemeliharaan
                        sumber daya air.
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

