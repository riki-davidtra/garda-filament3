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
        Schema::create('dokumens', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('jenis_dokumen_id')->nullable()->constrained('jenis_dokumens')->nullOnDelete();
            $table->foreignId('jadwal_dokumen_id')->nullable()->constrained('jadwal_dokumens')->nullOnDelete();
            $table->foreignId('subbagian_id')->nullable()->constrained('subbagians')->nullOnDelete();
            $table->foreignId('subkegiatan_id')->nullable()->constrained('subkegiatans')->nullOnDelete();
            $table->string('nama');
            $table->string('tahun')->nullable();
            $table->string('periode')->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status', [
                'Menunggu Persetujuan',
                'Diterima',
                'Ditolak',
                'Revisi Menunggu Persetujuan',
                'Revisi Diterima',
                'Revisi Ditolak'
            ])->default('Menunggu Persetujuan');
            $table->text('komentar')->nullable();
            $table->auditColumns();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['tahun', 'periode', 'status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumens');
    }
};
