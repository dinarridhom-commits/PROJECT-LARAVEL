@extends('layout.master')

@section('title', 'Master Data — Infrastruktur Irigasi')

@section('konten')
<div class="container-fluid">

    {{-- ============================================================ --}}
    {{-- FLASH NOTIFIKASI --}}
    {{-- ============================================================ --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" style="border-radius: 10px; background: #ecfdf5; color: #065f46; border-left: 4px solid #10b981 !important;">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle fa-lg mr-2.5 text-success"></i>
                <div class="font-weight-medium" style="font-size: 13.5px;">{{ session('success') }}</div>
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" style="border-radius: 10px; background: #fef2f2; color: #991b1b; border-left: 4px solid #ef4444 !important;">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle fa-lg mr-2.5 text-danger"></i>
                <div class="font-weight-medium" style="font-size: 13.5px;">{{ session('error') }}</div>
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- HEADER HALAMAN & TOMBOL AKSI UTAMA --}}
    {{-- ============================================================ --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap" style="gap: 12px;">
        <div>
            <h1 class="h3 font-weight-bold text-gray-800 mb-1" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                <i class="fas fa-database text-primary mr-2"></i>
                Master Data Infrastruktur Irigasi
            </h1>
            <p class="text-muted small mb-0" style="font-family: 'Inter', sans-serif;">
                Katalog dan pengelolaan seluruh berkas spasial GeoJSON BWS Kalimantan I yang telah dipublikasikan ke peta interaktif.
            </p>
        </div>

        <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
            {{-- Tombol Unggah Berkas Baru --}}
            <button type="button" class="bws-btn-top bws-btn-top-primary"
                    data-toggle="modal" data-target="#modalUploadGisMaster">
                <i class="fas fa-cloud-upload-alt mr-2"></i>
                <span>Unggah Berkas Baru</span>
            </button>

            {{-- Tombol Arsip Terhapus --}}
            @if(Auth::check() && Auth::user()->role === 'superadmin')
            <a href="{{ route('gis.trash') }}" class="bws-btn-top bws-btn-top-archive" title="Lihat berkas spasial yang dihapus">
                <i class="fas fa-archive mr-2 text-danger"></i>
                <span>Arsip Terhapus</span>
            </a>
            @endif

            {{-- Tombol Buka Peta Spasial --}}
            <a href="{{ route('peta') }}" class="bws-btn-top bws-btn-top-map" title="Buka tampilan peta interaktif" target="_blank">
                <i class="fas fa-map-marked-alt mr-2 text-primary"></i>
                <span>Buka Peta</span>
            </a>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- KPI CARDS STATISTIK REALTIME PER KATEGORI --}}
    {{-- ============================================================ --}}
    <div class="row mb-4">
        {{-- Total Semua Data --}}
        <div class="col-6 col-md-2 mb-3">
            <a href="{{ route('master-data.infrastruktur') }}"
               class="md-kpi-card md-kpi-total {{ $categoryFilter === 'all' ? 'md-kpi-active' : '' }} h-100 text-decoration-none d-block">
                <div class="md-kpi-icon-wrap md-kpi-icon-total">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="md-kpi-value">{{ $totalApproved }}</div>
                <div class="md-kpi-label">Semua Kategori</div>
            </a>
        </div>

        @php
            $catDefs = [
                ['key' => 'baku',       'label' => 'DIR Baku',        'icon' => 'fa-map',      'dot' => '#27ae60'],
                ['key' => 'fungsional', 'label' => 'DIR Fungsional',  'icon' => 'fa-water',    'dot' => '#2980b9'],
                ['key' => 'potensial',  'label' => 'DIR Potensial',   'icon' => 'fa-seedling', 'dot' => '#e67e22'],
                ['key' => 'saluran',    'label' => 'Jaringan Saluran','icon' => 'fa-route',   'dot' => '#0984e3'],
                ['key' => 'bangunan',   'label' => 'Bangunan Air',    'icon' => 'fa-building', 'dot' => '#d63031'],
            ];
        @endphp

        @foreach($catDefs as $cat)
        <div class="col-6 col-md-2 mb-3">
            <a href="{{ route('master-data.infrastruktur', ['category' => $cat['key']]) }}"
               class="md-kpi-card h-100 text-decoration-none d-block {{ $categoryFilter === $cat['key'] ? 'md-kpi-active' : '' }}"
               style="border-top: 3px solid {{ $cat['dot'] }};">
                <div class="md-kpi-icon-wrap" style="background: {{ $cat['dot'] }}18; color: {{ $cat['dot'] }};">
                    <i class="fas {{ $cat['icon'] }}"></i>
                </div>
                <div class="md-kpi-value">{{ $stats[$cat['key']] ?? 0 }}</div>
                <div class="md-kpi-label">{{ $cat['label'] }}</div>
            </a>
        </div>
        @endforeach
    </div>

    {{-- ============================================================ --}}
    {{-- TABEL DATA SPASIAL INFRASTRUKTUR --}}
    {{-- ============================================================ --}}
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header bg-white py-3" style="border-bottom: 1px solid #f1f5f9;">
            <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap: 12px;">
                <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                    <h6 class="m-0 font-weight-bold text-dark" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                        <i class="fas fa-table text-primary mr-2"></i>
                        Daftar Berkas Spasial
                    </h6>
                    @if($categoryFilter !== 'all')
                        <span class="md-active-filter-chip">
                            <i class="fas fa-filter mr-1"></i>
                            {{ $categoryLabels[$categoryFilter] ?? ucfirst($categoryFilter) }}
                        </span>
                    @endif
                    @if(!empty($search))
                        <span class="badge badge-light border text-muted px-2 py-1" style="font-size: 11px;">
                            Pencarian: "<strong>{{ $search }}</strong>"
                        </span>
                    @endif
                </div>

                {{-- Form Pencarian Data --}}
                <form method="GET" action="{{ route('master-data.infrastruktur') }}" class="form-inline m-0">
                    @if($categoryFilter !== 'all')
                        <input type="hidden" name="category" value="{{ $categoryFilter }}">
                    @endif
                    <div class="input-group input-group-sm" style="width: 260px;">
                        <input type="text" name="search" class="form-control"
                               placeholder="Cari nama objek / file..."
                               value="{{ $search ?? '' }}"
                               style="border-radius: 8px 0 0 8px; font-size: 12.5px;">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit" title="Cari data" style="border-radius: {{ empty($search) ? '0 8px 8px 0' : '0' }};">
                                <i class="fas fa-search"></i>
                            </button>
                            @if(!empty($search))
                                <a href="{{ route('master-data.infrastruktur', ['category' => $categoryFilter]) }}"
                                   class="btn btn-outline-secondary" title="Hapus pencarian" style="border-radius: 0 8px 8px 0;">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                    <thead class="bg-light text-muted small text-uppercase" style="letter-spacing: 0.5px;">
                        <tr>
                            <th class="pl-4 py-3 text-center" width="4%">No.</th>
                            <th class="py-3" width="28%">Nama Objek Spasial</th>
                            <th class="py-3" width="18%">Kategori</th>
                            <th class="py-3" width="13%">Ukuran</th>
                            <th class="py-3" width="15%">Pengunggah</th>
                            <th class="py-3" width="12%">Waktu Unggah</th>
                            <th class="py-3 pr-4 text-center" width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($layers as $index => $layer)
                        <tr>
                            {{-- Nomor Urut --}}
                            <td class="pl-4 text-center text-muted font-weight-bold align-middle">
                                {{ $layers->firstItem() + $index }}
                            </td>

                            {{-- Nama Objek & Berkas --}}
                            <td class="align-middle">
                                <div class="d-flex align-items-start">
                                    <div class="md-file-icon mr-2.5">
                                        <i class="fas fa-file-code"></i>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold text-dark mb-0.5" style="font-size: 13.5px;">
                                            {{ $layer->nama_objek }}
                                        </div>
                                        <span class="text-muted d-block font-monospace" style="font-size: 11px;">
                                            {{ $layer->filename }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            {{-- Kategori --}}
                            <td class="align-middle">
                                @php
                                    $catKey = strtolower($layer->category);
                                    $dotColor = $categoryColors[$catKey] ?? '#64748b';
                                    $displayLabel = $categoryLabels[$catKey] ?? ucfirst($layer->category);
                                @endphp
                                <span class="md-category-chip" style="border-color: {{ $dotColor }}30; color: {{ $dotColor }}; background: {{ $dotColor }}15;">
                                    <span class="md-cat-dot" style="background: {{ $dotColor }};"></span>
                                    {{ $displayLabel }}
                                </span>
                            </td>

                            {{-- Ukuran Berkas --}}
                            <td class="align-middle">
                                <span class="badge badge-light border text-muted px-2 py-1 font-weight-normal" style="font-size: 11px;">
                                    {{ $layer->file_size ?? '-' }}
                                </span>
                            </td>

                            {{-- Pengunggah & Verifikator --}}
                            <td class="align-middle">
                                <div class="font-weight-medium text-dark">
                                    {{ $layer->user ? $layer->user->name : 'Staf GIS' }}
                                </div>
                                <small class="text-muted d-block">
                                    Verif: {{ $layer->reviewer ? $layer->reviewer->name : 'Super Admin' }}
                                </small>
                            </td>

                            {{-- Waktu --}}
                            <td class="align-middle text-muted small">
                                <div><i class="fas fa-calendar-alt text-gray-400 mr-1"></i>{{ $layer->created_at ? $layer->created_at->format('d M Y') : '-' }}</div>
                                <small class="text-gray-500">{{ $layer->created_at ? $layer->created_at->format('H:i') : '' }}</small>
                            </td>

                            {{-- Aksi --}}
                            <td class="pr-4 text-center align-middle">
                                <div class="d-flex align-items-center justify-content-center" style="gap: 6px;">
                                    {{-- Unduh Berkas --}}
                                    <a href="{{ route('peta.download-file', $layer->filename) }}"
                                       class="bws-action-btn bws-act-download"
                                       title="Unduh Berkas GeoJSON ({{ $layer->filename }})">
                                        <i class="fas fa-download"></i>
                                    </a>

                                    {{-- Buka di Peta Spasial --}}
                                    <a href="{{ route('peta', ['layer' => $layer->category]) }}"
                                       class="bws-action-btn bws-act-map"
                                       title="Buka layer di peta interaktif"
                                       target="_blank">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>

                                    {{-- Hapus (Pindahkan ke Arsip Terhapus) --}}
                                    @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'superadmin']))
                                    <button type="button"
                                            class="bws-action-btn bws-act-delete"
                                            title="Pindahkan ke Arsip Terhapus"
                                            onclick="confirmDeleteGis('{{ $layer->filename }}', '{{ addslashes($layer->nama_objek) }}')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="p-0 border-0">
                                <div class="text-center py-5 text-muted">
                                    <div style="width: 60px; height: 60px; border-radius: 50%; background: #f1f5f9; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px; color: #94a3b8; font-size: 24px;">
                                        <i class="fas fa-folder-open"></i>
                                    </div>
                                    <h6 class="font-weight-bold text-dark mb-1">Tidak Ada Berkas Ditemukan</h6>
                                    <p class="text-muted small mb-3">
                                        @if(!empty($search))
                                            Tidak ada berkas spasial yang cocok dengan kata kunci "<strong>{{ $search }}</strong>".
                                        @else
                                            Belum ada berkas spasial untuk kategori ini.
                                        @endif
                                    </p>
                                    <button type="button" class="btn btn-sm btn-primary font-weight-bold px-3" data-toggle="modal" data-target="#modalUploadGisMaster">
                                        <i class="fas fa-cloud-upload-alt mr-1.5"></i> Unggah Berkas Baru
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination Footer --}}
        @if($layers->hasPages() || $layers->total() > 0)
        <div class="card-footer bg-white py-3 d-flex justify-content-between align-items-center flex-wrap"
             style="border-top: 1px solid #eaecf0; border-radius: 0 0 12px 12px; gap: 10px;">
            <small class="text-muted">
                Menampilkan <strong>{{ $layers->firstItem() ?? 0 }}</strong> &ndash; <strong>{{ $layers->lastItem() ?? 0 }}</strong>
                dari total <strong>{{ number_format($layers->total()) }}</strong> berkas spasial
            </small>

            @if($layers->hasPages())
            <div class="bwsmd-pagination">
                @if($layers->onFirstPage())
                    <span class="bwsmd-pg-btn disabled">&laquo;</span>
                @else
                    <a href="{{ $layers->previousPageUrl() }}" class="bwsmd-pg-btn" title="Halaman Sebelumnya">&laquo;</a>
                @endif

                @php
                    $cur   = $layers->currentPage();
                    $last  = $layers->lastPage();
                    $win   = 2;
                    $pStart = max(1, $cur - $win);
                    $pEnd   = min($last, $cur + $win);
                @endphp

                @if($pStart > 1)
                    <a href="{{ $layers->url(1) }}" class="bwsmd-pg-btn">1</a>
                    @if($pStart > 2)<span class="bwsmd-pg-btn disabled">&hellip;</span>@endif
                @endif

                @for($p = $pStart; $p <= $pEnd; $p++)
                    @if($p === $cur)
                        <span class="bwsmd-pg-btn active">{{ $p }}</span>
                    @else
                        <a href="{{ $layers->url($p) }}" class="bwsmd-pg-btn">{{ $p }}</a>
                    @endif
                @endfor

                @if($pEnd < $last)
                    @if($pEnd < $last - 1)<span class="bwsmd-pg-btn disabled">&hellip;</span>@endif
                    <a href="{{ $layers->url($last) }}" class="bwsmd-pg-btn">{{ $last }}</a>
                @endif

                @if($layers->hasMorePages())
                    <a href="{{ $layers->nextPageUrl() }}" class="bwsmd-pg-btn" title="Halaman Berikutnya">&raquo;</a>
                @else
                    <span class="bwsmd-pg-btn disabled">&raquo;</span>
                @endif
            </div>
            @endif
        </div>
        @endif
    </div>

