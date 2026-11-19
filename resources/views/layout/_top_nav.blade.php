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
            @php $isMapPage = request()->is('peta') || request()->is('/') || request()->is('home'); @endphp

            @auth
            {{-- Lonceng Notifikasi Sistem Modern --}}
            <div class="dropdown d-inline-block">
                <button type="button" class="bws-header-btn position-relative" id="notifBellDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Pemberitahuan &amp; Notifikasi Sistem" onclick="loadNotifications()">
                    <i class="fas fa-bell"></i>
                    <span class="bws-badge-pill" id="notifCountBadge" style="display:none;">0</span>
                </button>
                <div class="dropdown-menu dropdown-menu-right bws-notif-menu" aria-labelledby="notifBellDropdown">
                    <div class="bws-notif-header">
                        <div class="bws-notif-header-title">
                            <i class="fas fa-bell text-primary"></i>
                            <span>Notifikasi</span>
                            <span class="bws-notif-header-badge" id="notifHeaderBadge" style="display:none;">0 Baru</span>
                        </div>
                        <button type="button" class="bws-notif-btn-read" onclick="markAllNotificationsRead()" title="Tandai semua telah dibaca">
                            <i class="fas fa-check-double"></i>
                            <span>Tandai Dibaca</span>
                        </button>
                    </div>

                    <div id="notifListContainer" class="bws-notif-list">
                        <div class="text-center py-5 text-muted small">
                            <i class="fas fa-spinner fa-spin mr-1 fa-lg text-primary"></i>
                            <div class="mt-2">Memuat notifikasi...</div>
                        </div>
                    </div>

                    @if(Auth::user()->role === 'superadmin')
                    <div class="bws-notif-footer">
                        <a href="{{ route('gis.verification') }}" class="bws-notif-footer-link text-primary">
                            <i class="fas fa-tasks mr-1"></i> Antrean Verifikasi
                        </a>
                        <a href="{{ route('gis.trash') }}" class="bws-notif-footer-link text-muted">
                            <i class="fas fa-archive mr-1"></i> Arsip Terhapus
                        </a>
                    </div>
                    @else
                    <div class="bws-notif-footer justify-content-center">
                        <span class="text-muted" style="font-size: 11px;">
                            <i class="fas fa-shield-alt mr-1 text-primary"></i> Notifikasi Terpusat WebGIS BWS Kalimantan I
                        </span>
                    </div>
                    @endif
                </div>
            </div>
            @endauth

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
    $isMapPage = request()->is('peta') || request()->is('/') || request()->is('home');
@endphp

