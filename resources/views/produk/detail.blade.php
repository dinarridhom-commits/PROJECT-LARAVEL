@extends('layout.master')

@section('title', $title)

@section('konten')

<h1 class="h3 mb-4 text-gray-800">Detail Produk</h1>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Informasi Produk</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 text-center mb-3">
                @if(!empty($produk->gambar))
                    <img src="{{ asset('storage/' . $produk->gambar) }}" alt="{{ $produk->nama_produk }}" class="img-fluid rounded shadow-sm" style="max-height: 280px; object-fit: cover;">
                @else
                    <div class="border rounded d-flex align-items-center justify-content-center bg-light text-muted" style="height: 220px;">
                        <i class="fas fa-image fa-3x mr-2"></i> Belum ada foto
                    </div>
                @endif
            </div>
            <div class="col-md-8">
                <table class="table table-borderless">
                    <tr>
                        <th width="150">Kode Produk</th>
                        <td>: {{ $produk->kode_produk ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Nama Produk</th>
                        <td class="font-weight-bold">: {{ $produk->nama_produk }}</td>
                    </tr>
                    <tr>
                        <th>Kategori</th>
                        <td>: <span class="badge badge-primary">{{ $produk->kategori->nama_kategori ?? 'Tanpa Kategori' }}</span></td>
                    </tr>
                    <tr>
                        <th>Harga</th>
                        <td>: Rp {{ number_format($produk->harga, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Stok</th>
                        <td>: {{ $produk->stok }} Unit</td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        <td>: {{ $produk->deskripsi ?? '-' }}</td>
                    </tr>
                </table>
                <a href="/produk" class="btn btn-secondary mt-3">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Produk
                </a>
                <a href="{{ route('produk.edit', $produk->id) }}" class="btn btn-warning mt-3 text-white">
                    <i class="fas fa-edit mr-1"></i> Edit Produk
                </a>
            </div>
        </div>
    </div>
</div>
@endsection