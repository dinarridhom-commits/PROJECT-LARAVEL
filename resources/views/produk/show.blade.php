@extends('layout.master')

@section('title', $title)

@section('konten')

<h1 class="h3 mb-4 text-gray-800">Daftar Produk Kami</h1>

<a href="{{ route('produk.create') }}" class="btn btn-primary mb-3">Tambah Data</a>

@if(session('message'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('message') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<div class="card mb-4">
    <div class="card-body">
        <p class="mb-1"><strong>Nama Toko : </strong>Gemilang Abadi</p>
        <p class="mb-1"><strong>Alamat : </strong>Pontianak, Kalimantan Barat</p>
        <p class="mb-0"><strong>Tipe Toko : </strong>ruko</p>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-gray-800">Daftar Produk</h6>
        <form action="/produk" method="GET" class="form-inline">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Cari data produk" value="{{ request('search') }}">
                <div class="input-group-append">
                    <button class="btn btn-success" type="submit">Cari Data</button>
                </div>
            </div>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th class="text-center align-middle" style="width: 50px;">No</th>
                        <th class="text-center align-middle" style="width: 80px;">Gambar</th>
                        <th class="text-center align-middle" style="width: 140px;">Kode Produk</th>
                        <th class="align-middle">Nama Produk</th>
                        <th class="text-right align-middle" style="width: 110px;">Harga</th>
                        <th class="text-center align-middle" style="width: 70px;">Stok</th>
                        <th class="align-middle">Deskripsi</th>
                        <th class="align-middle" style="width: 120px;">Kategori</th>
                        <th class="text-center align-middle" style="width: 190px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $item)
                    <tr>
                        <td class="text-center align-middle">{{ $loop->iteration }}</td>
                        <td class="text-center align-middle">
                            @if(!empty($item->gambar))
                                <img src="{{ asset('storage/' . $item->gambar) }}" alt="{{ $item->nama_produk }}" class="img-thumbnail" style="width: 55px; height: 55px; object-fit: cover;">
                            @else
                                <span class="badge badge-light border text-muted">No Image</span>
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            @if(!empty($item->kode_produk))
                                <div class="d-inline-block">
                                    {!! DNS2D::getBarcodeHTML($item->kode_produk, 'QRCODE', 4, 4) !!} 
                                </div>
                            @else
                                -
                            @endif
                        </td>  
                        <td class="align-middle font-weight-bold">{{ $item->nama_produk }}</td>
                        <td class="text-right align-middle">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                        <td class="text-center align-middle">{{ $item->stok }}</td>
                        <td class="align-middle">{{ $item->deskripsi ?? '-' }}</td>
                        <td class="align-middle">
                            <span class="badge badge-primary">{{ $item->kategori->nama_kategori ?? 'Tanpa Kategori' }}</span>
                        </td>
                        <td class="text-center align-middle text-nowrap">
                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#hapus{{ $item->id }}" data-bs-toggle="modal" data-bs-target="#hapus{{ $item->id }}">
                                Hapus
                            </button>
                            <a href="{{ route('produk.edit', $item->id) }}" class="btn btn-warning btn-sm text-white">Edit</a>
                            <a href="{{ route('produk.show', $item->id) }}" class="btn btn-info btn-sm">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center align-middle">Data tidak ditemukan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Hapus -->
@foreach($data as $item)
<div class="modal fade" id="hapus{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="modalHapusLabel{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="/produk/{{ $item->id }}" method="POST" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-header">
                <h5 class="modal-title" id="modalHapusLabel{{ $item->id }}">Konfirmasi Hapus Data</h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus produk <strong>{{ $item->nama_produk }}</strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Hapus Data</button>
            </div>
        </form>
    </div>
</div>
@endforeach

@endsection
