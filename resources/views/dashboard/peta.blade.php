@extends('layout.master')

@section('title', 'Peta Interaktif Daerah Irigasi (DIR) - BWS Kalimantan I')

@section('konten')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-3 mb-sm-4">
    <div class="mb-2 mb-sm-0">
        <h1 class="h3 mb-1 text-gray-800 font-weight-bold" style="font-size: clamp(1.2rem, 3.5vw, 1.75rem);">
            <i class="fas fa-map-marked-alt text-primary mr-2"></i>Peta Interaktif Daerah Irigasi (DIR)
        </h1>
        <p class="text-muted mb-0 small">Sistem Informasi Geografis (SIG) Terintegrasi Data ArcMap - Balai Wilayah Sungai Kalimantan I Pontianak</p>
    </div>
    <div class="mt-2 mt-sm-0 d-flex flex-wrap align-items-center" style="gap: 6px;">
        <button type="button" class="btn btn-sm btn-outline-secondary shadow-sm font-weight-bold" data-toggle="modal" data-target="#modalKelolaGeojson">
            <i class="fas fa-folder-open mr-1"></i> Kelola File GIS <span class="badge badge-primary ml-1">{{ count($fileList ?? []) }}</span>
        </button>
        <button type="button" class="btn btn-sm btn-info shadow-sm font-weight-bold" data-toggle="modal" data-target="#modalUploadGeojson">
            <i class="fas fa-cloud-upload-alt mr-1"></i> Upload GeoJSON
        </button>
    </div>
</div>

{{-- Flash Message Notifikasi --}}
<div id="js-flash-alert-container"></div>
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
    <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
    <div class="font-weight-bold mb-1"><i class="fas fa-exclamation-circle mr-1"></i>Terjadi Kesalahan:</div>
    <ul class="mb-0 pl-3">
        @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<!-- Modal Upload GeoJSON (Mendukung Batch Upload Puluhan File Sekaligus) -->
<div class="modal fade" id="modalUploadGeojson" tabindex="-1" role="dialog" aria-labelledby="modalUploadGeojsonLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="formUploadGeojson" action="{{ route('peta.upload-geojson') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold" id="modalUploadGeojsonLabel">
                    <i class="fas fa-cloud-upload-alt mr-2"></i>Upload File GeoJSON
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" id="btnCloseUploadModal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="form-group mb-3" id="uploadInputContainer">
                    <label class="font-weight-bold text-gray-800">
                        <i class="fas fa-file-code text-primary mr-1"></i>Pilih File GeoJSON / JSON:
                    </label>
                    <div class="custom-file">
                        <input type="file" name="file_geojson[]" class="custom-file-input" id="inputGeojsonFile" multiple accept=".geojson,.json" required onchange="updateFileInputLabel(this)">
                        <label class="custom-file-label" for="inputGeojsonFile" id="labelGeojsonFile">Pilih satu atau beberapa file...</label>
                    </div>
                    <small class="form-text text-muted mt-2">
                        <i class="fas fa-info-circle mr-1"></i>Format yang didukung: <code>.geojson</code> atau <code>.json</code>. Anda dapat memilih puluhan file sekaligus (misal 54 file, otomatis diunggah bertahap tanpa limit).
                    </small>
                </div>

                {{-- Progress Bar (Tampil otomatis saat proses upload banyak file berjalan) --}}
                <div id="uploadProgressSection" class="mb-3" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="font-weight-bold small text-primary" id="uploadStatusText">
                            <i class="fas fa-spinner fa-spin mr-1"></i>Mengunggah file...
                        </span>
                        <span class="font-weight-bold small text-muted" id="uploadPercentText">0%</span>
                    </div>
                    <div class="progress" style="height: 18px; border-radius: 9px;">
                        <div id="uploadProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success font-weight-bold" role="progressbar" style="width: 0%; font-size: 11px;">0%</div>
                    </div>
                    <div class="small text-muted mt-2 d-flex justify-content-between" id="uploadDetailText">
                        <span>Memproses batch file...</span>
                        <span id="uploadBatchCountText">Batch 0/0</span>
                    </div>
                </div>

                <div id="uploadSuccessSection" class="alert alert-success py-2 px-3 small mb-3" style="display: none;">
                    <i class="fas fa-check-circle mr-1"></i><span id="uploadSuccessMessage">Semua file berhasil diunggah!</span>
                </div>

                <div id="uploadErrorSection" class="alert alert-danger py-2 px-3 small mb-3" style="display: none;">
                    <i class="fas fa-exclamation-triangle mr-1"></i><span id="uploadErrorMessage"></span>
                </div>

                <div class="alert alert-info py-2 px-3 small mb-0" id="uploadInfoNote">
                    <i class="fas fa-folder-open mr-1"></i>File yang diunggah akan langsung disimpan ke folder <code>public/geojson/</code> dan otomatis dimuat ke peta interaktif.
                </div>
            </div>
            <div class="modal-footer bg-light" id="uploadModalFooter">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" id="btnBatalUpload">
                    <i class="fas fa-times mr-1"></i>Batal
                </button>
                <button type="submit" class="btn btn-primary font-weight-bold" id="btnSubmitUpload">
                    <i class="fas fa-upload mr-1"></i>Upload Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Kelola & Hapus File GeoJSON -->
<div class="modal fade" id="modalKelolaGeojson" tabindex="-1" role="dialog" aria-labelledby="modalKelolaGeojsonLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title font-weight-bold" id="modalKelolaGeojsonLabel">
                    <i class="fas fa-folder-open text-warning mr-2"></i>Kelola File GIS (public/geojson)
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <div class="small text-muted mb-2 mb-sm-0 d-flex align-items-center flex-wrap gap-2">
                        <span>Total tersimpan: <span class="badge badge-primary px-2 py-1">{{ count($fileList ?? []) }} File GIS</span></span>
                        <span id="bulk-selected-info" class="ml-2" style="display:none;">
                            <span class="badge badge-warning text-dark px-2 py-1">
                                <i class="fas fa-check-square mr-1"></i><span id="bulk-selected-count">0</span> Dipilih
                            </span>
                        </span>
                    </div>
                    <div class="w-50">
                        <input type="text" id="cari-file-geojson" class="form-control form-control-sm" placeholder="🔍 Cari nama file..." onkeyup="filterTabelFileGeojson(this.value)">
                    </div>
                </div>

                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-sm table-hover table-bordered mb-0" id="tabel-kelola-geojson">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%" class="text-center">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="chk-select-all-geojson">
                                        <label class="custom-control-label" for="chk-select-all-geojson"></label>
                                    </div>
                                </th>
                                <th width="4%" class="text-center">#</th>
                                <th>Nama File</th>
                                <th width="13%" class="text-center">Ukuran</th>
                                <th width="20%" class="text-center">Waktu Simpan</th>
                                <th width="13%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fileList ?? [] as $index => $f)
                            <tr class="item-file-row">
                                <td class="text-center align-middle">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input chk-file-geojson" id="chk-file-{{ $index }}" value="{{ $f['name'] }}">
                                        <label class="custom-control-label" for="chk-file-{{ $index }}"></label>
                                    </div>
                                </td>
                                <td class="text-center font-weight-bold text-muted align-middle">{{ $index + 1 }}</td>
                                <td class="align-middle">
                                    <i class="fas fa-file-code text-info mr-1"></i>
                                    <span class="nama-file-text font-weight-bold">{{ $f['name'] }}</span>
                                    @if(!$f['is_default'])
                                        <span class="badge badge-warning text-dark ml-1 small"><i class="fas fa-cloud-upload-alt mr-1"></i>Upload</span>
                                    @else
                                        <span class="badge badge-light border text-secondary ml-1 small">Sistem</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle"><span class="badge badge-light border">{{ $f['size'] }}</span></td>
                                <td class="text-center small text-muted align-middle">{{ $f['date'] }}</td>
                                <td class="text-center align-middle">
                                    <form action="{{ route('peta.delete-geojson') }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus file \'{{ $f['name'] }}\'? File ini akan dihapus secara permanen dari server dan tidak akan dimuat lagi di peta.')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="file_name" value="{{ $f['name'] }}">
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2 font-weight-bold" title="Hapus File Ini">
                                            <i class="fas fa-trash-alt mr-1"></i>Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">Tidak ada file GIS di folder public/geojson</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light py-2 d-flex justify-content-between align-items-center">
                {{-- Tombol Hapus Terpilih (Bulk Delete) --}}
                <div>
                    <button type="button" id="btn-hapus-terpilih" class="btn btn-danger btn-sm font-weight-bold" disabled
                        onclick="konfirmasiHapusTerpilih()">
                        <i class="fas fa-trash-alt mr-1"></i>Hapus Terpilih (<span id="btn-bulk-count">0</span>)
                    </button>
                </div>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Form tersembunyi untuk submit bulk delete --}}
