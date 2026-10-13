<!-- Component Tabel Kategori Dashboard (Read Only / Non-CRUD) -->
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-success text-white">
        <h6 class="m-0 font-weight-bold">
            <i class="fas fa-tags mr-2"></i>Tabel Data Kategori
            <span class="badge badge-light text-success ml-2">{{ count($kategori) }} data</span>
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th class="text-center align-middle" style="width: 50px;">No</th>
                        <th class="align-middle" style="width: 250px;">Nama Kategori</th>
                        <th class="align-middle">Deskripsi</th>
                        <th class="text-center align-middle" style="width: 160px;">Jumlah Produk</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kategori as $kat)
                    <tr>
                        <td class="text-center align-middle">{{ $loop->iteration }}</td>
                        <td class="align-middle font-weight-bold text-dark">
                            {{ $kat->nama_kategori }}
                        </td>
                        <td class="align-middle text-muted">{{ $kat->deskripsi ?? '-' }}</td>
                        <td class="text-center align-middle">
                            <span class="badge badge-pill badge-primary px-3 py-1 font-weight-bold">
                                {{ $kat->produks_count ?? 0 }} Produk
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">
                            <i class="fas fa-folder-open fa-2x mb-2 text-gray-300 d-block"></i>
                            Belum ada data kategori{{ !empty($search) ? ' yang sesuai pencarian' : '' }}.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
