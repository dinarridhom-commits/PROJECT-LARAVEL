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
         Tombol "Kelola File GIS": Membuka modal daftar file GeoJSON
         yang tersimpan di server (folder public/geojson/).
         Badge angka menampilkan jumlah file yang tersedia.

         Tombol "Upload GeoJSON": Membuka modal form upload file
         GeoJSON baru ke server secara batch (chunked). --}}
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

</aside>
