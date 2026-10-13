<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    // memanggil package laravel
    protected $table = 'produks';

    // kolom yang boleh diisi (mass assignment)
    protected $fillable = [
        'kode_produk',
        'nama_produk',
        'harga',
        'stok',
        'deskripsi',
        'kategori_id',
        'gambar',
    ];

    // relasi ke tabel kategori (belongsTo)
    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }
}

