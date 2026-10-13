<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
        ]);

        $kat1 = Kategori::create([
            'nama_kategori' => 'Pakaian',
            'deskripsi'     => 'Kategori busana dan baju'
        ]);

        $kat2 = Kategori::create([
            'nama_kategori' => 'Celana',
            'deskripsi'     => 'Kategori celana pendek dan panjang'
        ]);

        Produk::create([
            'kode_produk' => 'A001',
            'nama_produk' => 'Kemeja Polos',
            'harga'       => 100000,
            'stok'        => 10,
            'deskripsi'   => 'Kemeja bahan katun premium',
            'kategori_id' => $kat1->id,
        ]);

        Produk::create([
            'kode_produk' => 'A002',
            'nama_produk' => 'Kemeja Flanel',
            'harga'       => 150000,
            'stok'        => 20,
            'deskripsi'   => 'Kemeja bahan flanel hangat',
            'kategori_id' => $kat1->id,
        ]);

        Produk::create([
            'kode_produk' => 'A003',
            'nama_produk' => 'Celana Levis',
            'harga'       => 175000,
            'stok'        => 15,
            'deskripsi'   => 'Celana jeans bahan levis asli',
            'kategori_id' => $kat2->id,
        ]);
    }
}

