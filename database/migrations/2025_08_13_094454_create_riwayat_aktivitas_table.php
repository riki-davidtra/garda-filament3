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
        Schema::create('riwayat_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('aksi');
            $table->string('jenis_data')->nullable();
            $table->text('deskripsi')->nullable();
            $table->text('detail_data')->nullable();
            $table->ipAddress('ip')->nullable();
            $table->nullableMorphs('subjek');
            $table->timestamps();

            $table->index(['user_id', 'aksi', 'deskripsi', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_aktivitas');
    }
};
