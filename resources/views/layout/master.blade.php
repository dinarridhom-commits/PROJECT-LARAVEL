<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>@yield('title', 'FEWS BWS KALIMANTAN I - Sistem Informasi Irigasi')</title>

    <!-- Custom fonts for this template-->
    <link href="{{asset('sbadmin')}}/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{asset('sbadmin')}}/css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Leaflet CSS (untuk peta interaktif) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- CSS Khusus BWS Kalimantan I WebGIS & Navigasi -->
    <link rel="stylesheet" href="{{ asset('css/peta.css') }}?v={{ filemtime(public_path('css/peta.css')) }}">

    <!-- Responsive Styles untuk Tampilan Layar HP & Berbagai Perangkat -->
    <style>
        /* Mencegah horizontal scroll overflow di seluruh halaman */
        html, body {
            overflow-x: hidden !important;
            max-width: 100vw;
            width: 100%;
        }

        #wrapper {
            overflow-x: hidden !important;
            width: 100%;
        }

        /* Overlay Gelap saat Menu Dibuka di Layar HP */
        .sidebar-mobile-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(3px);
            -webkit-backdrop-filter: blur(3px);
            z-index: 1045;
            opacity: 0;
            transition: opacity 0.25s ease-in-out;
        }
        .sidebar-mobile-backdrop.show {
            display: block;
            opacity: 1;
        }

        /* Penyesuaian Responsif untuk Perangkat HP (< 768px) */
        @media (max-width: 767.98px) {
            /* Sidebar disembunyikan secara default menjadi Off-Canvas Drawer */
            #accordionSidebar {
                position: fixed !important;
                top: 0 !important;
                left: -280px !important;
                width: 260px !important;
                height: 100vh !important;
                min-height: 100vh !important;
                max-height: 100vh !important;
                overflow-y: auto !important;
                -webkit-overflow-scrolling: touch;
                z-index: 1055 !important;
                transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
                box-shadow: 6px 0 25px rgba(0, 0, 0, 0.35) !important;
                margin: 0 !important;
            }

            /* Saat tombol menu HP diklik, sidebar meluncur masuk */
            #accordionSidebar.mobile-open {
                left: 0 !important;
            }

            /* Area konten memenuhi 100% lebar layar HP */
            #content-wrapper {
                width: 100% !important;
                min-width: 0 !important;
                margin-left: 0 !important;
            }

            /* Padding halaman dibuat pas dan nyaman di layar HP */
            .container-fluid {
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
            }

            /* Topbar navbar lebih proporsional di HP */
            .topbar {
                height: 3.75rem !important;
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
                margin-bottom: 1rem !important;
            }

            /* Ukuran font judul di layar HP */
            h1.h3, .h3 {
                font-size: 1.25rem !important;
                line-height: 1.35 !important;
            }

            /* Modal dialog menyesuaikan lebar layar HP */
            .modal-dialog {
                margin: 0.5rem auto !important;
                max-width: 95% !important;
            }
        }

        /* Layar HP Sangat Sempit (< 480px, misal ponsel kecil/lipat) */
        @media (max-width: 480px) {
            .container-fluid {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }
            .btn-sm {
                padding: 0.25rem 0.5rem !important;
                font-size: 0.8rem !important;
            }
        }
    </style>
    @stack('styles')
</head>

