<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_losses', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('master_produk_id')->constrained('master_produk')->cascadeOnDelete();
            $table->integer('qty');
            $table->string('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_losses');
    }
};
