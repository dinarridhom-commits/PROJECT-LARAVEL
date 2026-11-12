@extends('layout.master')

@section('title', 'Tong Sampah Berkas GIS - Super Admin')

@section('konten')
<div class="container-fluid">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 font-weight-bold text-gray-800">
                <i class="fas fa-trash-alt text-danger mr-2"></i>
                Tong Sampah Berkas GIS (Trash)
            </h1>
            <p class="text-muted small mb-0">
                Berkas spasial GeoJSON yang dihapus sementara (*Soft Deleted*). Anda dapat memulihkan (*Restore*) berkas kembali ke peta atau menghapusnya secara permanen.
            </p>
        </div>

        <div class="d-flex align-items-center" style="gap: 8px;">
            <a href="{{ route('gis.verification') }}" class="btn btn-outline-primary shadow-sm font-weight-bold">
                <i class="fas fa-clipboard-check mr-1"></i> Antrean Verifikasi
            </a>
            <a href="{{ route('peta') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-map mr-1"></i> Kembali ke Peta
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

    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-dark">
                <i class="fas fa-layer-group text-muted mr-2"></i>
                Berkas GIS Terhapus Sementara ({{ count($trashedLayers) }})
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="pl-4 py-3">Nama Objek Spasial</th>
                            <th class="py-3">Nama Berkas Fisik</th>
                            <th class="py-3">Kategori</th>
                            <th class="py-3">Ukuran</th>
                            <th class="py-3">Waktu Dihapus</th>
                            <th class="pr-4 text-right py-3">Aksi Pemulihan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trashedLayers as $tl)
                            <tr>
                                <td class="pl-4 font-weight-bold text-dark">
                                    <i class="fas fa-map-pin mr-1.5 text-danger"></i>
                                    {{ $tl->nama_objek }}
                                </td>
                                <td class="text-muted small font-monospace">{{ $tl->filename }}</td>
                                <td>
                                    <span class="badge badge-secondary px-2 py-1 text-uppercase" style="font-size: 11px;">
                                        {{ $tl->category }}
                                    </span>
                                </td>
                                <td class="text-muted small">{{ $tl->file_size ?? '-' }}</td>
                                <td class="text-muted small">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $tl->deleted_at ? $tl->deleted_at->format('d M Y, H:i') : '-' }}
                                    <span class="text-xs">({{ $tl->deleted_at ? $tl->deleted_at->diffForHumans() : '' }})</span>
                                </td>
                                <td class="pr-4 text-right">
                                    {{-- Tombol Restore --}}
                                    <form action="{{ route('gis.restore', $tl->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-success px-3 py-1 font-weight-bold shadow-sm" style="border-radius: 6px;" title="Pulihkan berkas kembali ke peta aktif">
                                            <i class="fas fa-undo mr-1"></i> Pulihkan ke Peta
                                        </button>
                                    </form>

                                    {{-- Tombol Force Delete --}}
                                    <form action="{{ route('gis.forceDelete', $tl->id) }}" method="POST" class="d-inline ml-1" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin menghapus berkas GIS ini secara PERMANEN? File fisik di server akan dihapus total!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger px-2.5 py-1" style="border-radius: 6px;" title="Hapus total secara permanen">
                                            <i class="fas fa-times mr-1"></i> Hapus Permanen
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-trash-alt fa-3x mb-3 text-gray-300 d-block"></i>
                                    Tong sampah berkas GIS kosong. Tidak ada layer yang terhapus sementara.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
