{{--
    _top_nav.blade.php
    Header & Navigasi Horizontal Terpadu FEWS BWS KALIMANTAN I
    Sesuai standar portal Kementerian Pekerjaan Umum & Direktorat Jenderal SDA
--}}

<header class="bws-navbar">
    <div class="bws-navbar-inner">
        <div class="bws-nav-left">
            {{-- Tombol Kembali / Navigasi --}}
            @if(request()->is('peta') || request()->is('/') || request()->is('home') || request()->is('dashboard'))
            <button type="button" class="bws-back-btn" onclick="bwsGoBack()" title="Kembali">
                <i class="fas fa-arrow-left"></i>
            </button>
            @else
            <a href="{{ route('peta') }}" class="bws-back-btn" title="Kembali ke Peta">
                <i class="fas fa-arrow-left"></i>
            </a>
            @endif

            {{-- Logo PU & Teks Instansi --}}
            <a href="{{ route('peta') }}" class="bws-brand-wrapper text-decoration-none">
                <img src="{{ asset('sbadmin/img/logo-pu.jpeg') }}" alt="Logo Kementerian PU" class="bws-logo-pu">
                <div class="bws-brand-text">
                    <div class="bws-brand-title">WEBGIS BWS KALIMANTAN I</div>
                    <div class="bws-brand-subtitle">Direktorat Jenderal Sumber Daya Air</div>
                    <div class="bws-brand-dept">Kementerian Pekerjaan Umum</div>
                </div>
            </a>
        </div>

        <div class="bws-nav-right">
            @php $isMapPage = request()->is('peta') || request()->is('/') || request()->is('home') || request()->is('dashboard'); @endphp

            {{-- Tombol Fullscreen — sembunyikan di halaman peta (sudah ada di gambar referensi) --}}
            @if(!$isMapPage)
            <button type="button" class="bws-header-btn" id="btnFullscreenToggle" title="Tampilan Layar Penuh">
                <i class="fas fa-expand"></i>
            </button>
            @endif

            @auth
            {{-- Dropdown Profil Pengguna (Logged In) — hanya di halaman non-peta --}}
            @if(!$isMapPage)
            <div class="dropdown">
                <div class="bws-profile-pill" id="userMenuDropdown"
                     data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="bws-profile-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <span class="font-weight-bold">{{ Auth::user()->name }}</span>
                    <i class="fas fa-chevron-down ml-1 small text-white-50"></i>
                </div>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in py-2"
                     aria-labelledby="userMenuDropdown" style="min-width: 180px; border-radius: 8px;">

                    <a class="dropdown-item py-2 d-flex align-items-center text-dark font-weight-medium"
                       href="#" data-toggle="modal" data-target="#modalEditProfile">
                        <i class="fas fa-user-edit text-primary mr-2.5" style="font-size: 14px; width: 20px;"></i>
                        <span>Edit Profil</span>
                    </a>

                    @if(in_array(Auth::user()->role, ['superadmin', 'admin']))
                    <a class="dropdown-item py-2 d-flex align-items-center text-dark font-weight-medium"
                       href="{{ route('admin.panel') }}">
                        <i class="fas fa-cog text-muted mr-2.5" style="font-size: 15px; width: 20px;"></i>
                        <span>Admin Panel</span>
                    </a>
                    @endif
                    <div class="dropdown-divider my-1"></div>

                    <a class="dropdown-item py-2 d-flex align-items-center text-danger font-weight-medium"
                       href="#" data-toggle="modal" data-target="#logoutModal">
                        <i class="fas fa-sign-out-alt mr-2.5" style="font-size: 14px; width: 20px;"></i>
                        <span>Keluar / Logout</span>
                    </a>
                </div>
            </div>
            @endif
            @else
            {{-- Mode Publik / Tamu (Belum Login) — tombol Login hanya di halaman non-peta --}}
            @if(!$isMapPage)
            <div class="d-flex align-items-center">
                <a href="{{ route('login') }}" class="btn btn-sm btn-warning text-dark font-weight-bold px-3 shadow-sm" style="font-size: 12px; border-radius: 6px;">
                    <i class="fas fa-sign-in-alt mr-1"></i> Login Staf
                </a>
            </div>
            @endif
            @endauth
        </div>
    </div>

    {{-- Garis Aksen Emas PU --}}
    <div class="bws-accent-strip"></div>
</header>