@if(!$isMapPage && empty($hideSubnav))
<nav class="bws-subnav">
    <div class="bws-subnav-inner">

        {{-- 1. DASHBOARD ANALYTICS --}}
        <a href="{{ route('dashboard') }}"
           class="bws-subnav-link {{ request()->is('dashboard*') ? 'active' : '' }}">
            <i class="fas fa-chart-line text-primary mr-2"></i>
            <span>Dashboard</span>
        </a>

        {{-- 2. MASTER DATA (DROPDOWN) --}}
        <div class="bws-subnav-dropdown">
            <button type="button" class="bws-subnav-link {{ request()->is('master-data*') || request()->has('layer') ? 'active' : '' }}">
                <i class="fas fa-database text-secondary mr-2"></i>
                <span>Master Data</span>
                <i class="fas fa-chevron-down ml-1 text-muted" style="font-size: 10px;"></i>
            </button>

            <div class="bws-dropdown-menu shadow-lg" style="min-width: 290px; padding: 8px; border-radius: 12px;">
                @php
                    $navCatCounts = \App\Models\GisLayer::where('status', 'approved')
                        ->selectRaw('category, count(*) as total')
                        ->groupBy('category')
                        ->pluck('total', 'category');
                    $totalInfrastruktur = ($navCatCounts['saluran'] ?? 0) + ($navCatCounts['bangunan'] ?? 0);
                    $totalDaerahIrigasi = ($navCatCounts['baku'] ?? 0) + ($navCatCounts['fungsional'] ?? 0) + ($navCatCounts['potensial'] ?? 0);
                @endphp

                {{-- CARD 1 (ATAS): INFRASTRUKTUR / PRASARANA FISIK AIR --}}
                <div class="bws-dropdown-submenu">
                    <a href="{{ route('master-data.infrastruktur') }}"
                       class="bws-main-dropdown-card {{ in_array(request('category'), ['saluran', 'bangunan']) ? 'active' : '' }}">
                        <div class="d-flex align-items-center">
                            <div class="bws-main-card-icon bws-icon-cyan mr-2.5">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <div>
                                <div class="bws-main-card-title">Infrastruktur</div>
                                <div class="bws-main-card-sub">Prasarana &amp; bangunan air</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge badge-light border text-muted px-2 py-0.5 mr-2" style="font-size: 10.5px;">
                                {{ $totalInfrastruktur }} Data
                            </span>
                            <i class="fas fa-chevron-right text-muted small"></i>
                        </div>
                    </a>

                    {{-- Flyout Khusus Infrastruktur (Hanya 2 Berkas: Saluran & Bangunan Air) --}}
                    <div class="bws-flyout-menu shadow-lg">
                        <div class="bws-flyout-header-group">
                            <span class="bws-group-badge bws-badge-blue">Prasarana Fisik</span>
                            <span class="bws-group-title">Infrastruktur Air</span>
                        </div>

                        <a href="{{ route('master-data.infrastruktur', ['category' => 'saluran']) }}"
                           class="bws-flyout-card {{ request('category') == 'saluran' ? 'active' : '' }}">
                            <div class="bws-flyout-card-left">
                                <div class="bws-flyout-icon-box bws-icon-cyan">
                                    <i class="fas fa-stream"></i>
                                </div>
                                <div>
                                    <div class="bws-flyout-card-name">Jaringan Saluran Irigasi</div>
                                    <div class="bws-flyout-card-desc">Saluran primer, sekunder &amp; tersier</div>
                                </div>
                            </div>
                            <span class="bws-flyout-pill-count">
                                {{ $navCatCounts['saluran'] ?? 0 }} Berkas
                            </span>
                        </a>

                        <a href="{{ route('master-data.infrastruktur', ['category' => 'bangunan']) }}"
                           class="bws-flyout-card {{ request('category') == 'bangunan' ? 'active' : '' }}">
                            <div class="bws-flyout-card-left">
                                <div class="bws-flyout-icon-box bws-icon-red">
                                    <i class="fas fa-archway"></i>
                                </div>
                                <div>
                                    <div class="bws-flyout-card-name">Bangunan Air &amp; Pelengkap</div>
                                    <div class="bws-flyout-card-desc">Bendung, pintu air &amp; bagi sadap</div>
                                </div>
                            </div>
                            <span class="bws-flyout-pill-count">
                                {{ $navCatCounts['bangunan'] ?? 0 }} Berkas
                            </span>
                        </a>
                    </div>
                </div>

                {{-- CARD 2 (BAWAH): DAERAH IRIGASI / KAWASAN AREAL --}}
                <div class="bws-dropdown-submenu mt-1">
                    <a href="{{ route('master-data.infrastruktur') }}"
                       class="bws-main-dropdown-card {{ in_array(request('category'), ['baku', 'fungsional', 'potensial']) ? 'active' : '' }}">
                        <div class="d-flex align-items-center">
                            <div class="bws-main-card-icon bws-icon-green mr-2.5">
                                <i class="fas fa-map-marked-alt"></i>
                            </div>
                            <div>
                                <div class="bws-main-card-title">Daerah Irigasi</div>
                                <div class="bws-main-card-sub">Kawasan &amp; hamparan areal</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge badge-light border text-muted px-2 py-0.5 mr-2" style="font-size: 10.5px;">
                                {{ $totalDaerahIrigasi }} Data
                            </span>
                            <i class="fas fa-chevron-right text-muted small"></i>
                        </div>
                    </a>

                    {{-- Flyout Khusus Daerah Irigasi (Hanya 3 Berkas: Baku, Fungsional, Potensial) --}}
                    <div class="bws-flyout-menu shadow-lg">
                        <div class="bws-flyout-header-group">
                            <span class="bws-group-badge bws-badge-green">Kawasan / Areal</span>
                            <span class="bws-group-title">Daerah Irigasi</span>
                        </div>

                        <a href="{{ route('master-data.infrastruktur', ['category' => 'baku']) }}"
                           class="bws-flyout-card {{ request('category') == 'baku' ? 'active' : '' }}">
                            <div class="bws-flyout-card-left">
                                <div class="bws-flyout-icon-box bws-icon-green">
                                    <i class="fas fa-seedling"></i>
                                </div>
                                <div>
                                    <div class="bws-flyout-card-name">Daerah Irigasi Baku</div>
                                    <div class="bws-flyout-card-desc">Lahan beririgasi baku aktif</div>
                                </div>
                            </div>
                            <span class="bws-flyout-pill-count">
                                {{ $navCatCounts['baku'] ?? 0 }} Berkas
                            </span>
                        </a>

                        <a href="{{ route('master-data.infrastruktur', ['category' => 'fungsional']) }}"
                           class="bws-flyout-card {{ request('category') == 'fungsional' ? 'active' : '' }}">
                            <div class="bws-flyout-card-left">
                                <div class="bws-flyout-icon-box bws-icon-blue">
                                    <i class="fas fa-tint"></i>
                                </div>
                                <div>
                                    <div class="bws-flyout-card-name">Daerah Irigasi Fungsional</div>
                                    <div class="bws-flyout-card-desc">Lahan beririgasi teratur</div>
                                </div>
                            </div>
                            <span class="bws-flyout-pill-count">
                                {{ $navCatCounts['fungsional'] ?? 0 }} Berkas
                            </span>
                        </a>

                        <a href="{{ route('master-data.infrastruktur', ['category' => 'potensial']) }}"
                           class="bws-flyout-card {{ request('category') == 'potensial' ? 'active' : '' }}">
                            <div class="bws-flyout-card-left">
                                <div class="bws-flyout-icon-box bws-icon-amber">
                                    <i class="fas fa-chart-area"></i>
                                </div>
                                <div>
                                    <div class="bws-flyout-card-name">Daerah Irigasi Potensial</div>
                                    <div class="bws-flyout-card-desc">Kawasan potensi pengembangan</div>
                                </div>
                            </div>
                            <span class="bws-flyout-pill-count">
                                {{ $navCatCounts['potensial'] ?? 0 }} Berkas
                            </span>
                        </a>
                    </div>
                </div>

                {{-- SEKAT PEMBATAS & FOOTER LIHAT SEMUA DATA --}}
                <div class="bws-flyout-divider my-2"></div>

                <a href="{{ route('master-data.infrastruktur') }}" class="bws-flyout-footer-btn">
                    <div class="d-flex align-items-center">
                        <div class="bws-flyout-icon-box bws-icon-indigo mr-2.5">
                            <i class="fas fa-th-large"></i>
                        </div>
                        <div>
                            <div class="bws-flyout-footer-title">Buka Katalog Master Data</div>
                            <div class="bws-flyout-footer-sub">Kelola seluruh 54 berkas spasial</div>
                        </div>
                    </div>
                    <i class="fas fa-arrow-right text-primary small ml-2"></i>
                </a>
            </div>
        </div>

        {{-- 3. MANAJEMEN PENGGUNA (Super Admin & Admin) --}}
        @if(Auth::check() && in_array(Auth::user()->role, ['superadmin', 'admin']))
        <a href="{{ route('users.index') }}"
           class="bws-subnav-link {{ request()->is('users*') ? 'active' : '' }}">
            <i class="fas fa-users-cog text-info mr-2"></i>
            <span>Manajemen Pengguna</span>
        </a>
        @endif

        {{-- 4. VERIFIKASI FILE GIS (Khusus Super Admin) --}}
        @if(Auth::check() && Auth::user()->role === 'superadmin')
        @php
            $pendingGisCount = \App\Models\GisLayer::where('status', 'pending')->count();
        @endphp
        <a href="{{ route('gis.verification') }}"
           class="bws-subnav-link {{ request()->is('gis/verification*') ? 'active' : '' }}">
            <i class="fas fa-clipboard-check text-success mr-2"></i>
            <span>Verifikasi GIS</span>
            @if($pendingGisCount > 0)
                <span class="badge badge-warning text-dark font-weight-bold ml-1.5" style="font-size:10px; border-radius:10px; padding:2px 6px;">{{ $pendingGisCount }}</span>
            @endif
        </a>
        @endif

        {{-- 5. LOG AKTIVITAS (Super Admin) --}}
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

            var submenus = dropdown ? dropdown.querySelectorAll('.bws-dropdown-submenu') : [];
            submenus.forEach(function(sub) {
                var subLink = sub.querySelector('a.bws-main-dropdown-card') || sub.querySelector('a.bws-dropdown-item');
                var flyout = sub.querySelector('.bws-flyout-menu');

                if (subLink && flyout) {
                    subLink.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        submenus.forEach(function(other) {
                            if (other !== sub) {
                                other.classList.remove('show');
                                var of = other.querySelector('.bws-flyout-menu');
                                if (of) of.classList.remove('show');
                            }
                        });
                        sub.classList.toggle('show');
                        flyout.classList.toggle('show');
                    });
                }
            });

            document.addEventListener('click', function(e) {
                if (dropdown && !dropdown.contains(e.target)) {
                    dropdown.classList.remove('show');
                    var m = dropdown.querySelector('.bws-dropdown-menu');
                    if (m) m.classList.remove('show');
                    submenus.forEach(function(sub) {
                        sub.classList.remove('show');
                        var f = sub.querySelector('.bws-flyout-menu');
                        if (f) f.classList.remove('show');
                    });
                }
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initSubnavDropdowns);
        } else {
            initSubnavDropdowns();
        }
    })();

    // Script Notifikasi Sistem In-App Modern
    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function updateBadgeUI(count) {
        var badge = document.getElementById('notifCountBadge');
        var headerBadge = document.getElementById('notifHeaderBadge');
        if (badge) {
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'inline-flex';
            } else {
                badge.style.display = 'none';
            }
        }
        if (headerBadge) {
            if (count > 0) {
                headerBadge.textContent = count + ' Baru';
                headerBadge.style.display = 'inline-block';
            } else {
                headerBadge.style.display = 'none';
            }
        }
    }

    function loadNotifications() {
        var container = document.getElementById('notifListContainer');
        if (!container) return;

        fetch('{{ route("notifications.get") }}')
            .then(function(res) { return res.json(); })
            .then(function(data) {
                updateBadgeUI(data.unread || 0);

                if (!data.notifications || data.notifications.length === 0) {
                    container.innerHTML = 
                        '<div class="text-center py-5 px-3">' +
                            '<div style="width: 48px; height: 48px; border-radius: 50%; background: #f1f5f9; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 10px; color: #94a3b8; font-size: 20px;">' +
                                '<i class="fas fa-bell-slash"></i>' +
                            '</div>' +
                            '<div class="font-weight-bold text-dark" style="font-size: 13px;">Belum Ada Notifikasi</div>' +
                            '<div class="text-muted small mt-1" style="font-size: 11.5px; line-height: 1.4;">Semua pemberitahuan aktivitas berkas GIS akan tampil di sini.</div>' +
                        '</div>';
                    return;
                }

                var html = '';
                data.notifications.forEach(function(n) {
                    var isUnread = !n.is_read;
                    var itemClass = 'bws-notif-item' + (isUnread ? ' unread' : '');
                    
                    var iconClass = 'info';
                    var iconFa = 'fa-info-circle';
                    if (n.type === 'success') {
                        iconClass = 'success';
                        iconFa = 'fa-check-circle';
                    } else if (n.type === 'warning') {
                        iconClass = 'warning';
                        iconFa = 'fa-exclamation-triangle';
                    } else if (n.type === 'danger') {
                        iconClass = 'danger';
                        iconFa = 'fa-times-circle';
                    }

                    // Format message & parse "Catatan:" if present
                    var rawMsg = n.message || '';
                    var mainMsg = rawMsg;
                    var noteHtml = '';
                    var notePos = rawMsg.indexOf('Catatan:');
                    if (notePos !== -1) {
                        mainMsg = rawMsg.substring(0, notePos).trim();
                        var noteContent = rawMsg.substring(notePos).trim();
                        noteHtml = '<div class="bws-notif-note"><i class="fas fa-comment-dots mr-1"></i>' + escapeHtml(noteContent) + '</div>';
                    }

                    var timeDisplay = n.time_ago || n.time_formatted || '';

                    html += '<div class="' + itemClass + '" onclick="markSingleRead(' + n.id + ')" style="cursor: pointer;">' +
                        '<div class="bws-notif-icon ' + iconClass + '">' +
                            '<i class="fas ' + iconFa + '"></i>' +
                        '</div>' +
                        '<div class="bws-notif-content">' +
                            '<div class="bws-notif-top-row">' +
                                '<span class="bws-notif-title">' + escapeHtml(n.title) + '</span>' +
                                '<span class="bws-notif-time">' + escapeHtml(timeDisplay) + '</span>' +
                            '</div>' +
                            '<p class="bws-notif-desc">' + escapeHtml(mainMsg) + '</p>' +
                            noteHtml +
                            (n.link ? '<a href="' + n.link + '" class="bws-notif-action-btn" onclick="event.stopPropagation();"><i class="fas fa-arrow-right"></i> Buka Berkas</a>' : '') +
                        '</div>' +
                        (isUnread ? '<span class="bws-notif-unread-dot" title="Belum dibaca"></span>' : '') +
                    '</div>';
                });

                container.innerHTML = html;
            })
            .catch(function(err) {
                container.innerHTML = '<div class="text-center py-4 text-danger small"><i class="fas fa-exclamation-circle mr-1"></i> Gagal memuat notifikasi.</div>';
            });
    }

    function markSingleRead(id) {
        fetch('{{ url("notifications/read") }}/' + id, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(function() {
            loadNotifications();
        }).catch(function() {});
    }

    function markAllNotificationsRead() {
        fetch('{{ route("notifications.readAll") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(function() {
            updateBadgeUI(0);
            loadNotifications();
        });
    }

    // Auto-fetch unread count saat halaman pertama dimuat
    document.addEventListener('DOMContentLoaded', function() {
        fetch('{{ route("notifications.get") }}')
            .then(function(res) { return res.json(); })
            .then(function(data) {
                updateBadgeUI(data.unread || 0);
            }).catch(function() {});
    });
