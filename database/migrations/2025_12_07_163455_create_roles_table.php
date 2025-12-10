<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label')->nullable();
            $table->timestamps();
        });

        Schema::create('permissions', function(Blueprint $table){
            $table->id();
            $table->string('name')->unique();
            $table->string('label')->nullable();
            $table->timestamps();
        });
        //pivot role_permission (many-to-many)
        Schema::create('role_permission', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id');
            $table->primary(['role_id', 'permission_id']);
        });

        // tambah kolom role_id ke users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->after('id')
                ->nullable()
                ->constrained('roles');
        });
        
        // seed role dasar
        DB::table('roles')->insert([
            ['name' => 'superadmin', 'label' => 'Super Admin', 'created_at'=>now(), 'updated_at'=>now()],
            ['name' => 'admin',      'label' => 'Admin',       'created_at'=>now(), 'updated_at'=>now()],
            ['name' => 'staf',       'label' => 'Staf',        'created_at'=>now(), 'updated_at'=>now()],
        ]);

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
                $table->dropConstrainedForeignId('role_id');
        });
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
