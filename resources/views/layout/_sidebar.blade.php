{{--
    _sidebar.blade.php
    Partial View: Panel Filter Sidebar Kiri
    Halaman Peta Daerah Irigasi (DIR) - BWS Kalimantan I

    Fungsi: Menampilkan panel kontrol di sisi kiri halaman peta yang berisi:
    1. Dropdown View Mode (Table+Maps / Maps Only / Table Only)
    2. Tombol Kelola File GIS (buka modal daftar file - khusus Admin)
    3. Tombol Upload GeoJSON (buka modal upload - khusus Admin)

    Digunakan di: resources/views/dashboard/peta.blade.php
    Dipanggil via: @include('layout._sidebar')
--}}

<aside class="bws-sidebar">

    {{-- 1. VIEW MODE SWITCHER
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

    {{-- 2. TOMBOL AKSI GIS
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

        {{-- Tombol Upload File GIS (GeoJSON, SHP, KML) --}}
        <button type="button"
                class="btn btn-primary bws-btn-action shadow-sm text-white"
                data-toggle="modal"
                data-target="#modalUploadGeojson">
            <i class="fas fa-cloud-upload-alt"></i>
            <span>Upload File GIS</span>
        </button>

    </div>
    @endif

</aside>
