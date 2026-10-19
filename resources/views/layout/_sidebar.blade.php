{{--
    _sidebar.blade.php
    Partial View: Panel Filter Sidebar Kiri
    Halaman Peta Daerah Irigasi (DIR) - BWS Kalimantan I

    Fungsi: Menampilkan panel kontrol di sisi kiri halaman peta yang berisi:
    1. Dropdown Dashboard Type
    2. Input Filter Tanggal (readonly, tampil tanggal hari ini)
    3. Dropdown View Mode (Table+Maps / Maps Only / Table Only)
    4. Tombol Kelola File GIS (buka modal daftar file)
    5. Tombol Upload GeoJSON (buka modal upload)

    Digunakan di: resources/views/dashboard/peta.blade.php
    Dipanggil via: @include('layout._sidebar')
--}}

<aside class="bws-sidebar">

    {{-- 1. DASHBOARD TYPE
         Fungsi: Dropdown memilih jenis dashboard.
         Saat ini hanya ada 1 opsi "Dashboard Data". --}}
    <div class="bws-filter-group">
        <label class="bws-filter-label">Dashboard Type</label>
        <select class="form-control bws-select-control" id="filterDashboardType">
            <option value="data" selected>Dashboard Data</option>
        </select>
    </div>

    {{-- 2. VIEW MODE SWITCHER
         Fungsi: Dropdown untuk memilih tampilan halaman:
         - "Table + Maps" → Tampilkan peta DAN tabel (default)
         - "Maps Only"    → Hanya peta, diperbesar penuh layar
         - "Table Only"   → Hanya tabel data koordinat GIS
         Perubahan ditangani oleh fungsi switchViewMode() di peta-map.js --}}
    <div class="bws-filter-group">
        <label class="bws-filter-label">View</label>
        <select class="form-control bws-select-control"
                id="filterViewMode"
                onchange="switchViewMode(this.value)">
            <option value="both" selected>Table + Maps</option>
            <option value="maps">Maps Only</option>
            <option value="table">Table Only</option>
        </select>
    </div>

    {{-- 3. TOMBOL AKSI GIS
         Hanya tampil untuk pengguna dengan peran Admin atau Super Admin.
         Pengguna Tamu / Publik hanya dalam mode lihat, filter, dan unduh data. --}}
    @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'superadmin']))
    <div class="bws-btn-block">

        {{-- Tombol Kelola File GIS --}}
        <button type="button"
                class="btn btn-outline-primary bws-btn-action shadow-sm"
                data-toggle="modal"
                data-target="#modalKelolaGeojson">
            <i class="fas fa-folder-open"></i>
            <span>Kelola File GIS</span>
            <span class="badge badge-primary ml-auto" id="sidebar-file-count">
                {{ count($fileList ?? []) }}
            </span>
        </button>

        {{-- Tombol Upload GeoJSON --}}
        <button type="button"
                class="btn btn-primary bws-btn-action shadow-sm text-white"
                data-toggle="modal"
                data-target="#modalUploadGeojson">
            <i class="fas fa-cloud-upload-alt"></i>
            <span>Upload GeoJSON</span>
        </button>

    </div>
    @else
    {{-- Info Box untuk Mode Publik / Tamu --}}
    <div class="card border-0 bg-light shadow-sm text-center p-3 mt-2" style="border-radius: 8px;">
        <div class="mb-1 text-primary">
            <i class="fas fa-globe-asia fa-lg"></i>
        </div>
        <div class="font-weight-bold text-dark" style="font-size: 12px;">Mode Publik / Viewer</div>
        <div class="text-muted mt-1" style="font-size: 10.5px; line-height: 1.4;">
            Anda dapat melihat peta, menyaring data spasial, dan mengunduh tabel CSV.
        </div>
        @guest
        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary mt-2 font-weight-bold" style="font-size: 11px;">
            <i class="fas fa-sign-in-alt mr-1"></i> Login Admin / Staf
        </a>
        @endguest
    </div>
    @endif

</aside>
