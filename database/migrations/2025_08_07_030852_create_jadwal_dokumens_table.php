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
        Schema::create('jadwal_dokumens', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('kode')->unique();
            $table->foreignId('jenis_dokumen_id')->nullable()->constrained('jenis_dokumens')->nullOnDelete();
            $table->dateTime('waktu_unggah_mulai')->nullable();
            $table->dateTime('waktu_unggah_selesai')->nullable();
            $table->boolean('aktif')->default(false);

            $table->auditColumns();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['waktu_unggah_mulai', 'waktu_unggah_selesai', 'aktif', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_dokumens');
    }
};
