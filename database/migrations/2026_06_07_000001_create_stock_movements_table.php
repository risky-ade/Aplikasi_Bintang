<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_produk_id')->constrained('master_produk')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('deskripsi');
            $table->integer('qty_masuk')->default(0);
            $table->integer('qty_keluar')->default(0);
            $table->integer('sisa')->default(0);
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->index(['master_produk_id', 'tanggal']);
            $table->index(['reference_type', 'reference_id']);
        });

        $now = now();
        $products = DB::table('master_produk')->select('id', 'stok')->get();

        foreach ($products as $product) {
            $stok = (int) ($product->stok ?? 0);

            if ($stok === 0) {
                continue;
            }

            DB::table('stock_movements')->insert([
                'master_produk_id' => $product->id,
                'tanggal' => $now->toDateString(),
                'deskripsi' => 'Saldo Awal Input Produk',
                'qty_masuk' => $stok,
                'qty_keluar' => 0,
                'sisa' => $stok,
                'reference_type' => 'saldo_awal',
                'reference_id' => $product->id,
                'created_by' => null,
                'keterangan' => 'Saldo awal dari stok master produk saat fitur mutasi dibuat.',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
