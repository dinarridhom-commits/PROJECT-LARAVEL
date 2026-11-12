{{--
    _modals.blade.php
    Partial View: Semua Modal Popup
    Halaman Peta Daerah Irigasi (DIR) - BWS Kalimantan I

    Fungsi: Menampung seluruh modal dialog yang digunakan di halaman peta:
    1. Modal Upload GeoJSON  → Form upload file GeoJSON baru ke server
    2. Modal Kelola File GIS → Daftar & hapus file GeoJSON di server
    3. Form Bulk Delete      → Hidden form untuk hapus banyak file sekaligus
    4. Modal Logout          → Konfirmasi logout dari sistem

    Digunakan di: resources/views/dashboard/peta.blade.php
    Dipanggil via: @include('dashboard.partials._modals')
--}}


{{-- ============================================================
     MODAL 1: UPLOAD GEOJSON
     Fungsi: Form upload file GeoJSON ke folder public/geojson/.
     Mendukung multi-file dan chunked batch upload (5 file/batch)
     untuk menghindari error PostTooLargeException dari PHP.
     Progress bar realtime per batch ditampilkan saat upload.
     ============================================================ --}}
@if(Auth::check() && in_array(Auth::user()->role, ['admin', 'superadmin']))
{{-- ============================================================
     MODAL 1: UPLOAD & PILIH LAYER GIS (Interactive File & Category Selector)
     Sesuai Konsep:
     1. Pilih Berkas Spasial (.ZIP / .GeoJSON / .KML)
     2. Layer Terdeteksi di dalam Berkas (Pilih yang diunggah & Kategori)
     ============================================================ --}}
