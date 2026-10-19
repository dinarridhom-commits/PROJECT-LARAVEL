{{--
    _top_nav.blade.php
    Header & Navigasi Horizontal Terpadu FEWS BWS KALIMANTAN I
    Sesuai standar portal Kementerian Pekerjaan Umum & Direktorat Jenderal SDA
--}}

<header class="bws-navbar">
    <div class="bws-navbar-inner">
        <div class="bws-nav-left">
            {{-- Tombol Reset / Home Tampilan Peta (tampil jika di halaman peta) --}}
            @if(request()->is('peta') || request()->is('/') || request()->is('home') || request()->is('dashboard'))
            <button type="button" class="bws-back-btn" onclick="if(window.resetMapView) resetMapView();" title="Reset Tampilan Peta ke Awal">
                <i class="fas fa-chevron-left"></i>
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
                    <div class="bws-brand-title">FEWS BWS KALIMANTAN I</div>
                    <div class="bws-brand-subtitle">Direktorat Jenderal Sumber Daya Air</div>
                    <div class="bws-brand-dept">Kementerian Pekerjaan Umum</div>
                </div>
            </a>
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

            @auth
            {{-- Dropdown Profil Pengguna (Logged In) --}}
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
                    
                    @if(in_array(Auth::user()->role, ['superadmin', 'admin']))
                    {{-- Menu Admin Panel Sesuai Gambar --}}
                    <a class="dropdown-item py-2 d-flex align-items-center text-dark font-weight-medium"
                       href="{{ route('admin.panel') }}">
                        <i class="fas fa-cog text-muted mr-2.5" style="font-size: 15px; width: 20px;"></i>
                        <span>Admin Panel</span>
                    </a>
                    <div class="dropdown-divider my-1"></div>
                    @endif

                    <a class="dropdown-item py-2 d-flex align-items-center text-danger font-weight-medium"
                       href="#" data-toggle="modal" data-target="#logoutModal">
                        <i class="fas fa-sign-out-alt mr-2.5" style="font-size: 14px; width: 20px;"></i>
                        <span>Keluar / Logout</span>
                    </a>
                </div>
            </div>
            @else
            {{-- Mode Publik / Tamu (Belum Login) --}}
            <div class="d-flex align-items-center">
                <span class="badge badge-light border text-muted px-2 py-1 mr-2 d-none d-sm-inline" style="font-size: 11px;">
                    <i class="fas fa-globe mr-1 text-success"></i> Mode Publik
                </span>
                <a href="{{ route('login') }}" class="btn btn-sm btn-warning text-dark font-weight-bold px-3 shadow-sm" style="font-size: 12px; border-radius: 6px;">
                    <i class="fas fa-sign-in-alt mr-1"></i> Login Staf
                </a>
            </div>
            @endauth
        </div>
    </div>

    {{-- Garis Aksen Emas PU --}}
    <div class="bws-accent-strip"></div>
</header>


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
                        <span>
                            <i class="fas fa-layer-group text-primary mr-2"></i>
                            Infrastruktur
                        </span>
                        <i class="fas fa-chevron-right text-muted small ml-3"></i>
                    </a>

                    {{-- Submenu Flyout Infrastruktur (Mengikuti Data Website) --}}
                    <div class="bws-flyout-menu shadow">
                        <a href="{{ route('peta', ['layer' => 'baku']) }}" class="bws-dropdown-item {{ request('layer') == 'baku' ? 'font-weight-bold text-primary' : '' }}">
                            <span class="bws-flyout-badge" style="background:#27ae60;"></span>
                            <span>Daerah Irigasi Baku</span>
                        </a>
                        <a href="{{ route('peta', ['layer' => 'fungsional']) }}" class="bws-dropdown-item {{ request('layer') == 'fungsional' ? 'font-weight-bold text-primary' : '' }}">
                            <span class="bws-flyout-badge" style="background:#2980b9;"></span>
                            <span>Daerah Irigasi Fungsional</span>
                        </a>
                        <a href="{{ route('peta', ['layer' => 'potensial']) }}" class="bws-dropdown-item {{ request('layer') == 'potensial' ? 'font-weight-bold text-primary' : '' }}">
                            <span class="bws-flyout-badge" style="background:#e67e22;"></span>
                            <span>Daerah Irigasi Potensial</span>
                        </a>
                        <div class="dropdown-divider my-1"></div>
                        <a href="{{ route('peta', ['layer' => 'saluran']) }}" class="bws-dropdown-item {{ request('layer') == 'saluran' ? 'font-weight-bold text-primary' : '' }}">
                            <span class="bws-flyout-badge" style="background:#0984e3;"></span>
                            <span>Jaringan Saluran Irigasi</span>
                        </a>
                        <a href="{{ route('peta', ['layer' => 'bangunan']) }}" class="bws-dropdown-item {{ request('layer') == 'bangunan' ? 'font-weight-bold text-primary' : '' }}">
                            <span class="bws-flyout-badge" style="background:#d63031;"></span>
                            <span>Bangunan Air / Pelengkap</span>
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
    document.addEventListener('DOMContentLoaded', function() {
        var dropdown = document.querySelector('.bws-subnav-dropdown');
        var submenu = document.querySelector('.bws-dropdown-submenu');

        if (dropdown) {
            var btn = dropdown.querySelector('.bws-subnav-link');
            if (btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropdown.classList.toggle('show');
                });
            }
        }

        if (submenu) {
            var subLink = submenu.querySelector('a.bws-dropdown-item');
            if (subLink) {
                subLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    submenu.classList.toggle('show');
                });
            }
        }

        document.addEventListener('click', function(e) {
            if (dropdown && !dropdown.contains(e.target)) {
                dropdown.classList.remove('show');
                if (submenu) submenu.classList.remove('show');
            }
        });
    });
</script>
@endif
