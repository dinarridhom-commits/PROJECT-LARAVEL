@extends('layout.master')

@section('title', 'Tong Sampah Pengguna - Super Admin')

@section('konten')
<div class="container-fluid">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 font-weight-bold text-gray-800">
                <i class="fas fa-trash-restore text-warning mr-2"></i>
                Tong Sampah Pengguna (Trash)
            </h1>
            <p class="text-muted small mb-0">
                Daftar akun pengguna yang dihapus sementara (*Soft Deleted*). Anda dapat memulihkan (*Restore*) atau menghapus permanen.
            </p>
        </div>

        <div class="d-flex align-items-center">
            <a href="{{ route('users.index') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Manajemen User
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
                <i class="fas fa-user-slash text-danger mr-2"></i>
                Akun Terhapus Sementara ({{ count($trashedUsers) }})
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="pl-4 py-3">No</th>
                            <th class="py-3">Nama</th>
                            <th class="py-3">Email</th>
                            <th class="py-3">Role</th>
                            <th class="py-3">Waktu Dihapus</th>
                            <th class="pr-4 text-right py-3">Aksi Pemulihan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trashedUsers as $i => $u)
                            <tr>
                                <td class="pl-4 font-weight-bold">{{ $i + 1 }}</td>
                                <td class="font-weight-bold text-dark">
                                    <i class="fas fa-user-circle mr-1.5 text-muted"></i>
                                    {{ $u->name }}
                                </td>
                                <td>{{ $u->email }}</td>
                                <td>
                                    <span class="badge badge-{{ $u->role === 'superadmin' ? 'danger' : 'warning' }} px-2 py-1">
                                        {{ $u->role === 'superadmin' ? 'Super Admin' : 'Admin' }}
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $u->deleted_at ? $u->deleted_at->format('d M Y, H:i') : '-' }}
                                    <span class="text-xs">({{ $u->deleted_at ? $u->deleted_at->diffForHumans() : '' }})</span>
                                </td>
                                <td class="pr-4 text-right">
                                    {{-- Tombol Restore (Pulihkan) --}}
                                    <form action="{{ route('users.restore', $u->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-success px-3 py-1 font-weight-bold" style="border-radius: 6px;" title="Pulihkan akun ini agar bisa login kembali">
                                            <i class="fas fa-undo mr-1"></i> Pulihkan
                                        </button>
                                    </form>

                                    {{-- Tombol Force Delete (Hapus Permanen) --}}
                                    <form action="{{ route('users.forceDelete', $u->id) }}" method="POST" class="d-inline ml-1" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin menghapus akun ini secara PERMANEN? Data tidak akan bisa dikembalikan lagi!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger px-2.5 py-1" style="border-radius: 6px;" title="Hapus permanen total dari basis data">
                                            <i class="fas fa-times mr-1"></i> Hapus Permanen
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-trash-alt fa-3x mb-3 text-gray-300 d-block"></i>
                                    Tong sampah pengguna kosong. Tidak ada akun yang terhapus sementara.
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