<div class="modal fade" id="modalUploadGeojson" tabindex="-1" role="dialog"
     aria-labelledby="modalUploadGeojsonLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 680px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">

            {{-- Modal Header --}}
            <div class="modal-header bg-primary text-white py-3 px-4 d-flex align-items-center justify-content-between">
                <h5 class="modal-title font-weight-bold mb-0" id="modalUploadGeojsonLabel" style="font-size: 15.5px;">
                    <i class="fas fa-cloud-upload-alt mr-2"></i>Upload &amp; Pilih Layer GIS (GeoJSON / SHP / KML)
                </h5>
                <button type="button" class="close text-white opacity-80" data-dismiss="modal"
                        aria-label="Close" id="btnCloseUploadModal" style="font-size: 20px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-4">

                {{-- 1. PILIH BERKAS SPASIAL --}}
                <div class="form-group mb-3">
                    <label class="font-weight-bold text-dark mb-1.5" style="font-size: 13.5px;">
                        1. Pilih Berkas Spasial:
                    </label>
                    <div class="custom-file">
                        <input type="file"
                               class="custom-file-input"
                               id="inputGeojsonFile"
                               multiple
                               accept=".geojson,.json,.kml,.kmz,.zip"
                               onchange="handleTriageFileSelection(this)">
                        <label class="custom-file-label font-weight-normal text-muted" for="inputGeojsonFile" id="labelGeojsonFile">
                            Pilih file .ZIP / .GeoJSON / .KML...
                        </label>
                    </div>
                    <small class="form-text text-muted mt-1.5" style="font-size: 11.5px;">
                        <i class="fas fa-info-circle text-primary mr-1"></i>
                        Format yang didukung: <code>.zip</code> (Shapefile ArcMap multi-layer), <code>.geojson</code>, <code>.kml</code>, <code>.kmz</code>.
                    </small>

                    {{-- Loading Spinner saat membaca isi file --}}
                    <div id="step1InspectingLoading" class="text-center py-2.5 my-2 rounded bg-light" style="display: none; border: 1px dashed #93c5fd;">
                        <i class="fas fa-spinner fa-spin text-primary mr-1.5"></i>
                        <span class="font-weight-bold text-dark small">Mendeteksi layer di dalam berkas...</span>
                    </div>

                    {{-- Alert error saat membaca berkas --}}
                    <div id="step1ErrorAlert" class="alert alert-danger py-2 px-3 small my-2" style="display: none;"></div>
                </div>

                {{-- 2. LAYER TERDETEKSI DI DALAM BERKAS (PILIH YANG DIUNGGAH) --}}
                <div class="form-group mb-2">
                    <div class="d-flex align-items-center justify-content-between mb-1.5">
                        <label class="font-weight-bold text-dark mb-0" style="font-size: 13.5px;">
                            2. Layer Terdeteksi di dalam Berkas (Pilih yang diunggah):
                        </label>
                        <div id="triageActionButtons" style="display: none;">
                            <button type="button" class="btn btn-xs btn-outline-secondary font-weight-bold mr-1" onclick="selectAllTriage(true)" style="font-size: 10.5px; padding: 2px 7px; border-radius: 4px;">Pilih Semua</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary font-weight-bold" onclick="selectAllTriage(false)" style="font-size: 10.5px; padding: 2px 7px; border-radius: 4px;">Batal Semua</button>
                        </div>
                    </div>

                    {{-- Placeholder saat belum ada file yang dipilih --}}
                    <div id="triagePlaceholder" class="p-4 text-center rounded border" style="background: #f8fafc; border-style: dashed !important; border-color: #cbd5e1 !important;">
                        <i class="fas fa-layer-group text-muted mb-2 fa-2x opacity-50 d-block"></i>
                        <span class="text-muted small">Pilih berkas <code>.ZIP</code> atau GIS di atas untuk melihat dan memilah layer di sini.</span>
                    </div>

                    {{-- Box Daftar Layer Terdeteksi (Sesuai Diagram) --}}
                    <div id="triageTableBox" class="border rounded" style="display: none; max-height: 260px; overflow-y: auto; background: #ffffff;">
                        <table class="table table-hover align-middle mb-0" id="triageTable" style="font-size: 12.5px;">
                            <tbody id="triageTableBody">
                                {{-- Diisi secara dinamis oleh JavaScript --}}
                            </tbody>
                        </table>
                    </div>

                    {{-- Keterangan Footer Jumlah Dipilih --}}
                    <div id="triageFooterInfo" class="mt-2 text-muted small" style="display: none; font-size: 11.5px;">
                        * <span id="triageSelectedCountBadge" class="font-weight-bold text-dark">0</span> dari <span id="triageTotalCountBadge" class="font-weight-bold text-dark">0</span> layer dipilih untuk diunggah
                    </div>
                </div>

                {{-- PROGRESS BAR UPLOAD --}}
                <div id="uploadProgressSection" class="mt-3" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="font-weight-bold small text-primary" id="uploadStatusText">
                            <i class="fas fa-spinner fa-spin mr-1"></i>Menyimpan berkas terpilih...
                        </span>
                        <span class="font-weight-bold small text-muted" id="uploadPercentText">0%</span>
                    </div>
                    <div class="progress" style="height: 14px; border-radius: 7px;">
                        <div id="uploadProgressBar"
                             class="progress-bar progress-bar-striped progress-bar-animated bg-success font-weight-bold"
                             role="progressbar"
                             style="width: 0%; font-size: 10px;">0%</div>
                    </div>
                </div>

                {{-- ALERT SUKSES --}}
                <div id="uploadSuccessSection" class="alert alert-success py-2.5 px-3 small mt-3" style="display: none;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle fa-lg mr-2 text-success"></i>
                        <div>
                            <div class="font-weight-bold text-dark">Proses Selesai!</div>
                            <span id="uploadSuccessMessage">Layer terpilih berhasil diunggah dan disimpan ke sistem.</span>
                        </div>
                    </div>
                </div>

                {{-- ALERT ERROR --}}
                <div id="uploadErrorSection" class="alert alert-danger py-2 px-3 small mt-3" style="display: none;">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    <span id="uploadErrorMessage"></span>
                </div>

            </div>

            {{-- FOOTER MODAL (Sesuai Diagram: [ Batal ]  [ ⬆ Upload Terpilih ]) --}}
            <div class="modal-footer bg-light py-2.5 px-4 d-flex align-items-center justify-content-between">
                <button type="button" class="btn btn-secondary font-weight-bold px-3 py-1.5" data-dismiss="modal" id="btnBatalUpload" style="font-size: 13px; border-radius: 8px;">
                    Batal
                </button>
                <button type="button" class="btn btn-primary font-weight-bold px-4 py-1.5 shadow-sm" id="btnSubmitTriage" onclick="submitTriageUpload()" disabled style="font-size: 13px; border-radius: 8px;">
                    <i class="fas fa-arrow-up mr-1.5"></i>Upload Terpilih
                </button>
            </div>

        </div>
    </div>
</div>

        </div>
    </div>
</div>


{{-- ============================================================
     MODAL 2: KELOLA & HAPUS FILE GEOJSON
     Fungsi: Menampilkan daftar semua file GeoJSON yang tersimpan
     di server (folder public/geojson/) dengan fitur:
     - Pencarian nama file realtime
     - Checkbox untuk memilih file (single/all)
     - Tombol hapus per file (konfirmasi)
     - Tombol hapus massal (bulk delete) untuk file terpilih
     ============================================================ --}}