</script>

<style>
/* ================================================================
   BWS MAIN DROPDOWN CARDS (2 KARTU TERPISAH: INFRASTRUKTUR & DAERAH IRIGASI)
================================================================ */
.bws-main-dropdown-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 12px;
    border-radius: 10px;
    background: #ffffff;
    border: 1px solid #eef2f6;
    text-decoration: none !important;
    transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
}
.bws-main-dropdown-card:hover,
.bws-dropdown-submenu:hover > .bws-main-dropdown-card,
.bws-dropdown-submenu.show > .bws-main-dropdown-card {
    background: #f8fafc;
    border-color: #cbd5e1;
    box-shadow: 0 3px 8px rgba(0,0,0,0.06);
    transform: translateY(-1px);
}
.bws-main-dropdown-card.active {
    background: #eff6ff;
    border-color: #bfdbfe;
}
.bws-main-card-icon {
    width: 34px;
    height: 34px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
}
.bws-main-card-title {
    font-size: 13px;
    font-weight: 700;
    color: #1e293b;
    line-height: 1.25;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.bws-main-card-sub {
    font-size: 10.5px;
    color: #64748b;
    line-height: 1.2;
    margin-top: 1px;
    font-family: 'Inter', sans-serif;
}

/* ================================================================
   BWS FLYOUT MENU (CARD & SECTIONED GROUP STYLE - OPSI B)
================================================================ */
.bws-dropdown-submenu .bws-flyout-menu {
    min-width: 320px !important;
    padding: 10px !important;
    border-radius: 14px !important;
    border: 1px solid #e2e8f0 !important;
    background: #ffffff !important;
    box-shadow: 0 20px 40px -6px rgba(15, 23, 42, 0.16), 0 10px 16px -6px rgba(15, 23, 42, 0.08) !important;
}

.bws-flyout-header-group {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 8px 4px 8px;
}
.bws-group-badge {
    font-size: 9.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 2px 7px;
    border-radius: 6px;
    line-height: 1.2;
}
.bws-badge-green { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.bws-badge-blue  { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }

.bws-group-title {
    font-size: 11px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-family: 'Inter', sans-serif;
}

.bws-flyout-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 10px;
    border-radius: 10px;
    text-decoration: none !important;
    transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
    margin-bottom: 2px;
    background: transparent;
    border: 1px solid transparent;
}
.bws-flyout-card:hover {
    background: #f8fafc;
    border-color: #e2e8f0;
    transform: translateX(3px);
}
.bws-flyout-card.active {
    background: #eff6ff;
    border-color: #bfdbfe;
}

.bws-flyout-card-left {
    display: flex;
    align-items: center;
    gap: 10px;
}
.bws-flyout-icon-box {
    width: 34px;
    height: 34px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13.5px;
    flex-shrink: 0;
}
.bws-icon-green  { background: #dcfce7; color: #16a34a; }
.bws-icon-blue   { background: #dbeafe; color: #2563eb; }
.bws-icon-amber  { background: #fef3c7; color: #d97706; }
.bws-icon-cyan   { background: #e0f2fe; color: #0284c7; }
.bws-icon-red    { background: #fee2e2; color: #dc2626; }
.bws-icon-indigo { background: #e0e7ff; color: #4f46e5; }

.bws-flyout-card-name {
    font-size: 12.5px;
    font-weight: 700;
    color: #1e293b;
    line-height: 1.25;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.bws-flyout-card-desc {
    font-size: 10.5px;
    color: #64748b;
    line-height: 1.2;
    margin-top: 1px;
    font-family: 'Inter', sans-serif;
}
.bws-flyout-pill-count {
    font-size: 11px;
    font-weight: 600;
    color: #475569;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    padding: 2px 8px;
    border-radius: 12px;
    white-space: nowrap;
}

.bws-flyout-divider {
    height: 1px;
    background: #f1f5f9;
    margin: 6px 4px;
}

.bws-flyout-footer-btn {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 9px 12px;
    border-radius: 10px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    text-decoration: none !important;
    transition: all 0.18s ease;
}
.bws-flyout-footer-btn:hover {
    background: #eff6ff;
    border-color: #93c5fd;
    transform: translateY(-1px);
}
.bws-flyout-footer-title {
    font-size: 12.5px;
    font-weight: 700;
    color: #1d4ed8;
    line-height: 1.2;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.bws-flyout-footer-sub {
    font-size: 10.5px;
    color: #64748b;
    line-height: 1.2;
    margin-top: 1px;
    font-family: 'Inter', sans-serif;
}
</style>
@endif