</div>

{{-- ============================================================ --}}
{{-- MODAL UPLOAD BERKAS SPASIAL BARU (SUPER ADMIN & ADMIN) --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalUploadGisMaster" tabindex="-1" role="dialog" aria-labelledby="modalUploadGisMasterLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 620px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <form action="{{ route('peta.upload-geojson') }}" method="POST" enctype="multipart/form-data" id="formUploadMaster">
                @csrf
                <div class="modal-header bg-primary text-white py-3 px-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="modal-title font-weight-bold mb-0" id="modalUploadGisMasterLabel" style="font-size: 15.5px; font-family: 'Plus Jakarta Sans', sans-serif;">
                            <i class="fas fa-cloud-upload-alt mr-2"></i>Unggah Berkas Spasial Baru
                        </h5>
                        <small class="text-white-50" style="font-size: 11.5px;">Data langsung dipublikasikan ke peta spasial WebGIS BWS Kalimantan I</small>
                    </div>
                    <button type="button" class="close text-white opacity-80" data-dismiss="modal" aria-label="Close" style="font-size: 22px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-4">
                    {{-- 1. Pilihan Berkas --}}
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small mb-1">
                            Berkas GIS (.GeoJSON / .JSON / .KML / .KMZ / Shapefile .ZIP) <span class="text-danger">*</span>
                        </label>
                        <div class="md-dropzone-box text-center p-3" id="dropzoneBox" onclick="document.getElementById('inputMasterFile').click()">
                            <i class="fas fa-file-upload fa-2x text-primary mb-2 d-block"></i>
                            <div class="font-weight-bold text-dark small" id="dropzoneFileName">Klik atau Seret Berkas ke Sini</div>
                            <small class="text-muted d-block mt-1" style="font-size: 11px;">Maksimal 100 MB per berkas</small>
                        </div>
                        <input type="file" name="file_geojson[]" id="inputMasterFile" class="d-none" multiple required
                               accept=".geojson,.json,.kml,.kmz,.zip" onchange="handleMasterFileSelect(this)">
                    </div>

                    {{-- 2. Kategori Infrastruktur --}}
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small mb-1">
                            Kategori Infrastruktur <span class="text-danger">*</span>
                        </label>
                        <select name="categories[]" class="form-control form-control-sm" required style="border-radius: 8px; height: 38px; font-size: 13px;">
                            <option value="">-- Pilih Kategori Infrastruktur --</option>
                            <option value="baku" {{ $categoryFilter === 'baku' ? 'selected' : '' }}>🟢 Daerah Irigasi Baku</option>
                            <option value="fungsional" {{ $categoryFilter === 'fungsional' ? 'selected' : '' }}>🔵 Daerah Irigasi Fungsional</option>
                            <option value="potensial" {{ $categoryFilter === 'potensial' ? 'selected' : '' }}>🟠 Daerah Irigasi Potensial</option>
                            <option value="saluran" {{ $categoryFilter === 'saluran' ? 'selected' : '' }}>🔷 Jaringan Saluran Irigasi</option>
                            <option value="bangunan" {{ $categoryFilter === 'bangunan' ? 'selected' : '' }}>🔴 Bangunan Air / Pelengkap</option>
                        </select>
                    </div>

                    {{-- 3. Nama Objek Spasial --}}
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small mb-1">
                            Nama Objek Spasial <small class="text-muted">(Opsional, otomatis dari nama berkas jika kosong)</small>
                        </label>
                        <input type="text" name="object_names[]" id="inputMasterObjectName" class="form-control form-control-sm"
                               placeholder="Contoh: DIR Baku Sebangkau..." style="border-radius: 8px; height: 38px; font-size: 13px;">
                    </div>

                    {{-- Info Box --}}
                    <div class="p-2.5 rounded bg-light border d-flex align-items-center" style="font-size: 12px; color: #475569;">
                        <i class="fas fa-info-circle text-primary fa-lg mr-2.5 flex-shrink-0"></i>
                        <span>Berkas yang diunggah akan otomatis diproses dan langsung dapat diakses pada tabel Master Data serta peta spasial.</span>
                    </div>
                </div>

                <div class="modal-footer bg-light py-2.5 px-4 d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-secondary btn-sm font-weight-medium px-3" data-dismiss="modal" style="border-radius: 6px;">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm font-weight-bold px-4 shadow-sm" id="btnSubmitUploadMaster" style="border-radius: 6px;">
                        <i class="fas fa-cloud-upload-alt mr-1.5"></i> Mulai Unggah
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL KONFIRMASI HAPUS (PINDAHKAN KE TRASH) --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalDeleteGis" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 440px;">
        <div class="modal-content border-0 shadow" style="border-radius: 12px; overflow: hidden;">
            <form action="{{ route('peta.delete-geojson') }}" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="file_name" id="deleteTargetFileName" value="">

                <div class="modal-header bg-danger text-white py-3 px-4">
                    <h5 class="modal-title font-weight-bold mb-0" style="font-size: 15px;">
                        <i class="fas fa-archive mr-2"></i> Pindahkan ke Arsip Terhapus?
                    </h5>
                    <button type="button" class="close text-white opacity-80" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-4 text-center">
                    <div style="width: 54px; height: 54px; border-radius: 50%; background: #fee2e2; color: #dc2626; display: inline-flex; align-items: center; justify-content: center; font-size: 22px; margin-bottom: 14px;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h6 class="font-weight-bold text-dark mb-1" id="deleteTargetDisplayName">Nama Objek</h6>
                    <p class="text-muted small mb-0">
                        Berkas akan dipindahkan ke <strong>Arsip Terhapus</strong> dan disembunyikan dari publikasi peta. Berkas dapat dipulihkan kembali oleh Super Admin sewaktu-waktu.
                    </p>
                </div>

                <div class="modal-footer bg-light py-2.5 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" style="border-radius: 6px;">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm font-weight-bold px-3 shadow-sm" style="border-radius: 6px;">
                        <i class="fas fa-archive mr-1"></i> Ya, Pindahkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
/* ================================================================
   KPI CARDS
================================================================ */
.md-kpi-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 16px 12px;
    border-radius: 12px;
    background: #fff;
    border: 1.5px solid #e9ecef;
    box-shadow: 0 1px 4px rgba(0,0,0,.05);
    transition: all .2s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    color: inherit;
}
.md-kpi-card:hover {
    box-shadow: 0 6px 18px rgba(0,0,0,.08);
    transform: translateY(-3px);
    border-color: #94a3b8;
}
.md-kpi-card:active {
    transform: translateY(0) scale(0.97) !important;
}
.md-kpi-active {
    border-color: #1d4ed8 !important;
    box-shadow: 0 0 0 3px rgba(29,78,216,.12), 0 4px 16px rgba(0,0,0,.08) !important;
}
.md-kpi-total { border-top: 3px solid #1d4ed8; }
.md-kpi-icon-total { background: #eff6ff; color: #1d4ed8; }

.md-kpi-icon-wrap {
    width: 42px; height: 42px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 17px;
    margin-bottom: 10px;
}
.md-kpi-value {
    font-size: 24px;
    font-weight: 800;
    color: #1e293b;
    line-height: 1.1;
    font-family: 'Plus Jakarta Sans', sans-serif;
}
.md-kpi-label {
    font-size: 11px;
    color: #64748b;
    font-weight: 500;
    margin-top: 4px;
    line-height: 1.3;
    font-family: 'Inter', sans-serif;
}

/* ================================================================
   FILE ICON & CATEGORY CHIP
================================================================ */
.md-file-icon {
    width: 32px; height: 32px;
    border-radius: 8px;
    background: #eff6ff;
    color: #1d4ed8;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 14px; flex-shrink: 0;
}
.md-category-chip {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px;
    border-radius: 20px;
    border: 1.5px solid;
    font-size: 11.5px; font-weight: 600;
    white-space: nowrap;
    font-family: 'Inter', sans-serif;
}
.md-cat-dot {
    width: 7px; height: 7px;
    border-radius: 50%; flex-shrink: 0;
}
.md-active-filter-chip {
    display: inline-flex; align-items: center;
    background: #dbeafe; color: #1d4ed8;
    border: 1px solid #bfdbfe;
    padding: 3px 10px; border-radius: 20px;
    font-size: 11.5px; font-weight: 700;
    vertical-align: middle;
}

/* ================================================================
   TOP HEADER BUTTONS (PROFESIONAL & GAGAH)
================================================================ */
.bws-btn-top {
    height: 38px;
    padding: 0 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    font-family: 'Plus Jakarta Sans', sans-serif;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    white-space: nowrap;
    cursor: pointer;
}
.bws-btn-top:active {
    transform: scale(0.96) !important;
}
.bws-btn-top-primary {
    background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
    color: #ffffff !important;
    border: 1px solid #1d4ed8;
}
.bws-btn-top-primary:hover {
    background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 100%);
    box-shadow: 0 4px 14px rgba(29, 78, 216, 0.35);
    transform: translateY(-1px);
}

.bws-btn-top-archive {
    background: #ffffff;
    color: #475569 !important;
    border: 1.5px solid #cbd5e1;
}
.bws-btn-top-archive:hover {
    background: #fef2f2;
    color: #dc2626 !important;
    border-color: #fca5a5;
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.15);
    transform: translateY(-1px);
}

.bws-btn-top-map {
    background: #ffffff;
    color: #1e293b !important;
    border: 1.5px solid #cbd5e1;
}
.bws-btn-top-map:hover {
    background: #eff6ff;
    color: #1d4ed8 !important;
    border-color: #93c5fd;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
    transform: translateY(-1px);
}

/* ================================================================
   ACTION BUTTONS DI DALAM TABEL (DOWNLOAD, LIHAT PETA, HAPUS)
================================================================ */
.bws-action-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12.5px;
    cursor: pointer;
    text-decoration: none !important;
    transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
}
.bws-action-btn:active {
    transform: scale(0.90) !important;
}
.bws-act-download {
    color: #2563eb;
}
.bws-act-download:hover {
    background: #eff6ff;
    border-color: #93c5fd;
    color: #1d4ed8;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(37, 99, 235, 0.20);
}
.bws-act-map {
    color: #16a34a;
}
.bws-act-map:hover {
    background: #f0fdf4;
    border-color: #86efac;
    color: #15803d;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(22, 163, 74, 0.20);
}
.bws-act-delete {
    color: #dc2626;
}
.bws-act-delete:hover {
    background: #fef2f2;
    border-color: #fca5a5;
    color: #b91c1c;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(220, 38, 38, 0.20);
}

