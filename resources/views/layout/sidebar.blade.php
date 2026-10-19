 <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <div class="sidebar-brand d-flex align-items-center justify-content-between px-3">
                <a class="d-flex align-items-center text-white text-decoration-none flex-grow-1" href="/home">
                    <div class="sidebar-brand-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="sidebar-brand-text mx-2">TOKO KAMI</div>
                </a>
                <button class="btn btn-sm btn-link text-white d-md-none p-1" id="sidebarMobileClose" type="button" title="Tutup Menu" style="opacity: 0.85;">
                    <i class="fas fa-times fa-lg"></i>
                </button>
            </div>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item {{ request()->is('home') || request()->is('/') || request()->is('dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Menu Utama
            </div>

            <!-- Nav Item - Tables -->
            <li class="nav-item {{ request()->is('produk*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('produk.index')}}">
                    <i class="fas fa-fw fa-boxes"></i>
                    <span>Produk</span></a>
            </li>
            <li class="nav-item {{ request()->is('kategori*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('kategori.index')}}">
                    <i class="fas fa-fw fa-tags"></i>
                    <span>Kategori</span></a>
            </li>
            <li class="nav-item {{ request()->is('peta') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('peta')}}">
                    <i class="fas fa-fw fa-map-marked-alt"></i>
                    <span>Peta Lokasi</span></a>
            </li>

            @if(Auth::check() && Auth::user()->role === 'superadmin')
            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Super Admin
            </div>

            <li class="nav-item {{ request()->is('users*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('users.index') }}">
                    <i class="fas fa-fw fa-users-cog"></i>
                    <span>Manajemen User</span></a>
            </li>

            <li class="nav-item {{ request()->is('activity-logs*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('activity-logs.index') }}">
                    <i class="fas fa-fw fa-history"></i>
                    <span>Log Aktivitas</span></a>
            </li>
            @elseif(Auth::check() && Auth::user()->role === 'admin')
            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Admin Panel
            </div>

            <li class="nav-item {{ request()->is('users*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('users.index') }}">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Daftar Pengguna</span></a>
            </li>
            @endif

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->