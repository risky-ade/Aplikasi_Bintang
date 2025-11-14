<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('retur_penjualan_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('retur_penjualan_id');
            $table->unsignedBigInteger('produk_id');
            $table->integer('qty_retur');
            $table->integer('harga_jual');
            $table->integer('subtotal');
            $table->timestamps();

            $table->foreign('retur_penjualan_id')->references('id')->on('retur_penjualan')->onDelete('cascade');
            $table->foreign('produk_id')->references('id')->on('master_produk')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retur_penjualan_detail');
    }
};