<body id="page-top" class="bg-light">

    {{-- Header & Navigasi Horizontal FEWS BWS KALIMANTAN I
         (Dashboard, Master Data Infrastruktur, Manajemen User, Log Aktivitas) --}}
    @include('layout._top_nav')

    <!-- Main Content Container (Full Width / Standar Portal FEWS BWS) -->
    <main class="py-4" style="min-height: calc(100vh - 160px);">
        <div class="container-fluid px-4">
            @yield('konten')
        </div>
    </main>

    <!-- Footer -->
    @include('layout.footer')

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal (Modern Centered Dialog) -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 420px;">
            <div class="modal-content border-0 shadow-lg position-relative" style="border-radius: 18px; overflow: hidden;">
                <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="top: 16px; right: 18px; z-index: 10; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #f1f5f9; font-size: 18px; opacity: 0.8; transition: all 0.15s ease;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <div class="modal-body text-center p-4 pt-5 pb-4">
                    {{-- Icon Logout Bulat Tengah --}}
                    <div style="width: 68px; height: 68px; border-radius: 50%; background: #fee2e2; color: #dc2626; display: inline-flex; align-items: center; justify-content: center; font-size: 26px; margin-bottom: 18px; box-shadow: 0 0 0 8px #fef2f2;">
                        <i class="fas fa-sign-out-alt"></i>
                    </div>
                    <h5 class="font-weight-bold text-dark mb-2" id="logoutModalLabel" style="font-size: 19px; letter-spacing: -0.2px;">
                        Keluar dari Akun?
                    </h5>
                    <p class="text-muted mb-4" style="font-size: 13.5px; line-height: 1.5; max-width: 330px; margin: 0 auto 24px auto;">
                        Apakah Anda yakin ingin keluar? Sesi Anda akan diakhiri dan Anda perlu masuk kembali untuk mengakses sistem.
                    </p>
                    <div class="d-flex align-items-center justify-content-center" style="gap: 12px;">
                        <button type="button" class="btn btn-light font-weight-bold px-4 py-2.5" data-dismiss="modal" style="border-radius: 10px; font-size: 13.5px; min-width: 120px; border: 1px solid #e2e8f0; color: #475569; background: #f8fafc;">
                            Batal
                        </button>
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-danger font-weight-bold px-4 py-2.5" style="border-radius: 10px; font-size: 13.5px; min-width: 140px; background: linear-gradient(135deg, #ef4444, #dc2626); border: none; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.35);">
                                <i class="fas fa-sign-out-alt mr-1.5"></i> Ya, Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    @include('dashboard.partials._modal_edit_profile')

    <!-- Bootstrap core JavaScript-->
    <script src="{{asset('sbadmin')}}/vendor/jquery/jquery.min.js"></script>
    <script src="{{asset('sbadmin')}}/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{asset('sbadmin')}}/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{asset('sbadmin')}}/js/sb-admin-2.min.js"></script>

    <!-- Script Kontrol Responsif Menu Layar HP -->
    <script>
        (function($) {
            $(document).ready(function() {
                var $sidebar = $('#accordionSidebar');
                var $backdrop = $('#sidebarMobileBackdrop');

                function openMobileSidebar() {
                    $sidebar.addClass('mobile-open');
                    $backdrop.addClass('show');
                    $('body').css('overflow', 'hidden');
                }

                function closeMobileSidebar() {
                    $sidebar.removeClass('mobile-open');
                    $backdrop.removeClass('show');
                    $('body').css('overflow', '');
                }

                // Tombol hamburger di navbar (khusus HP)
                $('#sidebarToggleTop').off('click').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if ($(window).width() < 768) {
                        if ($sidebar.hasClass('mobile-open')) {
                            closeMobileSidebar();
                        } else {
                            openMobileSidebar();
                        }
                    } else {
                        $("body").toggleClass("sidebar-toggled");
                        $(".sidebar").toggleClass("toggled");
                    }
                });

                // Tutup menu saat tap area backdrop luar
                $backdrop.on('click', function() {
                    closeMobileSidebar();
                });

                // Tutup menu saat tap tombol silang (X)
                $('#sidebarMobileClose').on('click', function() {
                    closeMobileSidebar();
                });

                // Tutup menu saat navigasi link diklik di HP
                $sidebar.find('.nav-link').on('click', function() {
                    if ($(window).width() < 768) {
                        closeMobileSidebar();
                    }
                });

                // Deteksi perubahan orientasi atau resize layar di simulator HP
                $(window).on('resize orientationchange', function() {
                    if ($(window).width() >= 768) {
                        closeMobileSidebar();
                    }
                });
            });
        })(jQuery);
    </script>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Leaflet JS (untuk peta interaktif) - harus sebelum @stack('scripts') -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

    <!-- Fullscreen toggle script -->
    <script>
        var btnFsMaster = document.getElementById('btnFullscreenToggle');
        if (btnFsMaster && !window.btnFsInitialized) {
            window.btnFsInitialized = true;
            btnFsMaster.addEventListener('click', function() {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen().catch(function(err){});
                    btnFsMaster.innerHTML = '<i class="fas fa-compress"></i>';
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                        btnFsMaster.innerHTML = '<i class="fas fa-expand"></i>';
                    }
                }
            });
        }
    </script>

    @stack('scripts')

</body>

</html>