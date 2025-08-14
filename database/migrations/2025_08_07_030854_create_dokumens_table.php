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
            $table->foreignId('subbagian_id')->nullable()->constrained('subbagians')->nullOnDelete();
            $table->foreignId('subkegiatan_id')->nullable()->constrained('subkegiatans')->nullOnDelete();
            $table->string('nama');
            $table->string('tahun');
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
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('dibuat_pada')->nullable();
            $table->foreignId('diperbarui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('diperbarui_pada')->nullable();
            $table->foreignId('dihapus_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('dihapus_pada')->nullable();
            $table->foreignId('dipulihkan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('dipulihkan_pada')->nullable();
            $table->softDeletes();
            $table->timestamps();
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
