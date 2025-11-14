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
        Schema::table('retur_penjualan_detail', function (Blueprint $table) {
            $table->decimal('diskon_unit', 15, 2)->default(0)->after('harga_jual');
            $table->decimal('harga_unit', 15, 2)->nullable()->after('diskon_unit'); // opsional
            $table->unsignedBigInteger('penjualan_detail_id')->nullable()->after('retur_penjualan_id'); // opsional
            $table->foreign('penjualan_detail_id')->references('id')->on('penjualan_detail')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('retur_penjualan_detail', function (Blueprint $table) {
            $table->dropForeign(['penjualan_detail_id']);
            $table->dropColumn(['diskon_unit','harga_unit','penjualan_detail_id']);
        });
    }
};
