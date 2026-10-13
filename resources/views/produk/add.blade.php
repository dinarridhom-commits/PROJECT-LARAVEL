@extends('layout.master')

@section('title', $title)

@section('konten')

<div class="card">
    <div class="card-header font-weight-bold text-primary">Tambah Data Produk</div>
    <div class="card-body">
        <form action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-sm-4">
                    <div class="mb-3">
                        <label class="form-label">Kode Produk</label>
                        <input type="text" name="kode_produk" class="form-control" value="{{ old('kode_produk') }}" placeholder="Otomatis jika dikosongkan">
                        @error('kode_produk')
                        <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="mb-3">
                        <label class="form-label">Nama Produk</label>
                        <input type="text" name="nama_produk" class="form-control" value="{{ old('nama_produk') }}">
                        @error('nama_produk')
                        <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="kategori_id" class="form-control">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategori as $kat)
                                <option value="{{ $kat->id }}" {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>
                                    {{ $kat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                        @error('kategori_id')
                        <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div> 
                </div>

                <div class="col-sm-6">
                    <div class="mb-3">
                        <label class="form-label">Harga (Rp)</label>
                        <input type="number" name="harga" class="form-control" value="{{ old('harga') }}">
                        @error('harga')
                        <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div> 
                </div>

                <div class="col-sm-6">
                    <div class="mb-3">
                        <label class="form-label">Stok</label>
                        <input type="number" name="stok" class="form-control" value="{{ old('stok') }}">
                        @error('stok')
                        <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div> 
                </div>

                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label">Foto / Gambar Produk</label>
                        <input type="file" name="gambar" class="form-control" accept="image/*">
                        <small class="text-muted">Format: JPG, JPEG, PNG, WEBP (Maksimal 2MB)</small>
                        @error('gambar')
                        <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                        <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">Tambah Data</button>
                    <a href="{{ route('produk.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection 

