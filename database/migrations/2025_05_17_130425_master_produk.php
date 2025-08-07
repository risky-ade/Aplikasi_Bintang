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
        Schema::create('master_produk', function (Blueprint $table) {
            $table->id();
            $table->string('nama_produk')->unique();
            $table->foreignId('kategori_id');
            $table->foreignId('satuan_id');
            $table->text('deskripsi')->nullable();
            $table->decimal('harga_dasar',12, 2);
            $table->decimal('harga_jual', 15, 2);
            $table->boolean('include_pajak')->default(false);
            $table->integer('stok')->default('0');
            $table->integer('stok_minimal')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_produk');
    }
};
