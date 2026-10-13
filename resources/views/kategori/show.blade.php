@extends('layout.master')

@section('title', $title)

@section('konten')
<h1 class="h3 mb-4 text-gray-800">Daftar Kategori</h1>

<a href="{{ route('kategori.create') }}" class="btn btn-primary mb-3">Tambah Data</a>

@if(session('message'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('message') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-gray-800">Daftar Kategori</h6>
        <form action="/kategori" method="GET" class="form-inline">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Cari data kategori" value="{{ request('search') }}">
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
                        <th class="align-middle">Nama Kategori</th>
                        <th class="align-middle">Deskripsi</th>
                        <th class="text-center align-middle" style="width: 190px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kategori as $kat)
                    <tr>
                        <td class="text-center align-middle">{{ $loop->iteration }}</td>
                        <td class="align-middle">{{ $kat->nama_kategori }}</td>
                        <td class="align-middle">{{ $kat->deskripsi ?? '-' }}</td>
                        <td class="text-center align-middle text-nowrap">
                            <button type="button" class="btn btn-danger btn-sm"
                                data-toggle="modal" data-target="#hapus{{ $kat->id }}">
                                Hapus
                            </button>
                            <a href="{{ route('kategori.edit', $kat->id) }}" class="btn btn-warning btn-sm text-white">Edit</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center align-middle">Data tidak ditemukan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Hapus --}}
@foreach($kategori as $kat)
<div class="modal fade" id="hapus{{ $kat->id }}" tabindex="-1" role="dialog"
    aria-labelledby="modalHapusLabel{{ $kat->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{ route('kategori.destroy', $kat->id) }}" method="POST" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-header">
                <h5 class="modal-title" id="modalHapusLabel{{ $kat->id }}">Konfirmasi Hapus Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus kategori <strong>{{ $kat->nama_kategori }}</strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Hapus Data</button>
            </div>
        </form>
    </div>
</div>
@endforeach

@endsection