{{-- Handler Navigasi Tombol Kembali --}}
<script>
    function bwsGoBack() {
        // 1. Cek apakah ada halaman sebelumnya dari domain yang sama
        if (document.referrer && document.referrer !== window.location.href) {
            try {
                var refUrl = new URL(document.referrer);
                if (refUrl.origin === window.location.origin) {
                    if (window.history.length > 1) {
                        window.history.back();
                        return;
                    }
                    window.location.href = document.referrer;
                    return;
                }
            } catch(e) {}
        }

        // 2. Jika ada riwayat penjelajahan di sesi browser saat ini
        if (window.history.length > 1) {
            window.history.back();
            return;
        }

        // 3. Fallback cerdas sesuai status dan konteks halaman:
        @if(request()->has('layer'))
            {{-- Jika sedang memfilter layer spesifik (?layer=...), kembali ke peta utama --}}
            window.location.href = "{{ route('peta') }}";
        @else
            @auth
                @if(in_array(Auth::user()->role, ['superadmin', 'admin']))
                    window.location.href = "{{ route('users.index') }}";
                @else
                    if (window.resetMapView) {
                        resetMapView();
                    } else {
                        window.location.href = "{{ route('peta') }}";
                    }
                @endif
            @else
                if (window.resetMapView) {
                    resetMapView();
                } else {
                    window.location.href = "{{ route('peta') }}";
                }
            @endauth
        @endif
    }
</script>


{{-- ============================================================
     SUB-NAVBAR HORIZONTAL PUTIH
     HANYA DITAMPILKAN DI DALAM ADMIN PANEL (users, activity logs, dll),
     TIDAK DITAMPILKAN DI HALAMAN PETA AGAR TIDAK MENGGANGGU TAMPILAN PETA.
     ============================================================ --}}
@php
    $isMapPage = request()->is('peta') || request()->is('/') || request()->is('home') || request()->is('dashboard');
@endphp