.table-hover tbody tr:hover {
    background-color: #f8fafc;
}

/* ================================================================
   DROPZONE STYLING
================================================================ */
.md-dropzone-box {
    border: 2px dashed #93c5fd;
    border-radius: 10px;
    background: #f8fafc;
    cursor: pointer;
    transition: all 0.2s ease;
}
.md-dropzone-box:hover {
    background: #eff6ff;
    border-color: #3b82f6;
}

/* ================================================================
   PAGINATION
================================================================ */
.bwsmd-pagination {
    display: flex; align-items: center; gap: 4px; flex-wrap: wrap;
}
.bwsmd-pg-btn {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 36px; height: 36px; padding: 0 10px;
    border-radius: 8px; border: 1.5px solid #e2e8f0;
    background: #fff; color: #475569;
    font-size: 13px; font-weight: 600; cursor: pointer;
    text-decoration: none;
    transition: all .15s ease; line-height: 1;
    font-family: 'Inter', sans-serif;
}
.bwsmd-pg-btn:hover:not(.active):not(.disabled) {
    background: #f1f5f9; border-color: #94a3b8;
    color: #1e293b; text-decoration: none;
}
.bwsmd-pg-btn:active:not(.disabled) {
    transform: scale(0.93) !important;
}
.bwsmd-pg-btn.active {
    background: #1d4ed8; border-color: #1d4ed8;
    color: #fff; font-weight: 700; cursor: default;
    box-shadow: 0 2px 6px rgba(29,78,216,.35);
}
.bwsmd-pg-btn.disabled {
    opacity: .4; cursor: not-allowed;
    pointer-events: none; background: #f8fafc;
}
</style>

<script>
function handleMasterFileSelect(input) {
    const box = document.getElementById('dropzoneFileName');
    const nameInput = document.getElementById('inputMasterObjectName');
    if (input.files && input.files.length > 0) {
        if (input.files.length === 1) {
            const fileName = input.files[0].name;
            box.innerHTML = '<span class="text-success"><i class="fas fa-check mr-1"></i> ' + escapeHtml(fileName) + '</span>';
            if (nameInput && !nameInput.value) {
                // Auto-fill object name from filename without extension
                const cleanName = fileName.replace(/\.[^/.]+$/, "").replace(/[-_]/g, " ");
                nameInput.value = cleanName;
            }
        } else {
            box.innerHTML = '<span class="text-success"><i class="fas fa-check-double mr-1"></i> ' + input.files.length + ' berkas dipilih</span>';
        }
    } else {
        box.textContent = 'Klik atau Seret Berkas ke Sini';
    }
}

function confirmDeleteGis(fileName, displayName) {
    document.getElementById('deleteTargetFileName').value = fileName;
    document.getElementById('deleteTargetDisplayName').textContent = displayName || fileName;
    $('#modalDeleteGis').modal('show');
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
</script>
@endpush
