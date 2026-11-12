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
<div class="modal fade" id="modalUploadGeojson" tabindex="-1" role="dialog"
     aria-labelledby="modalUploadGeojsonLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="formUploadGeojson"
              action="{{ route('peta.upload-geojson') }}"
              method="POST"
              enctype="multipart/form-data"
              class="modal-content border-0 shadow">
            @csrf

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold" id="modalUploadGeojsonLabel">
                    <i class="fas fa-cloud-upload-alt mr-2"></i>Upload File GIS (GeoJSON / SHP / KML)
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"
                        aria-label="Close" id="btnCloseUploadModal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-4">

                {{-- Input pilih file --}}
                <div class="form-group mb-3" id="uploadInputContainer">
                    <label class="font-weight-bold text-gray-800">
                        <i class="fas fa-file-code text-primary mr-1"></i>Pilih File GIS Spasial:
                    </label>
                    <div class="custom-file">
                        <input type="file"
                               name="file_geojson[]"
                               class="custom-file-input"
                               id="inputGeojsonFile"
                               multiple
                               accept=".geojson,.json,.kml,.kmz,.zip"
                               required
                               onchange="updateFileInputLabel(this)">
                        <label class="custom-file-label" for="inputGeojsonFile" id="labelGeojsonFile">
                            Pilih file GeoJSON, Shapefile (.zip), atau KML/KMZ...
                        </label>
                    </div>
                    <small class="form-text text-muted mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Format yang didukung: <code>.geojson</code>, <code>.json</code>, Shapefile ArcMap (<code>.zip</code>), atau Google Earth (<code>.kml</code>, <code>.kmz</code>).
                        Data akan otomatis dikonversi ke GeoJSON standar oleh sistem.
                    </small>

                    {{-- Peringatan jika pengguna memilih file .rar --}}
                    <div id="rarWarningAlert" class="alert alert-warning py-2 px-3 small mt-2 mb-0" style="display: none;">
                        <i class="fas fa-exclamation-triangle mr-1 text-warning"></i>
                        <strong>Perhatian:</strong>
                        <span id="rarWarningText">Format .rar tidak didukung. Harap gunakan format .ZIP untuk Shapefile.</span>
                    </div>
                </div>

                {{-- Progress bar upload --}}
                <div id="uploadProgressSection" class="mb-3" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="font-weight-bold small text-primary" id="uploadStatusText">
                            <i class="fas fa-spinner fa-spin mr-1"></i>Mengunggah file...
                        </span>
                        <span class="font-weight-bold small text-muted" id="uploadPercentText">0%</span>
                    </div>
                    <div class="progress" style="height: 18px; border-radius: 9px;">
                        <div id="uploadProgressBar"
                             class="progress-bar progress-bar-striped progress-bar-animated bg-success font-weight-bold"
                             role="progressbar"
                             style="width: 0%; font-size: 11px;">0%</div>
                    </div>
                    <div class="small text-muted mt-2 d-flex justify-content-between" id="uploadDetailText">
                        <span>Memproses batch file...</span>
                        <span id="uploadBatchCountText">Batch 0/0</span>
                    </div>
                </div>

                {{-- Alert sukses --}}
                <div id="uploadSuccessSection"
                     class="alert alert-success py-2 px-3 small mb-3"
                     style="display: none;">
                    <i class="fas fa-check-circle mr-1"></i>
                    <span id="uploadSuccessMessage">Semua file berhasil diunggah!</span>
                </div>

                {{-- Alert error --}}
                <div id="uploadErrorSection"
                     class="alert alert-danger py-2 px-3 small mb-3"
                     style="display: none;">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    <span id="uploadErrorMessage"></span>
                </div>

                {{-- Info tujuan penyimpanan --}}
                <div class="alert alert-info py-2 px-3 small mb-0" id="uploadInfoNote">
                    <i class="fas fa-folder-open mr-1"></i>
                    File yang diunggah akan langsung disimpan ke folder
                    <code>public/geojson/</code> dan otomatis dimuat ke peta interaktif.
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
