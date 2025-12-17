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
        Schema::create('password_reset_requests', function (Blueprint $table) {
            $table->id();
            $table->string('login');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('note')->nullable();
            $table->enum('status', ['pending','done'])->default('pending');
            $table->timestamp('handled_at')->nullable();
            $table->unsignedBigInteger('handled_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_requests');
    }
};
