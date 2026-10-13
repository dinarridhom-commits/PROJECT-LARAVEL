<!-- Component Tabel Produk Dashboard (Read Only / Non-CRUD) -->
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-primary text-white">
        <h6 class="m-0 font-weight-bold">
            <i class="fas fa-boxes mr-2"></i>Tabel Data Produk
            <span class="badge badge-light text-primary ml-2">{{ count($produks) }} data</span>

        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th class="text-center align-middle" style="width: 50px;">No</th>
                        <th class="text-center align-middle" style="width: 140px;">Kode Produk</th>
                        <th class="align-middle">Nama Produk</th>
                        <th class="align-middle" style="width: 160px;">Kategori</th>
                        <th class="text-right align-middle" style="width: 140px;">Harga</th>
                        <th class="text-center align-middle" style="width: 90px;">Stok</th>
                        <th class="align-middle">Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produks as $item)
                    <tr>
                        <td class="text-center align-middle">{{ $loop->iteration }}</td>
                        <td class="text-center align-middle font-weight-bold text-dark">
                            {!! DNS2D::getBarcodeHTML($item->kode_produk, 'QRCODE', 4, 4) !!}
                            <!-- <span class="badge badge-secondary px-2 py-1">{{ $item->kode_produk ?? '-' }}</span> -->
                        </td>
                        <td class="align-middle font-weight-bold text-dark">{{ $item->nama_produk }}</td>
                        <td class="align-middle">
                            @if(!empty($item->kategori->nama_kategori))
                                <span class="badge badge-info">{{ $item->kategori->nama_kategori }}</span>
                            @else
                                <span class="badge badge-light text-muted">Tanpa Kategori</span>
                            @endif
                        </td>
                        <td class="text-right align-middle font-weight-bold text-dark">
                            Rp {{ number_format($item->harga, 0, ',', '.') }}
                        </td>
                        <td class="text-center align-middle">
                            @if($item->stok > 10)
                                <span class="badge badge-success px-2 py-1">{{ $item->stok }}</span>
                            @elseif($item->stok > 0)
                                <span class="badge badge-warning text-dark px-2 py-1">{{ $item->stok }}</span>
                            @else
                                <span class="badge badge-danger px-2 py-1">Habis</span>
                            @endif
                        </td>
                        <td class="align-middle text-muted small">{{ $item->deskripsi ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-box-open fa-2x mb-2 text-gray-300 d-block"></i>
                            Belum ada data produk{{ !empty($search) ? ' yang sesuai pencarian' : '' }}.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
