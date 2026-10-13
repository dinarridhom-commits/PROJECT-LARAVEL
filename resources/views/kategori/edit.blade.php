@extends('layout.master')

@section('title', $title)

@section('konten')

<div class="card">
    <div class="card-header">Edit Data Kategori</div>
    <div class="card-body">
        <form action="{{ route('kategori.update', $kategori->id) }}" method="POST">
            @method('PUT')
            @csrf
            <div class="row">
                <div class="col-sm-6">
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori</label>
                        <input type="text" name="nama_kategori" class="form-control"
                            value="{{ old('nama_kategori', $kategori->nama_kategori) }}">
                        @error('nama_kategori')
                        <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <input type="text" name="deskripsi" class="form-control"
                            value="{{ old('deskripsi', $kategori->deskripsi) }}">
                        @error('deskripsi')
                        <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-primary">Update Data</button>
                <a href="{{ route('kategori.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection
