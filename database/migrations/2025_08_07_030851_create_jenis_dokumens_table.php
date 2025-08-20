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
        Schema::create('jenis_dokumens', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('nama');
            $table->dateTime('waktu_unggah_mulai')->nullable();
            $table->dateTime('waktu_unggah_selesai')->nullable();
            $table->integer('batas_unggah')->default(0);
            $table->foreignId('subbagian_id')->nullable()->constrained('subbagians', 'id')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_dokumens');
    }
};
