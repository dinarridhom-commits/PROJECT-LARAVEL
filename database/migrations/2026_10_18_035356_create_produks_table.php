<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Kategori;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // membuat tabel di database
        
        Schema::create('produks', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Kategori::class);
            $table->string('kode_produk');
            $table->text('deskripsi')->nullable();
            $table->string('nama_produk');
            $table->bigInteger('harga');
            $table->integer('stok');
            $table->timestamps();
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};