<form id="form-bulk-delete" action="{{ route('peta.delete-geojson-bulk') }}" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
    <div id="bulk-delete-inputs"></div>
</form>

{{-- Filter Bar: muncul kondisional berdasarkan ketersediaan file GIS --}}
@if($hasFiles)
<div class="card shadow-sm mb-4 border-left-primary">
    <div class="card-body py-2 px-3 d-flex flex-wrap align-items-center justify-content-between">
        <div class="d-flex align-items-center my-1">
            <span class="font-weight-bold text-gray-700 mr-2 small"><i class="fas fa-filter text-primary mr-1"></i>Filter:</span>
            <div class="btn-group btn-group-sm" role="group" id="btn-filter-wilayah">
                <button type="button" class="btn btn-outline-primary active" data-filter="all">
                    <i class="fas fa-layer-group mr-1"></i>Semua Data GIS
                    <span class="badge badge-primary ml-1">{{ count($fileList) }} File</span>
                </button>
            </div>
        </div>
        <div class="small text-muted my-1" id="info-fitur-termuat">
            <i class="fas fa-spinner fa-spin text-info mr-1"></i> Memuat layer GIS ArcMap...
        </div>
    </div>
</div>
@else
<div class="card shadow-sm mb-4 border-left-warning">
    <div class="card-body py-2 px-3 d-flex align-items-center">
        <i class="fas fa-exclamation-triangle text-warning mr-2"></i>
        <span class="small text-muted">
            Belum ada file GIS yang diunggah. Gunakan tombol
            <button type="button" class="btn btn-xs btn-info btn-sm py-0 px-2 mx-1" data-toggle="modal" data-target="#modalUploadGeojson">
                <i class="fas fa-cloud-upload-alt mr-1"></i>Upload GeoJSON
            </button>
            untuk menambahkan data ke peta.
        </span>
    </div>
</div>
@endif


<!-- Peta Interaktif Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-gradient-primary text-white">
        <h6 class="m-0 font-weight-bold">
            <i class="fas fa-globe-asia mr-1"></i> Visualisasi Spasial (WebGIS BWSK I)
        </h6>
        <div id="map-loading" class="small text-white font-weight-bold">
            <i class="fas fa-spinner fa-spin mr-1"></i> Membaca data koordinat ArcMap...
        </div>
    </div>
    <div class="card-body p-0 position-relative">
        <div id="map" class="map-container-responsive"></div>
    </div>
    <div class="card-footer bg-light text-muted small d-flex flex-wrap justify-content-between align-items-center py-2">
        <div>
            <i class="fas fa-mouse-pointer text-primary mr-1"></i>
            Klik fitur pada peta (Saluran, Bangunan, atau Area DIR) untuk menampilkan tabel atribut lengkap dari ArcMap.
        </div>
        <div class="font-weight-bold text-primary">
            Data IGT DIR Kewenangan BWSK I Pontianak
        </div>
    </div>
</div>

<!-- Tabel Daftar Koordinat & Data Spasial -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-wrap justify-content-between align-items-center">
        <div class="d-flex align-items-center flex-wrap my-1">
            <h6 class="m-0 font-weight-bold text-primary mr-2">
                <i class="fas fa-list-alt mr-2"></i>Daftar Koordinat & Data Spasial GIS
            </h6>
            @if($hasFiles)
                <span class="badge badge-info small mr-2" id="badge-total-file-tabel">{{ count($fileList) }} File Aktif</span>
            @endif
        </div>
        @if($hasFiles)
        <div class="d-flex align-items-center flex-wrap my-1 w-100 w-md-auto justify-content-between justify-content-md-end" style="gap: 8px;">
            {{-- Toggle Mode: Per File GIS vs Rincian Objek --}}
            <div class="btn-group btn-group-sm flex-wrap" role="group" id="tabel-mode-switch">
                <button type="button" class="btn btn-primary active font-weight-bold" id="btn-mode-file" onclick="setTabelMode('file')">
                    <i class="fas fa-folder mr-1"></i>Ringkasan File (<span id="count-mode-file">{{ count($fileList) }}</span>)
                </button>
                <button type="button" class="btn btn-outline-primary font-weight-bold" id="btn-mode-objek" onclick="setTabelMode('objek')">
                    <i class="fas fa-map-marker-alt mr-1"></i>Rincian Objek (<span id="count-mode-objek">0</span>)
                </button>
            </div>
            {{-- Input Pencarian Cepat --}}
            <div class="flex-grow-1 flex-md-grow-0" style="min-width: 180px; max-width: 100%;">
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-light border-right-0"><i class="fas fa-search text-muted"></i></span>
                    </div>
                    <input type="text" id="cari-tabel-koordinat" class="form-control border-left-0" placeholder="Cari nama objek/file..." onkeyup="filterTabelKoordinat(this.value)">
                </div>
            </div>
        </div>
        @endif
    </div>
    <div class="card-body {{ $hasFiles ? '' : 'p-0' }}">
        @if($hasFiles)
        <div class="table-responsive" style="max-height: 550px; overflow-y: auto;">
            <table class="table table-bordered table-hover table-sm mb-0" id="tabel-lokasi">
                <thead class="thead-dark sticky-top">
                    <tr>
                        <th width="4%" class="text-center align-middle">#</th>
                        <th class="align-middle">Nama Objek / Fitur</th>
                        <th width="18%" class="align-middle">Wilayah / Kategori</th>
                        <th width="13%" class="text-center align-middle">Latitude (Y)</th>
                        <th width="13%" class="text-center align-middle">Longitude (X)</th>
                        <th width="22%" class="align-middle">Sumber File</th>
                        <th width="10%" class="text-center align-middle">Navigasi Peta</th>
                    </tr>
                </thead>
                <tbody id="tbody-lokasi">
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-spinner fa-spin text-primary mr-2"></i>Membaca koordinat dari {{ count($fileList) }} file GIS...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-light py-2 px-3 d-flex justify-content-between align-items-center small text-muted">
            <span id="tabel-footer-info">Memuat data koordinat...</span>
            <span><i class="fas fa-sync-alt text-success mr-1"></i>Otomatis sinkron dengan file yang diunggah</span>
        </div>
        @else
        <div class="text-center py-5 px-3">
            <div class="mb-3">
                <i class="fas fa-map-marked-alt fa-4x text-gray-300"></i>
            </div>
            <h5 class="font-weight-bold text-gray-600 mb-2">Peta & Data Masih Kosong</h5>
            <p class="text-muted small mb-4">Belum ada file GeoJSON yang diunggah ke sistem. Upload file untuk menampilkan layer peta, legenda, dan koordinat secara otomatis.</p>
            <button type="button" class="btn btn-info font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalUploadGeojson">
                <i class="fas fa-cloud-upload-alt mr-2"></i>Upload File GeoJSON Sekarang
            </button>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<!-- Proj4 JS untuk reproyeksi koordinat -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.9.0/proj4.js"></script>

