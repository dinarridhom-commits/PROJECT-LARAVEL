@extends('layout.master')

@section('title', 'Verifikasi Berkas GIS - Super Admin')

@section('konten')
<div class="container-fluid">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 font-weight-bold text-gray-800">
                <i class="fas fa-clipboard-check text-primary mr-2"></i>
                Verifikasi Berkas Spasial (Approval Workflow)
            </h1>
            <p class="text-muted small mb-0">
                Tinjau dan verifikasi data GeoJSON yang diunggah oleh staf sebelum dipublikasikan ke peta WebGIS BWS Kalimantan I.
            </p>
        </div>

        <div class="d-flex align-items-center" style="gap: 8px;">
            <a href="{{ route('gis.trash') }}" class="btn btn-outline-secondary font-weight-bold shadow-sm" title="Buka Tong Sampah Berkas GIS">
                <i class="fas fa-trash-alt mr-1 text-danger"></i> Tong Sampah GIS
            </a>
            <a href="{{ route('peta') }}" class="btn btn-primary font-weight-bold shadow-sm">
                <i class="fas fa-map mr-1"></i> Buka Peta
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    {{-- TABEL ANTREAN PENDING --}}
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between" style="border-bottom: 1px solid #f1f5f9;">
            <h6 class="m-0 font-weight-bold text-dark">
                <i class="fas fa-clock text-warning mr-2"></i>
                Antrean Berkas Menunggu Verifikasi ({{ count($pendingLayers) }})
            </h6>
            @if(count($pendingLayers) > 0)
                <span class="badge badge-warning text-dark font-weight-bold px-2 py-1">Butuh Review</span>
            @endif
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="pl-4 py-3">Nama Objek Spasial</th>
                            <th class="py-3">Pengunggah</th>
                            <th class="py-3">Kategori</th>
                            <th class="py-3">Ukuran File</th>
                            <th class="py-3">Waktu Diunggah</th>
                            <th class="pr-4 text-right py-3">Aksi Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingLayers as $l)
                            <tr>
                                <td class="pl-4 font-weight-bold text-dark">
                                    <i class="fas fa-file-code text-primary mr-1.5"></i>
                                    {{ $l->nama_objek }}
                                    <small class="text-muted d-block font-monospace">{{ $l->filename }}</small>
                                </td>
                                <td>
                                    <span class="font-weight-semibold text-dark">
                                        {{ $l->user ? $l->user->name : 'Staf GIS' }}
                                    </span>
                                    <small class="text-muted d-block">{{ $l->user ? $l->user->email : '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-primary px-2 py-1 text-uppercase" style="font-size: 11px;">
                                        {{ $l->category }}
                                    </span>
                                </td>
                                <td class="text-muted small">{{ $l->file_size ?? '-' }}</td>
                                <td class="text-muted small">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $l->created_at ? $l->created_at->format('d M Y, H:i') : '-' }}
                                </td>
                                <td class="pr-4 text-right">
                                    {{-- Tombol Setujui (Approve) --}}
                                    <form action="{{ route('gis.approve', $l->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Setujui berkas ini agar langsung tampil di peta WebGIS publik?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success font-weight-bold px-3 py-1 shadow-sm" style="border-radius: 6px;" title="Setujui dan publikasikan berkas ke peta">
                                            <i class="fas fa-check mr-1"></i> Setujui
                                        </button>
                                    </form>

                                    {{-- Tombol Tolak (Reject) --}}
                                    <button type="button" class="btn btn-sm btn-outline-danger px-2.5 py-1 ml-1" style="border-radius: 6px;" data-toggle="modal" data-target="#modalReject{{ $l->id }}" title="Tolak berkas dengan catatan alasan">
                                        <i class="fas fa-times mr-1"></i> Tolak
                                    </button>

                                    {{-- Modal Alasan Penolakan --}}
                                    <div class="modal fade text-left" id="modalReject{{ $l->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                                                <form action="{{ route('gis.reject', $l->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header bg-light">
                                                        <h5 class="modal-title font-weight-bold text-danger">
                                                            <i class="fas fa-exclamation-triangle mr-2"></i>Tolak Berkas GIS
                                                        </h5>
                                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="small text-muted mb-3">
                                                            Anda akan menolak berkas <b>{{ $l->nama_objek }}</b>. Berikan catatan alasan penolakan agar staf pengunggah dapat melakukan perbaikan.
                                                        </p>
                                                        <div class="form-group mb-0">
                                                            <label class="font-weight-bold small text-dark">Alasan Penolakan <span class="text-danger">*</span></label>
                                                            <textarea name="review_notes" rows="3" class="form-control" placeholder="Contoh: Format koordinat belum sesuai standar WGS84, atau batas poligon bertumpuk..." required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-danger btn-sm font-weight-bold">
                                                            <i class="fas fa-paper-plane mr-1"></i> Kirim Penolakan
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-check-double fa-3x mb-3 text-success d-block"></i>
                                    Semua berkas spasial telah diverifikasi. Tidak ada antrean pending saat ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- RIWAYAT VERIFIKASI TERAKHIR --}}
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-dark">
                <i class="fas fa-history text-secondary mr-2"></i>
                Riwayat Verifikasi Terbaru
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 12.5px;">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="pl-4 py-2.5">Nama Berkas</th>
                            <th class="py-2.5">Pengunggah</th>
                            <th class="py-2.5">Status</th>
                            <th class="py-2.5">Diverifikasi Oleh</th>
                            <th class="py-2.5">Catatan / Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($historyLayers as $hl)
                            <tr>
                                <td class="pl-4 font-weight-bold text-dark">
                                    {{ $hl->nama_objek }}
                                </td>
                                <td class="text-muted">{{ $hl->user ? $hl->user->name : '-' }}</td>
                                <td>
                                    @if($hl->status === 'approved')
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i>Disetujui</span>
                                    @else
                                        <span class="badge badge-danger px-2 py-1"><i class="fas fa-times mr-1"></i>Ditolak</span>
                                    @endif
                                </td>
                                <td>{{ $hl->reviewer ? $hl->reviewer->name : 'Super Admin' }}</td>
                                <td>
                                    <span class="text-muted">{{ $hl->review_notes ?? '-' }}</span>
                                    <small class="d-block text-gray-500">{{ $hl->reviewed_at ? $hl->reviewed_at->format('d M Y, H:i') : '' }}</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat verifikasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