<div class="modal fade" id="modalKelolaGeojson" tabindex="-1" role="dialog"
     aria-labelledby="modalKelolaGeojsonLabel" aria-hidden="true">
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

                {{-- Info jumlah file & pencarian --}}
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <div class="small text-muted mb-2 mb-sm-0 d-flex align-items-center flex-wrap gap-2">
                        <span>Total tersimpan:
                            <span class="badge badge-primary px-2 py-1">
                                {{ count($fileList ?? []) }} File GIS
                            </span>
                        </span>
                        <span id="bulk-selected-info" class="ml-2" style="display:none;">
                            <span class="badge badge-warning text-dark px-2 py-1">
                                <i class="fas fa-check-square mr-1"></i>
                                <span id="bulk-selected-count">0</span> Dipilih
                            </span>
                        </span>
                    </div>
                    <div class="w-50">
                        <input type="text"
                               id="cari-file-geojson"
                               class="form-control form-control-sm"
                               placeholder="🔍 Cari nama file..."
                               onkeyup="filterTabelFileGeojson(this.value)">
                    </div>
                </div>

                {{-- Tabel daftar file --}}
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
                                        <input type="checkbox"
                                               class="custom-control-input chk-file-geojson"
                                               id="chk-file-{{ $index }}"
                                               value="{{ $f['name'] }}">
                                        <label class="custom-control-label" for="chk-file-{{ $index }}"></label>
                                    </div>
                                </td>
                                <td class="text-center font-weight-bold text-muted align-middle">{{ $index + 1 }}</td>
                                <td class="align-middle">
                                    <i class="fas fa-file-code text-info mr-1"></i>
                                    <span class="nama-file-text font-weight-bold">{{ $f['name'] }}</span>
                                    @if(!$f['is_default'])
                                        <span class="badge badge-warning text-dark ml-1 small">
                                            <i class="fas fa-cloud-upload-alt mr-1"></i>Upload
                                        </span>
                                    @else
                                        <span class="badge badge-light border text-secondary ml-1 small">Sistem</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge badge-light border">{{ $f['size'] }}</span>
                                </td>
                                <td class="text-center small text-muted align-middle">{{ $f['date'] }}</td>
                                <td class="text-center align-middle">
                                    {{-- Tombol hapus per file dengan konfirmasi --}}
                                    <form action="{{ route('peta.delete-geojson') }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus file \'{{ $f['name'] }}\'? File ini akan dihapus secara permanen dari server.')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="file_name" value="{{ $f['name'] }}">
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger py-0 px-2 font-weight-bold"
                                                title="Hapus File Ini">
                                            <i class="fas fa-trash-alt mr-1"></i>Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">
                                    Tidak ada file GIS di folder public/geojson
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

            <div class="modal-footer bg-light py-2 d-flex justify-content-between align-items-center">
                {{-- Tombol hapus terpilih (bulk delete) & Tong Sampah --}}
                <div class="d-flex align-items-center">
                    <button type="button"
                            id="btn-hapus-terpilih"
                            class="btn btn-danger btn-sm font-weight-bold"
                            disabled
                            onclick="konfirmasiHapusTerpilih()">
                        <i class="fas fa-trash-alt mr-1"></i>
                        Hapus Terpilih (<span id="btn-bulk-count">0</span>)
                    </button>
                    @if(Auth::check() && Auth::user()->role === 'superadmin')
                    <a href="{{ route('gis.trash') }}" class="btn btn-outline-secondary btn-sm font-weight-bold ml-2" title="Buka Tong Sampah Berkas GIS (Trash)">
                        <i class="fas fa-trash-restore mr-1 text-danger"></i> Tong Sampah GIS
                    </a>
                    @endif
                </div>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Tutup
                </button>
            </div>

        </div>
    </div>
</div>


{{-- ============================================================
     HIDDEN BULK DELETE FORM
     Fungsi: Form tersembunyi yang diisi secara dinamis oleh
     JavaScript (fungsi konfirmasiHapusTerpilih) ketika user
     ingin menghapus banyak file sekaligus. File yang dipilih
     dimasukkan sebagai input hidden sebelum form disubmit.
     ============================================================ --}}
<form id="form-bulk-delete"
      action="{{ route('peta.delete-geojson-bulk') }}"
      method="POST"
      style="display:none;">
    @csrf
    @method('DELETE')
    <div id="bulk-delete-inputs"></div>
</form>
@endif


@auth
{{-- ============================================================
     MODAL 3: KONFIRMASI LOGOUT (Modern Centered Dialog)
     Fungsi: Dialog konfirmasi sebelum user keluar dari sistem WebGIS.
     ============================================================ --}}
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog"
     aria-labelledby="logoutModalLabel" aria-hidden="true">
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
@endauth