<style>
    .map-legend {
        background: rgba(255,255,255,0.97);
        padding: 10px 14px;
        border-radius: 8px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.22);
        font-size: 12px;
        line-height: 19px;
        max-width: 230px;
        border: 1px solid #ddd;
    }
    .map-legend h6 {
        font-size: 13px;
        font-weight: bold;
        margin-bottom: 8px;
        border-bottom: 1px solid #e0e0e0;
        padding-bottom: 5px;
        color: #333;
    }
    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
        gap: 8px;
    }
    .legend-color-box {
        width: 20px;
        height: 13px;
        border-radius: 3px;
        border: 1px solid rgba(0,0,0,0.25);
        flex-shrink: 0;
    }
    .legend-line {
        width: 22px;
        height: 4px;
        border-radius: 2px;
        flex-shrink: 0;
    }
    .legend-circle {
        width: 11px;
        height: 11px;
        border-radius: 50%;
        border: 1.5px solid #fff;
        box-shadow: 0 0 0 1px #999;
        flex-shrink: 0;
    }
    .custom-dir-pin {
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        color: #fff;
        font-weight: bold;
        box-shadow: 0 2px 6px rgba(0,0,0,0.4);
        border: 2.5px solid #ffffff;
    }
    .leaflet-popup-content-wrapper {
        border-radius: 8px;
        box-shadow: 0 4px 18px rgba(0,0,0,0.22);
        padding: 2px;
    }
    .leaflet-popup-content {
        margin: 10px 14px;
        line-height: 1.45;
    }
    .gis-popup-table { font-size: 11px; margin-bottom: 0; }
    .gis-popup-table td, .gis-popup-table th { padding: 3px 6px; }
    .leaflet-control-layers {
        border-radius: 8px !important;
        box-shadow: 0 2px 12px rgba(0,0,0,0.18) !important;
        border: 1px solid #ccc !important;
        font-size: 13px !important;
        transition: all 0.2s ease;
    }
    .leaflet-control-layers-expanded {
        padding: 8px 12px !important;
        min-width: 210px !important;
    }
    .leaflet-control-layers label { margin-bottom: 4px !important; cursor: pointer !important; }
    
    /* Header & Tombol Minimize untuk Layer Control & Legenda */
    .custom-control-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-weight: 700;
        font-size: 13px;
        color: #2d3748;
        padding-bottom: 6px;
        margin-bottom: 8px;
        border-bottom: 1px solid #e2e8f0;
        cursor: pointer;
        user-select: none;
    }
    .custom-control-header:hover {
        color: #1a56db;
    }
    .custom-control-header .control-header-title {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .btn-toggle-box {
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        width: 22px;
        height: 22px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 11px;
        color: #475569;
        transition: all 0.2s ease;
        padding: 0;
        line-height: 1;
    }
    .btn-toggle-box:hover {
        background: #e2e8f0;
        color: #0f172a;
        border-color: #94a3b8;
    }
    .btn-toggle-box:focus {
        outline: none;
    }

    /* State Minimized untuk Layer Control */
    .leaflet-control-layers.is-minimized .leaflet-control-layers-list {
        display: none !important;
    }
    .leaflet-control-layers.is-minimized .custom-control-header {
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
        border-bottom: none !important;
    }
    .leaflet-control-layers.is-minimized {
        padding: 6px 10px !important;
        min-width: unset !important;
    }

    /* State Minimized untuk Legenda */
    .map-legend.is-minimized .legend-content {
        display: none !important;
    }
    .map-legend.is-minimized .custom-control-header {
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
        border-bottom: none !important;
    }
    .map-legend.is-minimized {
        padding: 6px 10px !important;
        max-width: unset !important;
    }

    /* Penyesuaian Responsif Map & Kontrol untuk Berbagai Ukuran Layar HP */
    .map-container-responsive {
        height: 580px;
        width: 100%;
        z-index: 1;
    }
    @media (max-width: 991.98px) {
        .map-container-responsive {
            height: 460px;
        }
    }
    @media (max-width: 767.98px) {
        .map-container-responsive {
            height: 380px !important;
        }
        .leaflet-control-layers-expanded {
            min-width: 175px !important;
            max-width: 220px !important;
            font-size: 11.5px !important;
            padding: 6px 8px !important;
            max-height: 220px !important;
            overflow-y: auto !important;
        }
        .map-legend {
            max-width: 175px !important;
            font-size: 11px !important;
            line-height: 16px !important;
            padding: 6px 10px !important;
            max-height: 200px !important;
            overflow-y: auto !important;
        }
        .leaflet-popup-content {
            margin: 8px 10px !important;
            max-width: 240px !important;
            font-size: 11px !important;
        }
        .custom-control-header {
            font-size: 11.5px !important;
        }
    }
    @media (max-width: 480px) {
        .map-container-responsive {
            height: 340px !important;
        }
        .leaflet-control-layers-expanded {
            min-width: 155px !important;
            font-size: 11px !important;
        }
        .map-legend {
            max-width: 155px !important;
            font-size: 10px !important;
        }
    }
</style>

<script>
function filterTabelFileGeojson(keyword) {
    keyword = (keyword || '').toLowerCase();
    document.querySelectorAll('#tabel-kelola-geojson tbody tr.item-file-row').forEach(function(row) {
        var el = row.querySelector('.nama-file-text');
        if (!el) return;
        var text = el.innerText.toLowerCase();
        row.style.display = text.indexOf(keyword) !== -1 ? '' : 'none';
    });
}

function updateBulkDeleteUI() {
    var checked = document.querySelectorAll('.chk-file-geojson:checked');
    var total = checked.length;
    var countEl = document.getElementById('bulk-selected-count');
    var btnCountEl = document.getElementById('btn-bulk-count');
    var infoEl = document.getElementById('bulk-selected-info');
    var btn = document.getElementById('btn-hapus-terpilih');

    if (countEl) countEl.textContent = total;
    if (btnCountEl) btnCountEl.textContent = total;
    if (infoEl) infoEl.style.display = total > 0 ? '' : 'none';
    if (btn) btn.disabled = total === 0;

    // Update state checkbox select-all
    var all = document.querySelectorAll('.chk-file-geojson');
    var chkAll = document.getElementById('chk-select-all-geojson');
    if (chkAll) {
        if (total === 0) {
            chkAll.indeterminate = false;
            chkAll.checked = false;
        } else if (total === all.length) {
            chkAll.indeterminate = false;
            chkAll.checked = true;
        } else {
            chkAll.indeterminate = true;
            chkAll.checked = false;
        }
    }
}

function konfirmasiHapusTerpilih() {
    var checked = document.querySelectorAll('.chk-file-geojson:checked');
    if (checked.length === 0) return;

    var names = [];
    checked.forEach(function(chk) { names.push(chk.value); });

    var msg = 'Anda akan menghapus ' + checked.length + ' file GIS secara permanen:\n\n';
    names.forEach(function(n, i) { msg += (i + 1) + '. ' + n + '\n'; });
    msg += '\nFile yang dihapus tidak dapat dikembalikan. Lanjutkan?';

    if (!confirm(msg)) return;

    // Isi hidden form dengan input array
    var container = document.getElementById('bulk-delete-inputs');
    container.innerHTML = '';
    names.forEach(function(name) {
        var inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'file_names[]';
        inp.value = name;
        container.appendChild(inp);
    });

    document.getElementById('form-bulk-delete').submit();
}

// Inisialisasi event checkbox setelah DOM siap
document.addEventListener('DOMContentLoaded', function() {
    // Checkbox "Pilih Semua"
    var chkAll = document.getElementById('chk-select-all-geojson');
    if (chkAll) {
        chkAll.addEventListener('change', function() {
            document.querySelectorAll('.chk-file-geojson').forEach(function(chk) {
                chk.checked = chkAll.checked;
            });
            updateBulkDeleteUI();
        });
    }

    // Checkbox per baris
    document.querySelectorAll('.chk-file-geojson').forEach(function(chk) {
        chk.addEventListener('change', function() {
            updateBulkDeleteUI();
        });
    });

    // Reset checkbox saat modal ditutup
    var modalKelola = document.getElementById('modalKelolaGeojson');
    if (modalKelola) {
        modalKelola.addEventListener('hidden.bs.modal', function() {
            document.querySelectorAll('.chk-file-geojson').forEach(function(chk) { chk.checked = false; });
            var chkAllEl = document.getElementById('chk-select-all-geojson');
            if (chkAllEl) { chkAllEl.checked = false; chkAllEl.indeterminate = false; }
            updateBulkDeleteUI();
        });
        // jQuery fallback (Bootstrap 3/4 pakai jQuery)
        $(modalKelola).on('hidden.bs.modal', function() {
            document.querySelectorAll('.chk-file-geojson').forEach(function(chk) { chk.checked = false; });
            var chkAllEl = document.getElementById('chk-select-all-geojson');
            if (chkAllEl) { chkAllEl.checked = false; chkAllEl.indeterminate = false; }
            updateBulkDeleteUI();
        });
    }
});


function updateFileInputLabel(input) {
    var label = document.getElementById('labelGeojsonFile');
    if (!label) return;
    if (input.files && input.files.length > 1) {
        label.innerText = input.files.length + ' file dipilih';
    } else if (input.files && input.files.length === 1) {
        label.innerText = input.files[0].name;
    } else {
        label.innerText = 'Pilih satu atau beberapa file...';
    }
}

// Handler Flash Message dari Batch Upload via sessionStorage
(function() {
    var flashMsg = sessionStorage.getItem('upload_flash_success');
    if (flashMsg) {
        sessionStorage.removeItem('upload_flash_success');
        var alertContainer = document.getElementById('js-flash-alert-container');
        if (alertContainer) {
            alertContainer.innerHTML = '<div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">' +
                '<i class="fas fa-check-circle mr-2"></i>' + flashMsg +
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span>' +
                '</button>' +
                '</div>';
        }
    }
})();

// Inisialisasi Batch Upload GeoJSON (Bypass limit max_file_uploads = 20 PHP)
document.addEventListener('DOMContentLoaded', function () {
    var formUpload = document.getElementById('formUploadGeojson');
    if (!formUpload) return;

    formUpload.addEventListener('submit', async function(e) {
        e.preventDefault();

        var input = document.getElementById('inputGeojsonFile');
        if (!input || !input.files || input.files.length === 0) {
            alert('Silakan pilih setidaknya satu file GeoJSON/JSON terlebih dahulu.');
            return;
        }

        var files = Array.from(input.files);
        var totalFiles = files.length;
        // Batch size 10: sangat aman di bawah limit bawaan PHP max_file_uploads = 20
        var BATCH_SIZE = 10;
        var totalBatches = Math.ceil(totalFiles / BATCH_SIZE);

        var progressSection = document.getElementById('uploadProgressSection');
        var progressBar     = document.getElementById('uploadProgressBar');
        var percentText     = document.getElementById('uploadPercentText');
        var statusText      = document.getElementById('uploadStatusText');
        var detailText      = document.getElementById('uploadDetailText');
        var batchCountText  = document.getElementById('uploadBatchCountText');
        var successSection  = document.getElementById('uploadSuccessSection');
        var errorSection    = document.getElementById('uploadErrorSection');
        var btnSubmit       = document.getElementById('btnSubmitUpload');
        var btnBatal        = document.getElementById('btnBatalUpload');
        var btnClose        = document.getElementById('btnCloseUploadModal');

        // Reset state
        if (errorSection) { errorSection.style.display = 'none'; errorSection.innerText = ''; }
        if (successSection) { successSection.style.display = 'none'; }
        if (progressSection) progressSection.style.display = 'block';
        if (btnSubmit) {
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Mengunggah...';
        }
        if (btnBatal) btnBatal.disabled = true;
        if (btnClose) btnClose.style.display = 'none';

        var totalUploaded = 0;
        var allUploadedNames = [];
        var allFailed = [];
        var csrfToken = formUpload.querySelector('input[name="_token"]')?.value || '{{ csrf_token() }}';

        try {
            for (var b = 0; b < totalBatches; b++) {
                var start = b * BATCH_SIZE;
                var end = Math.min(start + BATCH_SIZE, totalFiles);
                var batchFiles = files.slice(start, end);
                var currentBatchNum = b + 1;

                if (statusText) {
                    statusText.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Mengunggah batch ' + currentBatchNum + ' dari ' + totalBatches + '...';
                }
                if (batchCountText) {
                    batchCountText.textContent = 'Batch ' + currentBatchNum + '/' + totalBatches + ' (' + batchFiles.length + ' file)';
                }

                var formData = new FormData();
                formData.append('_token', csrfToken);
                for (var f = 0; f < batchFiles.length; f++) {
                    formData.append('file_geojson[]', batchFiles[f]);
                }

                var response = await fetch("{{ route('peta.upload-geojson') }}", {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (!response.ok) {
                    var errJson = await response.json().catch(function() { return {}; });
                    throw new Error(errJson.message || ('Gagal pada pengunggahan batch ke-' + currentBatchNum + ' (HTTP ' + response.status + ')'));
                }

                var result = await response.json();
                if (result.uploaded && result.uploaded.length > 0) {
                    allUploadedNames = allUploadedNames.concat(result.uploaded);
                    totalUploaded += result.uploaded.length;
                }
                if (result.failed && result.failed.length > 0) {
                    allFailed = allFailed.concat(result.failed);
                }

                var percent = Math.min(100, Math.round((totalUploaded / totalFiles) * 100));
                if (progressBar) {
                    progressBar.style.width = percent + '%';
                    progressBar.textContent = percent + '%';
                }
                if (percentText) percentText.textContent = percent + '%';
                if (detailText) {
                    detailText.innerHTML = '<span>' + totalUploaded + ' dari ' + totalFiles + ' file berhasil diproses...</span><span id="uploadBatchCountText">Batch ' + currentBatchNum + '/' + totalBatches + '</span>';
                }
            }

            if (progressBar) {
                progressBar.style.width = '100%';
                progressBar.textContent = '100%';
                progressBar.classList.remove('progress-bar-animated', 'progress-bar-striped');
                progressBar.classList.add('bg-success');
            }
            if (statusText) {
                statusText.innerHTML = '<i class="fas fa-check-circle text-success mr-1"></i>Pengunggahan Selesai!';
            }
            if (successSection) {
                var succMsg = 'Berhasil mengunggah total ' + totalUploaded + ' file GeoJSON!';
                if (allFailed.length > 0) {
                    succMsg += ' (' + allFailed.length + ' file dilewati karena bukan .json/.geojson)';
                }
                successSection.innerHTML = '<i class="fas fa-check-circle mr-1"></i>' + succMsg + '<br><small class="text-muted">Menyegarkan peta dalam sekejap...</small>';
                successSection.style.display = 'block';
            }

            var finalMsg = totalUploaded + ' file GeoJSON berhasil diunggah ke public/geojson (' + (allUploadedNames.slice(0, 5).join(', ') + (allUploadedNames.length > 5 ? '... dan ' + (allUploadedNames.length - 5) + ' file lainnya' : '')) + ').';
            sessionStorage.setItem('upload_flash_success', finalMsg);

            setTimeout(function() {
                window.location.reload();
            }, 1200);

        } catch (err) {
            console.error('Error saat upload batch:', err);
            if (progressBar) {
                progressBar.classList.remove('bg-success');
                progressBar.classList.add('bg-danger');
            }
            if (errorSection) {
                errorSection.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i>' + (err.message || 'Terjadi kesalahan saat mengunggah file.');
                errorSection.style.display = 'block';
            }
            if (statusText) {
                statusText.innerHTML = '<i class="fas fa-times-circle text-danger mr-1"></i>Gagal Mengunggah';
            }
            if (btnSubmit) {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '<i class="fas fa-upload mr-1"></i>Coba Upload Lagi';
            }
            if (btnBatal) btnBatal.disabled = false;
            if (btnClose) btnClose.style.display = '';
        }
    });

    // Reset form dan state progress saat modal ditutup
    $('#modalUploadGeojson').on('hidden.bs.modal', function () {
        var progressSection = document.getElementById('uploadProgressSection');
        var errorSection    = document.getElementById('uploadErrorSection');
        var successSection  = document.getElementById('uploadSuccessSection');
        var btnSubmit       = document.getElementById('btnSubmitUpload');
        var btnBatal        = document.getElementById('btnBatalUpload');
        var btnClose        = document.getElementById('btnCloseUploadModal');
        var progressBar     = document.getElementById('uploadProgressBar');
        var input           = document.getElementById('inputGeojsonFile');
        var label           = document.getElementById('labelGeojsonFile');

        if (progressSection) progressSection.style.display = 'none';
        if (errorSection) errorSection.style.display = 'none';
        if (successSection) successSection.style.display = 'none';
        if (btnSubmit) { btnSubmit.disabled = false; btnSubmit.innerHTML = '<i class="fas fa-upload mr-1"></i>Upload Sekarang'; }
        if (btnBatal) btnBatal.disabled = false;
        if (btnClose) btnClose.style.display = '';
        if (progressBar) {
            progressBar.style.width = '0%';
            progressBar.textContent = '0%';
            progressBar.className = 'progress-bar progress-bar-striped progress-bar-animated bg-success font-weight-bold';
        }
        if (input) input.value = '';
        if (label) label.innerText = 'Pilih satu atau beberapa file...';
    });
});

document.addEventListener('DOMContentLoaded', function () {

    // 1. PROJ4 DEFINITIONS
    proj4.defs("EPSG:4326",  "+proj=longlat +datum=WGS84 +no_defs");
    proj4.defs("EPSG:32649", "+proj=utm +zone=49 +datum=WGS84 +units=m +no_defs");
    proj4.defs("EPSG:32749", "+proj=utm +zone=49 +south +datum=WGS84 +units=m +no_defs");
    proj4.defs("EPSG:54034", "+proj=merc +lon_0=0 +k=1 +x_0=0 +y_0=0 +datum=WGS84 +units=m +no_defs");
    proj4.defs("EPSG:3857",  "+proj=merc +a=6378137 +b=6378137 +lat_ts=0 +lon_0=0 +x_0=0 +y_0=0 +k=1 +units=m +nadgrids=@null +wktext +no_defs");

    // 2. BASEMAPS
    var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    });
    var googleSat = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        attribution: '&copy; Google Satellite'
    });

    var map = L.map('map', {
        center: [0.55, 109.15],
        zoom: 8,
        layers: [osm],
        zoomControl: true
    });

    // 3. OVERLAY LAYER GROUPS (dinamis — hanya buat layer sesuai tipe file yang tersedia)
    var availableTypes = {!! json_encode($availableTypes) !!};
    var hasFiles = {{ $hasFiles ? 'true' : 'false' }};

    // Selalu buat semua layer group (agar referensi JS tidak error), tapi hanya tampilkan di peta jika ada file-nya
    var layerBaku       = L.layerGroup(); if (availableTypes.baku)       layerBaku.addTo(map);
    var layerFungsional = L.layerGroup(); if (availableTypes.fungsional) layerFungsional.addTo(map);
    var layerPotensial  = L.layerGroup(); if (availableTypes.potensial)  layerPotensial.addTo(map);
    var layerSaluran    = L.layerGroup(); if (availableTypes.saluran)    layerSaluran.addTo(map);
    var layerBangunan   = L.layerGroup(); if (availableTypes.bangunan)   layerBangunan.addTo(map);

    var baseMaps = {
        "OpenStreetMap": osm,
        "Satelit (Google Hybrid)": googleSat
    };

    // Overlay hanya ditambahkan jika ada file untuk tipe tersebut
    var overlayMaps = {};
    if (availableTypes.baku)       overlayMaps["Daerah Irigasi Baku (Hijau)"]       = layerBaku;
    if (availableTypes.fungsional) overlayMaps["Daerah Irigasi Fungsional (Biru)"]  = layerFungsional;
    if (availableTypes.potensial)  overlayMaps["Daerah Irigasi Potensial (Oranye)"] = layerPotensial;
    if (availableTypes.saluran)    overlayMaps["Jaringan Saluran Irigasi"]           = layerSaluran;
    if (availableTypes.bangunan)   overlayMaps["Bangunan Air / Pelengkap"]           = layerBangunan;

    // LAYER CONTROL DENGAN HEADER & TOMBOL MINIMIZE
    var layerControl = L.control.layers(baseMaps, overlayMaps, { collapsed: false, position: 'topright' }).addTo(map);
    var layerControlContainer = layerControl.getContainer();

    var layerHeader = L.DomUtil.create('div', 'custom-control-header');
    layerHeader.innerHTML = 
        '<span class="control-header-title"><i class="fas fa-layer-group text-primary mr-1"></i> Layer Peta</span>' +
        '<button type="button" class="btn-toggle-box" id="btn-toggle-layers" title="Minimize / Perluas Layer">' +
            '<i class="fas fa-chevron-up"></i>' +
        '</button>';
    layerControlContainer.insertBefore(layerHeader, layerControlContainer.firstChild);

    var btnToggleLayers = layerHeader.querySelector('#btn-toggle-layers');
    var iconToggleLayers = btnToggleLayers.querySelector('i');

    function toggleLayerBox(e) {
        if (e) {
            L.DomEvent.stopPropagation(e);
            L.DomEvent.preventDefault(e);
        }
        var isMin = layerControlContainer.classList.toggle('is-minimized');
        if (isMin) {
            iconToggleLayers.className = 'fas fa-chevron-down';
            btnToggleLayers.title = 'Buka / Perluas Layer';
        } else {
            iconToggleLayers.className = 'fas fa-chevron-up';
            btnToggleLayers.title = 'Minimize / Perkecil Layer';
        }
    }

    L.DomEvent.on(btnToggleLayers, 'click', toggleLayerBox);
    L.DomEvent.on(layerHeader, 'click', function(e) {
        if (e.target !== btnToggleLayers && !btnToggleLayers.contains(e.target)) {
            toggleLayerBox(e);
        }
    });
    L.DomEvent.disableClickPropagation(layerHeader);

    // Di layar HP (< 768px), otomatis perkecil kontrol layer agar peta terlihat lapang
    if (window.innerWidth < 768) {
        layerControlContainer.classList.add('is-minimized');
        iconToggleLayers.className = 'fas fa-chevron-down';
        btnToggleLayers.title = 'Buka / Perluas Layer';
    }

    // 4. MAP BOUNDS
    var mapBounds = L.latLngBounds();


    // 5. LEGENDA DINAMIS (hanya tampil jika ada file; item hanya untuk tipe layer yang ada)
    var legend = L.control({ position: 'bottomleft' });
    legend.onAdd = function () {
        var div = L.DomUtil.create('div', 'map-legend');

        // Bangun item legenda dinamis berdasarkan tipe file yang tersedia
        var legendItems = '';
        if (availableTypes.baku)       legendItems += '<div class="legend-item"><span class="legend-color-box" style="background:#27ae60;border-color:#1b7a43;"></span> <b>DIR Baku</b> (Hijau)</div>';
        if (availableTypes.fungsional) legendItems += '<div class="legend-item"><span class="legend-color-box" style="background:#2980b9;border-color:#1a5276;"></span> <b>DIR Fungsional</b> (Biru)</div>';
        if (availableTypes.potensial)  legendItems += '<div class="legend-item"><span class="legend-color-box" style="background:#e67e22;border-color:#b95c00;"></span> <b>DIR Potensial</b> (Oranye)</div>';
        if (availableTypes.saluran) {
            legendItems += '<div class="legend-item"><span class="legend-line" style="background:#e74c3c;"></span> <b>Saluran Primer</b> (Merah)</div>';
            legendItems += '<div class="legend-item"><span class="legend-line" style="background:#0984e3;"></span> <b>Saluran Sekunder</b> (Biru)</div>';
            legendItems += '<div class="legend-item"><span class="legend-line" style="background:#00b894;"></span> <b>Saluran Sub Sek.</b> (Teal)</div>';
        }
        if (availableTypes.bangunan)   legendItems += '<div class="legend-item"><span class="legend-circle" style="background:#d63031;"></span> <b>Bangunan Air</b> (Titik)</div>';
        if (availableTypes.custom)     legendItems += '<div class="legend-item"><span class="legend-circle" style="background:#e74c3c;border-color:#fff;"></span> <b>Data Upload</b> (Titik)</div>';

        // Jika tidak ada item legenda, sembunyikan kontrol ini
        if (!legendItems) {
            div.style.display = 'none';
            return div;
        }

        div.innerHTML =
            '<div class="custom-control-header" id="legend-header">' +
                '<span class="control-header-title"><i class="fas fa-palette text-primary mr-1"></i> Legenda Peta</span>' +
                '<button type="button" class="btn-toggle-box" id="btn-toggle-legend" title="Minimize / Perluas Legenda">' +
                    '<i class="fas fa-chevron-up"></i>' +
                '</button>' +
            '</div>' +
            '<div class="legend-content" id="legend-content-body">' +
                legendItems +
            '</div>';

        var btnToggleLegend = div.querySelector('#btn-toggle-legend');
        var iconToggleLegend = btnToggleLegend.querySelector('i');
        var legendHeader = div.querySelector('#legend-header');

        function toggleLegendBox(e) {
            if (e) {
                L.DomEvent.stopPropagation(e);
                L.DomEvent.preventDefault(e);
            }
            var isMin = div.classList.toggle('is-minimized');
            if (isMin) {
                iconToggleLegend.className = 'fas fa-chevron-down';
                btnToggleLegend.title = 'Buka / Perluas Legenda';
            } else {
                iconToggleLegend.className = 'fas fa-chevron-up';
                btnToggleLegend.title = 'Minimize / Perkecil Legenda';
            }
        }

        L.DomEvent.on(btnToggleLegend, 'click', toggleLegendBox);
        L.DomEvent.on(legendHeader, 'click', function(e) {
            if (e.target !== btnToggleLegend && !btnToggleLegend.contains(e.target)) {
                toggleLegendBox(e);
            }
        });
        L.DomEvent.disableClickPropagation(div);

        // Di layar HP (< 768px), otomatis perkecil legenda agar tidak menutupi peta
        if (window.innerWidth < 768) {
            div.classList.add('is-minimized');
            iconToggleLegend.className = 'fas fa-chevron-down';
            btnToggleLegend.title = 'Buka / Perluas Legenda';
        }

        return div;
    };
    // Legenda hanya ditambahkan ke peta jika ada file GIS yang tersedia
    if (hasFiles) legend.addTo(map);

    // 6. MARKER SENTROID DIR (hanya dari file $lokasi — kini selalu kosong, data dari GeoJSON)
    const locations = {!! json_encode($lokasi) !!};
    var dirMarkers = {};

    locations.forEach(function (loc) {
        var markerBg = '#2980b9';
        if (loc.status.toLowerCase().indexOf('kubu raya') !== -1) markerBg = '#e74c3c';
        if (loc.status.toLowerCase().indexOf('ketapang') !== -1)  markerBg = '#27ae60';

        var customIcon = L.divIcon({
            className: 'custom-dir-pin-wrapper',
            html: '<div class="custom-dir-pin" style="background:' + markerBg + ';width:28px;height:28px;font-size:12px;">' +
                  '<i class="fas fa-' + (loc.ikon || 'tint') + '"></i></div>',
            iconSize: [28, 28],
            iconAnchor: [14, 14]
        });

        var marker = L.marker([loc.lat, loc.lng], { icon: customIcon }).addTo(map);
        marker.bindPopup(
            '<div style="min-width:230px;">' +
            '<div class="d-flex align-items-center mb-2">' +
            '<span class="badge badge-' + (loc.badge || 'primary') + ' mr-2">' + (loc.kode || 'DIR') + '</span>' +
            '<h6 class="font-weight-bold mb-0 text-primary">' + loc.nama + '</h6></div>' +
            '<p class="small text-muted mb-1"><i class="fas fa-map-pin mr-1"></i>' + loc.status + '</p>' +
            '<div class="small bg-light p-2 rounded mb-2 border"><b>Koordinat GIS:</b><br>' +
            'Latitude: <code>' + loc.lat + '</code><br>Longitude: <code>' + loc.lng + '</code></div>' +
            '<small class="text-secondary">' + (loc.keterangan || '-') + '</small></div>'
        );

        mapBounds.extend([loc.lat, loc.lng]);
        if (loc.kode) dirMarkers[loc.kode] = marker;
        dirMarkers[loc.nama] = marker;
    });

    // 7. REPROYEKSI KOORDINAT
    function projectCoordinate(x, y, wkid) {
        if (typeof x !== 'number' || typeof y !== 'number' || isNaN(x) || isNaN(y)) return [x, y];
        if (wkid === 4326 || (Math.abs(x) <= 180 && Math.abs(y) <= 90 && (!wkid || wkid === 'EPSG:4326'))) return [x, y];
        try {
            var wkidStr = String(wkid || '');
            if (wkidStr.indexOf('3857') !== -1 || wkid === 3857 || wkid === 102100 || wkid === 900913) {
                return proj4('EPSG:3857', 'EPSG:4326', [x, y]);
            }
            if (wkidStr.indexOf('54034') !== -1 || wkid === 54034) return proj4('EPSG:54034', 'EPSG:4326', [x, y]);
            if (wkidStr.indexOf('32749') !== -1 || wkid === 32749) return proj4('EPSG:32749', 'EPSG:4326', [x, y]);
            if (wkidStr.indexOf('32649') !== -1 || wkid === 32649) return proj4('EPSG:32649', 'EPSG:4326', [x, y]);
            if (wkid && proj4.defs['EPSG:' + wkid])                  return proj4('EPSG:' + wkid, 'EPSG:4326', [x, y]);
            if (wkid && proj4.defs[wkid])                            return proj4(wkid, 'EPSG:4326', [x, y]);

            // Auto deteksi koordinat mercator / utm
            if (Math.abs(x) > 2000000 || Math.abs(y) > 2000000) {
                return proj4('EPSG:3857', 'EPSG:4326', [x, y]);
            }
            if (y > 2000000) return proj4('EPSG:32749', 'EPSG:4326', [x, y]);
            if (x > 100000)  return proj4('EPSG:32649', 'EPSG:4326', [x, y]);
            return [x, y];
        } catch(e) { console.warn('Reproj err', x, y, e); return [x, y]; }
    }

    function transformGeoJsonCoords(coords, geomType, wkid) {
        if (!coords) return coords;
        if (geomType === 'Point') {
            return projectCoordinate(coords[0], coords[1], wkid);
        } else if (geomType === 'LineString' || geomType === 'MultiPoint') {
            return coords.map(function(c) { return projectCoordinate(c[0], c[1], wkid); });
        } else if (geomType === 'Polygon' || geomType === 'MultiLineString') {
            return coords.map(function(ring) {
                return ring.map(function(c) { return projectCoordinate(c[0], c[1], wkid); });
            });
        } else if (geomType === 'MultiPolygon') {
            return coords.map(function(poly) {
                return poly.map(function(ring) {
                    return ring.map(function(c) { return projectCoordinate(c[0], c[1], wkid); });
                });
            });
        }
        return coords;
    }

    // 8. KONVERSI ESRI & NORMALISASI GEOJSON KE WGS84
    function convertEsriToGeoJson(data) {
        if (!data) return null;

        var wkid = null;
        if (data.crs && data.crs.properties && data.crs.properties.name) {
            wkid = String(data.crs.properties.name);
        } else if (data.spatialReference) {
            wkid = data.spatialReference.wkid || data.spatialReference.latestWkid;
        }

        // Jika data adalah GeoJSON standar (FeatureCollection / Feature), transform koordinatnya ke WGS84
        if (data.type === 'FeatureCollection' && Array.isArray(data.features)) {
            data.features.forEach(function(f) {
                if (f.geometry && f.geometry.coordinates) {
                    f.geometry.coordinates = transformGeoJsonCoords(f.geometry.coordinates, f.geometry.type, wkid);
                }
            });
            return data;
        }
        if (data.type === 'Feature' && data.geometry && data.geometry.coordinates) {
            data.geometry.coordinates = transformGeoJsonCoords(data.geometry.coordinates, data.geometry.type, wkid);
            return data;
        }

        // Jika data adalah Esri JSON
        if (!data.features || !Array.isArray(data.features)) return null;

        var geomType = data.geometryType;
        var gj = { type: 'FeatureCollection', features: [] };

        data.features.forEach(function(f, idx) {
            if (!f.geometry) return;
            var geom = null;
            if (geomType === 'esriGeometryPoint' || (f.geometry.x !== undefined && f.geometry.y !== undefined)) {
                var pt = projectCoordinate(f.geometry.x, f.geometry.y, wkid);
                geom = { type: 'Point', coordinates: [pt[0], pt[1]] };
            } else if (geomType === 'esriGeometryPolyline' || f.geometry.paths) {
                var paths = f.geometry.paths || [];
                if (paths.length === 1) {
                    geom = { type: 'LineString', coordinates: paths[0].map(function(c){ return projectCoordinate(c[0],c[1],wkid); }) };
                } else if (paths.length > 1) {
                    geom = { type: 'MultiLineString', coordinates: paths.map(function(p){ return p.map(function(c){ return projectCoordinate(c[0],c[1],wkid); }); }) };
                }
            } else if (geomType === 'esriGeometryPolygon' || f.geometry.rings) {
                var rings = f.geometry.rings || [];
                if (rings.length > 0) {
                    geom = { type: 'Polygon', coordinates: rings.map(function(r){ return r.map(function(c){ return projectCoordinate(c[0],c[1],wkid); }); }) };
                }
            }
            if (geom) {
                gj.features.push({
                    type: 'Feature',
                    id: f.attributes ? (f.attributes.FID !== undefined ? f.attributes.FID : (f.attributes.OID_ !== undefined ? f.attributes.OID_ : idx)) : idx,
                    properties: f.attributes || {},
                    geometry: geom
                });
            }
        });
        return gj;
    }

    // 9. FETCH & RENDER GEOJSON FILES
    var geojsonFiles = {!! json_encode($geojsonFiles ?? []) !!};
    var loadingEl   = document.getElementById('map-loading');
    var infoFiturEl = document.getElementById('info-fitur-termuat');
    var customUploadLayers = {}; // Koleksi layer grup khusus untuk file yang di-upload

    // Data penampung baris tabel koordinat
    var fileTableRows = [];      // 1 baris per file GIS yang diunggah
    var objectTableRows = [];    // 1 baris per objek/fitur individu di dalam file
    var currentTabelMode = 'file'; // default: 'file' (Ringkasan per file)
    var currentSearchQuery = '';

    if (geojsonFiles.length > 0) {
        var totalFiles      = geojsonFiles.length;
        var loadedCount     = 0;
        var totalFiturCount = 0;

        geojsonFiles.forEach(function(fileName) {
            fetch('/geojson/' + fileName)
                .then(function(r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                .then(function(rawData) {
                    var gj = convertEsriToGeoJson(rawData);
                    if (!gj || !gj.features || gj.features.length === 0) return;

                    var featCount = gj.features.length;
                    totalFiturCount += featCount;

                    var nameUpper   = fileName.toUpperCase();
                    var cleanName   = fileName.replace(/\.(json|geojson)$/i, '').replace(/_/g, ' ');
                    var targetLayer = null;
                    var isCustomUpload = false;
                    var strokeColor = '#27ae60';
                    var fillColor   = '#2ecc71';
                    var fillOpacity = 0.45;
                    var weight      = 1.8;
                    var catBadge    = '<span class="badge badge-secondary"><i class="fas fa-file-code mr-1"></i>GIS</span>';
                    var catName     = 'Data GIS';
                    var rowClass    = 'row-custom';

                    if (nameUpper.indexOf('BAKU') === 0) {
                        targetLayer = layerBaku; strokeColor = '#1e824c'; fillColor = '#27ae60'; fillOpacity = 0.45;
                        catBadge = '<span class="badge badge-success"><i class="fas fa-seedling mr-1"></i>DIR Baku</span>';
                        catName  = 'Daerah Irigasi Baku';
                        rowClass = 'row-baku';
                    } else if (nameUpper.indexOf('FUNGSIONAL') === 0) {
                        targetLayer = layerFungsional; strokeColor = '#1f618d'; fillColor = '#2980b9'; fillOpacity = 0.45;
                        catBadge = '<span class="badge badge-primary"><i class="fas fa-water mr-1"></i>DIR Fungsional</span>';
                        catName  = 'DIR Fungsional';
                        rowClass = 'row-fungsional';
                    } else if (nameUpper.indexOf('POTENSIAL') === 0) {
                        targetLayer = layerPotensial; strokeColor = '#b95c00'; fillColor = '#e67e22'; fillOpacity = 0.40;
                        catBadge = '<span class="badge badge-warning text-dark"><i class="fas fa-sun mr-1"></i>DIR Potensial</span>';
                        catName  = 'DIR Potensial';
                        rowClass = 'row-potensial';
                    } else if (nameUpper.indexOf('SALURAN') === 0) {
                        targetLayer = layerSaluran; strokeColor = '#0984e3'; weight = 2.2;
                        catBadge = '<span class="badge badge-info"><i class="fas fa-project-diagram mr-1"></i>Saluran</span>';
                        catName  = 'Jaringan Saluran';
                        rowClass = 'row-saluran';
                    } else if (nameUpper.indexOf('BANGUNAN') === 0) {
                        targetLayer = layerBangunan; strokeColor = '#c0392b';
                        catBadge = '<span class="badge badge-danger"><i class="fas fa-map-pin mr-1"></i>Bangunan</span>';
                        catName  = 'Bangunan Air';
                        rowClass = 'row-bangunan';
                    } else {
                        // File Kustom / Upload baru
                        isCustomUpload = true;
                        if (!customUploadLayers[fileName]) {
                            customUploadLayers[fileName] = L.layerGroup().addTo(map);
                            layerControl.addOverlay(customUploadLayers[fileName], '<i class="fas fa-layer-group text-danger mr-1"></i> ' + cleanName);
                        }
                        targetLayer = customUploadLayers[fileName];
                        strokeColor = '#e74c3c'; fillColor = '#e74c3c'; fillOpacity = 0.6; weight = 2.2;
                        catBadge = '<span class="badge badge-warning text-dark"><i class="fas fa-cloud-upload-alt mr-1"></i>Upload</span>';
                        catName  = 'Data Kustom';
                        rowClass = 'row-uploaded';
                    }

                    var group = L.geoJSON(gj, {
                        style: function(feature) {
                            var lc = strokeColor, lw = weight;
                            if (feature.properties && (feature.properties.Jenis || feature.properties.JENIS)) {
                                var j = String(feature.properties.Jenis || feature.properties.JENIS).toLowerCase();
                                if      (j.indexOf('primer')   !== -1) { lc = '#e74c3c'; lw = 2.8; }
                                else if (j.indexOf('sekunder') !== -1) { lc = '#0984e3'; lw = 2.2; }
                                else if (j.indexOf('sub')      !== -1) { lc = '#00b894'; lw = 1.8; }
                            }
                            return { color: lc, weight: lw, opacity: 0.9, fillColor: fillColor, fillOpacity: fillOpacity };
                        },
                        pointToLayer: function(feature, latlng) {
                            if (isCustomUpload) {
                                return L.circleMarker(latlng, { radius: 7, fillColor: '#e74c3c', color: '#ffffff', weight: 2, opacity: 1, fillOpacity: 0.95 });
                            }
                            return L.circleMarker(latlng, { radius: 3.5, fillColor: '#e74c3c', color: '#ffffff', weight: 1, opacity: 1, fillOpacity: 0.95 });
                        },
                        onEachFeature: function(feature, layer) {
                            if (feature.properties) {
                                var props = feature.properties;
                                var title = props.NAMOBJ || props.Name || props.Nomenklatu || props.NomenKlatu || props.D_I_R || props.D_I || cleanName;

                                // Tooltip titik
                                if (feature.geometry && feature.geometry.type === 'Point') {
                                    layer.bindTooltip('<b>' + title + '</b>' + (props.REMARK ? ' <span class="badge badge-light border">' + props.REMARK + '</span>' : ''), {
                                        direction: 'top',
                                        offset: [0, -7]
                                    });
                                }

                                var objLat = null;
                                var objLng = null;
                                if (layer.getLatLng) {
                                    var ll = layer.getLatLng();
                                    objLat = ll.lat; objLng = ll.lng;
                                } else if (feature.geometry && feature.geometry.type === 'Point' && feature.geometry.coordinates) {
                                    objLng = feature.geometry.coordinates[0]; objLat = feature.geometry.coordinates[1];
                                } else if (layer.getBounds && layer.getBounds().isValid()) {
                                    var c = layer.getBounds().getCenter();
                                    objLat = c.lat; objLng = c.lng;
                                }

                                var latText = (objLat !== null) ? objLat.toFixed(6) : null;
                                var lngText = (objLng !== null) ? objLng.toFixed(6) : null;

                                var content = '<div style="max-width:300px;max-height:280px;overflow-y:auto;">' +
                                    '<h6 class="font-weight-bold text-primary mb-1 border-bottom pb-1">' +
                                    '<i class="fas fa-map-marker-alt text-danger mr-1"></i> ' + title + '</h6>';

                                if (latText && lngText) {
                                    content += '<div class="small bg-light p-1 px-2 rounded mb-2 border text-muted">' +
                                        '<b>Titik Koordinat:</b> Lat <code>' + latText + '</code>, Lng <code>' + lngText + '</code></div>';
                                }

                                content += '<table class="table table-sm table-striped gis-popup-table mb-1"><tbody>';
                                for (var key in props) {
                                    if (props[key] !== null && props[key] !== undefined && String(props[key]).trim() !== '' && key !== 'FID' && key !== 'OID_') {
                                        content += '<tr><th class="text-muted" style="width:42%;font-size:11px;">' + key + '</th><td style="font-size:11px;"><b>' + props[key] + '</b></td></tr>';
                                    }
                                }
                                content += '</tbody></table>' +
                                    '<div class="text-right"><small class="text-secondary" style="font-size:10px;"><i class="fas fa-file-code mr-1"></i>Sumber: ' + fileName + '</small></div>' +
                                    '</div>';

                                layer.bindPopup(content);

                                // Tambahkan ke daftar Rincian Semua Objek
                                if (objLat !== null && objLng !== null && !isNaN(objLat) && !isNaN(objLng)) {
                                    objectTableRows.push({
                                        nama: title,
                                        kode: (props.FID !== undefined ? 'FID:' + props.FID : (props.OID_ !== undefined ? 'OID:' + props.OID_ : '')),
                                        catBadge: catBadge,
                                        wilayah: (props.KABUPATEN || props.Kabupaten || props.Kecamatan || props.Desa || props.REMARK || catName),
                                        lat: objLat,
                                        lng: objLng,
                                        fileName: fileName,
                                        layerObj: layer,
                                        bounds: (layer.getBounds && layer.getBounds().isValid()) ? layer.getBounds() : null,
                                        rowClass: rowClass
                                    });
                                }
                            }
                        }
                    });

                    group.addTo(targetLayer);

                    // Ambil batas spasial dan titik sentroid file
                    var grpBounds = null;
                    var fileCenterLat = null;
                    var fileCenterLng = null;
                    try {
                        if (group.getBounds && group.getBounds().isValid()) {
                            grpBounds = group.getBounds();
                            mapBounds.extend(grpBounds);
                            var grpCenter = grpBounds.getCenter();
                            fileCenterLat = grpCenter.lat;
                            fileCenterLng = grpCenter.lng;
                            if (isCustomUpload) {
                                if (!window.customUploadBounds) window.customUploadBounds = L.latLngBounds();
                                window.customUploadBounds.extend(grpBounds);
                            }
                        }
                    } catch(e) {}

                    // Ekstrak nama utama dan wilayah file untuk baris ringkasan per file
                    var firstProps = (gj.features && gj.features[0] && gj.features[0].properties) ? gj.features[0].properties : {};
                    var fileTitle = firstProps.D_I_R || firstProps.NAMOBJ || firstProps.Name || cleanName;
                    var fileWilayah = firstProps.KABUPATEN || firstProps.Kabupaten || firstProps.Kecamatan || firstProps.REMARK || catName;

                    fileTableRows.push({
                        nama: fileTitle,
                        subInfo: featCount > 1 ? (featCount + ' Objek') : (featCount + ' Fitur'),
                        catBadge: catBadge,
                        wilayah: fileWilayah,
                        lat: fileCenterLat,
                        lng: fileCenterLng,
                        fileName: fileName,
                        bounds: grpBounds,
                        layerObj: group,
                        rowClass: rowClass
                    });
                })
                .catch(function(err) { console.warn('Gagal memuat [' + fileName + ']:', err); })
                .finally(function() {
                    loadedCount++;
                    if (loadedCount >= totalFiles) {
                        if (loadingEl) loadingEl.style.display = 'none';
                        if (infoFiturEl) infoFiturEl.innerHTML = '<i class="fas fa-check-circle text-success mr-1"></i>' + totalFiles + ' File GIS &amp; ' + totalFiturCount.toLocaleString() + ' Objek ArcMap Siap';
                        if (mapBounds.isValid()) map.fitBounds(mapBounds, { padding: [30, 30] });

                        // Update counter tombol mode
                        var countFileEl = document.getElementById('count-mode-file');
                        var countObjekEl = document.getElementById('count-mode-objek');
                        if (countFileEl) countFileEl.textContent = fileTableRows.length;
                        if (countObjekEl) countObjekEl.textContent = objectTableRows.length;

                        // Render tabel koordinat otomatis
                        renderCoordinateTable();
                    }
                });
        });
    } else {
        if (loadingEl) loadingEl.style.display = 'none';
        if (infoFiturEl) infoFiturEl.innerHTML = '<i class="fas fa-info-circle text-warning mr-1"></i>Tidak ada file GeoJSON di folder public/geojson';
        if (mapBounds.isValid()) map.fitBounds(mapBounds, { padding: [50, 50] });
        renderCoordinateTable();
    }

    // FUNGSI GANTI MODE TAMPILAN TABEL: Ringkasan File vs Rincian Semua Objek
    window.setTabelMode = function(mode) {
        currentTabelMode = mode;
        var btnFile  = document.getElementById('btn-mode-file');
        var btnObjek = document.getElementById('btn-mode-objek');
        if (btnFile && btnObjek) {
            if (mode === 'file') {
                btnFile.classList.add('active', 'btn-primary');
                btnFile.classList.remove('btn-outline-primary');
                btnObjek.classList.remove('active', 'btn-primary');
                btnObjek.classList.add('btn-outline-primary');
            } else {
                btnObjek.classList.add('active', 'btn-primary');
                btnObjek.classList.remove('btn-outline-primary');
                btnFile.classList.remove('active', 'btn-primary');
                btnFile.classList.add('btn-outline-primary');
            }
        }
        renderCoordinateTable();
    };

    // FUNGSI PENCARIAN CEPAT DI TABEL KOORDINAT
    window.filterTabelKoordinat = function(val) {
        currentSearchQuery = (val || '').toLowerCase().trim();
        renderCoordinateTable();
    };

    // FUNGSI UTAMA RENDER TABEL KOORDINAT & DATA SPASIAL
    function renderCoordinateTable() {
        var tbody = document.getElementById('tbody-lokasi');
        var footerInfo = document.getElementById('tabel-footer-info');
        if (!tbody) return;

        var sourceData = (currentTabelMode === 'file') ? fileTableRows : objectTableRows;
        var filteredData = sourceData;

        if (currentSearchQuery !== '') {
            filteredData = sourceData.filter(function(item) {
                var namaMatch = item.nama && item.nama.toLowerCase().indexOf(currentSearchQuery) !== -1;
                var fileMatch = item.fileName && item.fileName.toLowerCase().indexOf(currentSearchQuery) !== -1;
                var wilMatch  = item.wilayah && item.wilayah.toLowerCase().indexOf(currentSearchQuery) !== -1;
                return namaMatch || fileMatch || wilMatch;
            });
        }

        if (footerInfo) {
            footerInfo.innerHTML = 'Menampilkan <b>' + filteredData.length + '</b> dari <b>' + sourceData.length + '</b> ' +
                (currentTabelMode === 'file' ? 'file GIS' : 'objek koordinat');
        }

        if (filteredData.length === 0) {
            var msgEmpty = (sourceData.length === 0)
                ? 'Belum ada data GIS yang dimuat. Silakan upload file GeoJSON.'
                : 'Tidak ditemukan data yang cocok dengan pencarian "' + currentSearchQuery + '".';
            tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted"><i class="fas fa-info-circle mr-2"></i>' + msgEmpty + '</td></tr>';
            return;
        }

        var frag = document.createDocumentFragment();

        filteredData.forEach(function(item, idx) {
            var tr = document.createElement('tr');
            tr.className = 'dir-row ' + (item.rowClass || '');

            var latStr = (item.lat !== null && item.lat !== undefined && !isNaN(item.lat)) ? Number(item.lat).toFixed(5) : '-';
            var lngStr = (item.lng !== null && item.lng !== undefined && !isNaN(item.lng)) ? Number(item.lng).toFixed(5) : '-';

            tr.innerHTML = 
                '<td class="text-center font-weight-bold text-muted align-middle">' + (idx + 1) + '</td>' +
                '<td class="align-middle">' +
                    '<span class="font-weight-bold text-gray-800">' + item.nama + '</span>' +
                    (item.kode ? ' <span class="badge badge-secondary ml-1 small">' + item.kode + '</span>' : '') +
                    (item.subInfo ? ' <span class="badge badge-light border text-muted ml-1 small">' + item.subInfo + '</span>' : '') +
                '</td>' +
                '<td class="align-middle">' +
                    (item.catBadge || '') +
                    ' <span class="badge badge-light border text-dark ml-1">' + item.wilayah + '</span>' +
                '</td>' +
                '<td class="text-center align-middle font-weight-bold text-dark"><code>' + latStr + '</code></td>' +
                '<td class="text-center align-middle font-weight-bold text-dark"><code>' + lngStr + '</code></td>' +
                '<td class="align-middle">' +
                    '<span class="badge badge-light border text-monospace text-dark text-truncate d-inline-block" style="max-width:260px;" title="' + item.fileName + '">' +
                        '<i class="fas fa-file-code text-info mr-1"></i>' + item.fileName +
                    '</span>' +
                '</td>' +
                '<td class="text-center align-middle">' +
                    '<button type="button" class="btn btn-sm btn-outline-primary btn-fokus-action py-1 px-2 font-weight-bold shadow-sm" title="Fokuskan peta ke lokasi ini">' +
                        '<i class="fas fa-crosshairs mr-1"></i>Fokus' +
                    '</button>' +
                '</td>';

            var btnFokus = tr.querySelector('.btn-fokus-action');
            btnFokus.addEventListener('click', function() {
                if (item.bounds && item.bounds.isValid && item.bounds.isValid()) {
                    map.fitBounds(item.bounds, { padding: [50, 50], maxZoom: 16 });
                } else if (item.lat && item.lng) {
                    map.flyTo([item.lat, item.lng], 15, { animate: true, duration: 1.2 });
                }
                if (item.layerObj) {
                    if (item.layerObj.openPopup) {
                        item.layerObj.openPopup();
                    } else if (item.layerObj.eachLayer) {
                        item.layerObj.eachLayer(function(l) { if (l.openPopup) { l.openPopup(); return; } });
                    }
                }
                var mapContainer = document.getElementById('map');
                if (mapContainer) {
                    window.scrollTo({ top: mapContainer.offsetTop - 70, behavior: 'smooth' });
                }
            });

            frag.appendChild(tr);
        });

        tbody.innerHTML = '';
        tbody.appendChild(frag);
    }

    // 10. TOMBOL FOKUS & FILTER WILAYAH
    document.querySelectorAll('.btn-lokasi').forEach(function(button) {
        button.addEventListener('click', function() {
            var lat  = parseFloat(this.getAttribute('data-lat'));
            var lng  = parseFloat(this.getAttribute('data-lng'));
            var nama = this.getAttribute('data-nama');
            var kode = this.getAttribute('data-kode');
            map.flyTo([lat, lng], 13, { animate: true, duration: 1.2 });
            var m = dirMarkers[kode] || dirMarkers[nama];
            if (m) m.openPopup();
            window.scrollTo({ top: document.getElementById('map').offsetTop - 70, behavior: 'smooth' });
        });
    });

    document.querySelectorAll('#btn-filter-wilayah button').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('#btn-filter-wilayah button').forEach(function(b) { b.classList.remove('active'); });
            this.classList.add('active');
            var filter = this.getAttribute('data-filter');
            document.querySelectorAll('.dir-row').forEach(function(r) {
                if (filter === 'all') {
                    r.style.display = '';
                } else if (filter === 'uploaded') {
                    r.style.display = r.classList.contains('row-uploaded') ? '' : 'none';
                } else {
                    r.style.display = r.classList.contains('row-' + filter) ? '' : 'none';
                }
            });
            if      (filter === 'all')      { if (mapBounds.isValid()) map.fitBounds(mapBounds, { padding: [30,30] }); }
            else if (filter === 'uploaded') { if (window.customUploadBounds && window.customUploadBounds.isValid()) map.fitBounds(window.customUploadBounds, { padding: [60,60], maxZoom: 14 }); }
            else if (filter === 'sambas')   { map.flyTo([1.35,  109.15], 10, { animate: true, duration: 1 }); }
            else if (filter === 'kuburaya') { map.flyTo([-0.50, 109.55], 10, { animate: true, duration: 1 }); }
            else if (filter === 'ketapang') { map.flyTo([-1.90, 110.05], 10, { animate: true, duration: 1 }); }
        });
    });

    // Otomatis sinkronkan ukuran peta saat pergantian pilihan HP di browser / rotasi layar
    window.addEventListener('resize', function() {
        if (map) {
            setTimeout(function() {
                map.invalidateSize();
            }, 150);
        }
    });
    window.addEventListener('orientationchange', function() {
        if (map) {
            setTimeout(function() {
                map.invalidateSize();
            }, 250);
        }
    });

});
</script>
@endpush
