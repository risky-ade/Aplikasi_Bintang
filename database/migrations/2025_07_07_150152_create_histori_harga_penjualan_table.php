<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('histori_harga_penjualan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('master_produk');
            $table->foreignId('pelanggan_id')->nullable()->constrained('pelanggan');
            $table->enum('sumber', ['produk', 'penjualan']);
            $table->integer('harga_lama')->nullable();
            $table->integer('harga_baru');
            $table->date('tanggal')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('histori_harga_penjualan');
    }
};
