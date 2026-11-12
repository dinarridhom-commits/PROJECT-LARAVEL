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
            {{-- Tombol Cepat Buka Peta WebGIS (Saran Utama) --}}
            @if(!$isMapPage)
            <a href="{{ route('peta') }}" class="bws-btn-peta-nav" title="Buka Peta Interaktif WebGIS">
                <i class="fas fa-map-marked-alt mr-1.5"></i>
                <span>Buka Peta</span>
                <span class="badge badge-primary px-1.5 py-0.5 ml-1 text-white font-weight-bold" style="font-size: 10px; border-radius: 10px; background: linear-gradient(135deg, #1d4ed8, #2563eb);">54 Layer</span>
            </a>
            @endif
            @endauth

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
                            <i class="fas fa-trash-alt mr-1"></i> Tong Sampah GIS
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
@endif
