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
        Schema::create('pembelian', function (Blueprint $table) {
            $table->id();
            $table->string('no_faktur')->unique();
            $table->string('no_po')->nullable();
            $table->date('tanggal');
            $table->foreignId('pemasok_id')->constrained('pemasok')->cascadeOnUpdate();
            $table->text('catatan')->nullable();
            $table->decimal('pajak', 8, 2)->default(0);           // persen
            $table->decimal('biaya_kirim', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->date('jatuh_tempo')->nullable();
            $table->enum('status_pembayaran', ['Belum Lunas','Lunas'])->default('Belum Lunas');
            $table->enum('status', ['aktif','batal'])->default('aktif');
            $table->timestamp('approved_at')->nullable();         
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelians');
    }
};
