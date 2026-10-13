@extends('layout.master')

@section('content')
<h1>Daftar Produk Kami</h1>
<hr>
<a href="/produk/create" class="btn btn-primary mb-3">Tambah Data</a>
<div class="card">
    <div class="card-header">
        Daftar Produk
    </div>
  <div class="card-body">
    <table class=" table table-striped table-bordered">
  <thead>
    <tr>
      <th scope="col">No</th>
      <th scope="col">Nama Produk</th>
      <th scope="col">Harga</th>
      <th scope="col">Stok</th>
      <th scope="col">Aksi</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">1</th>
      <td>Kemeja Polos</td>
      <td>100000</td>
      <td>10</td>
      <td>
        <button type="button" class="btn btn-success">Edit</button>
        <button type="button" class="btn btn-danger">Hapus</button>
      </td>
    </tr>
    <tr>
      <th scope="row">2</th>
      <td>kaos Polos</td>
      <td>80000</td>
      <td>20</td>
      <td>
        <button type="button" class="btn btn-success">Edit</button>
        <button type="button" class="btn btn-danger">Hapus</button>
      </td>
    </tr>
    <tr>
      <th scope="row">3</th>
      <td>Levis Premium</td>
      <td>200000</td>
      <td>15</td>
      <td>
        <button type="button" class="btn btn-success">Edit</button>
        <button type="button" class="btn btn-danger">Hapus</button>
      </td>
    </tr>
  </tbody>
</table>
  </div>
</div>
@endsection