@if(!$isMapPage && empty($hideSubnav))
<nav class="bws-subnav">
    <div class="bws-subnav-inner">

        {{-- 1. DASHBOARD / PETA --}}
        <a href="{{ route('peta') }}"
           class="bws-subnav-link {{ (request()->is('peta') || request()->is('/') || request()->is('home') || request()->is('dashboard')) && !request()->has('layer') ? 'active' : '' }}">
            <i class="fas fa-th-large text-primary mr-2"></i>
            <span>Dashboard</span>
        </a>

        {{-- 2. MASTER DATA (DROPDOWN) --}}
        <div class="bws-subnav-dropdown">
            <button type="button" class="bws-subnav-link {{ request()->has('layer') ? 'active' : '' }}">
                <i class="fas fa-database text-secondary mr-2"></i>
                <span>Master Data</span>
                <i class="fas fa-chevron-down ml-1 text-muted" style="font-size: 10px;"></i>
            </button>

            <div class="bws-dropdown-menu shadow">
                {{-- Hanya Infrastruktur sesuai permintaan user --}}
                <div class="bws-dropdown-submenu">
                    <a href="#" class="bws-dropdown-item" onclick="return false;">
                        <span class="d-flex align-items-center">
                            <i class="fas fa-layer-group text-primary mr-2"></i>
                            <span>Infrastruktur</span>
                        </span>
                        <i class="fas fa-chevron-right text-muted small ml-3"></i>
                    </a>

                    {{-- Submenu Flyout Infrastruktur (Mengikuti 54 Data GeoJSON Website) --}}
                    <div class="bws-flyout-menu shadow">
                        <a href="{{ route('peta', ['layer' => 'baku']) }}" class="bws-dropdown-item {{ request('layer') == 'baku' ? 'font-weight-bold text-primary' : '' }}">
                            <span class="d-flex align-items-center">
                                <span class="bws-flyout-badge mr-2" style="background:#27ae60;"></span>
                                <span>Daerah Irigasi Baku</span>
                            </span>
                            <span class="badge badge-light border text-muted px-1.5 py-0.5 ml-2" style="font-size: 10px;">9 Data</span>
                        </a>
                        <a href="{{ route('peta', ['layer' => 'fungsional']) }}" class="bws-dropdown-item {{ request('layer') == 'fungsional' ? 'font-weight-bold text-primary' : '' }}">
                            <span class="d-flex align-items-center">
                                <span class="bws-flyout-badge mr-2" style="background:#2980b9;"></span>
                                <span>Daerah Irigasi Fungsional</span>
                            </span>
                            <span class="badge badge-light border text-muted px-1.5 py-0.5 ml-2" style="font-size: 10px;">6 Data</span>
                        </a>
                        <a href="{{ route('peta', ['layer' => 'potensial']) }}" class="bws-dropdown-item {{ request('layer') == 'potensial' ? 'font-weight-bold text-primary' : '' }}">
                            <span class="d-flex align-items-center">
                                <span class="bws-flyout-badge mr-2" style="background:#e67e22;"></span>
                                <span>Daerah Irigasi Potensial</span>
                            </span>
                            <span class="badge badge-light border text-muted px-1.5 py-0.5 ml-2" style="font-size: 10px;">10 Data</span>
                        </a>
                        <div class="dropdown-divider my-1"></div>
                        <a href="{{ route('peta', ['layer' => 'saluran']) }}" class="bws-dropdown-item {{ request('layer') == 'saluran' ? 'font-weight-bold text-primary' : '' }}">
                            <span class="d-flex align-items-center">
                                <span class="bws-flyout-badge mr-2" style="background:#0984e3;"></span>
                                <span>Jaringan Saluran Irigasi</span>
                            </span>
                            <span class="badge badge-light border text-muted px-1.5 py-0.5 ml-2" style="font-size: 10px;">15 Data</span>
                        </a>
                        <a href="{{ route('peta', ['layer' => 'bangunan']) }}" class="bws-dropdown-item {{ request('layer') == 'bangunan' ? 'font-weight-bold text-primary' : '' }}">
                            <span class="d-flex align-items-center">
                                <span class="bws-flyout-badge mr-2" style="background:#d63031;"></span>
                                <span>Bangunan Air / Pelengkap</span>
                            </span>
                            <span class="badge badge-light border text-muted px-1.5 py-0.5 ml-2" style="font-size: 10px;">14 Data</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. MANAJEMEN USER (Super Admin & Admin) --}}
        @if(Auth::check() && in_array(Auth::user()->role, ['superadmin', 'admin']))
        <a href="{{ route('users.index') }}"
           class="bws-subnav-link {{ request()->is('users*') ? 'active' : '' }}">
            <i class="fas fa-users-cog text-info mr-2"></i>
            <span>Manajemen User</span>
        </a>
        @endif

        {{-- 4. LOG AKTIVITAS (Super Admin) --}}
        @if(Auth::check() && Auth::user()->role === 'superadmin')
        <a href="{{ route('activity-logs.index') }}"
           class="bws-subnav-link {{ request()->is('activity-logs*') ? 'active' : '' }}">
            <i class="fas fa-history text-danger mr-2"></i>
            <span>Log Aktivitas</span>
        </a>
        @endif

    </div>
</nav>

<script>
    (function() {
        function initSubnavDropdowns() {
            var dropdown = document.querySelector('.bws-subnav-dropdown');
            var submenu = document.querySelector('.bws-dropdown-submenu');

            if (dropdown) {
                var btn = dropdown.querySelector('.bws-subnav-link');
                var menu = dropdown.querySelector('.bws-dropdown-menu');

                if (btn && menu) {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        var isOpen = dropdown.classList.contains('show');
                        if (isOpen) {
                            dropdown.classList.remove('show');
                            menu.classList.remove('show');
                            if (submenu) {
                                submenu.classList.remove('show');
                                var f = submenu.querySelector('.bws-flyout-menu');
                                if (f) f.classList.remove('show');
                            }
                        } else {
                            dropdown.classList.add('show');
                            menu.classList.add('show');
                        }
                    });
                }
            }

            if (submenu) {
                var subLink = submenu.querySelector('a.bws-dropdown-item');
                var flyout = submenu.querySelector('.bws-flyout-menu');

                if (subLink && flyout) {
                    subLink.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        submenu.classList.toggle('show');
                        flyout.classList.toggle('show');
                    });
                }
            }

            document.addEventListener('click', function(e) {
                if (dropdown && !dropdown.contains(e.target)) {
                    dropdown.classList.remove('show');
                    var m = dropdown.querySelector('.bws-dropdown-menu');
                    if (m) m.classList.remove('show');
                    if (submenu) {
                        submenu.classList.remove('show');
                        var f = submenu.querySelector('.bws-flyout-menu');
                        if (f) f.classList.remove('show');
                    }
                }
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initSubnavDropdowns);
        } else {
            initSubnavDropdowns();
        }
    })();
</script>
@endif
