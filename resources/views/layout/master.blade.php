<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>@yield('title', 'DINAR SHOP')</title>

    <!-- Custom fonts for this template-->
    <link href="{{asset('sbadmin')}}/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{asset('sbadmin')}}/css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Leaflet CSS (untuk peta interaktif) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

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

<body id="page-top">

    <!-- Backdrop Gelap untuk Menu Mobile -->
    <div id="sidebarMobileBackdrop" class="sidebar-mobile-backdrop"></div>

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        @include('layout.sidebar')

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                @include('layout.navbar')

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    @yield('konten')
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            @include('layout.footer')

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Yakin ingin keluar?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Apakah anda yakin ingin Logout?</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

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

    @stack('scripts')

</body>

</html>