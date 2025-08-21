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
        Schema::create('indeks_kinerja_utamas', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('indikator_id')->nullable()->constrained('indikators')->nullOnDelete();
            $table->enum('periode', ['Triwulan I', 'Triwulan II', 'Triwulan III', 'Triwulan IV']);
            $table->unsignedSmallInteger('nilai_bulan_1')->nullable();
            $table->unsignedSmallInteger('nilai_bulan_2')->nullable();
            $table->unsignedSmallInteger('nilai_bulan_3')->nullable();
            $table->unsignedInteger('perubahan_ke')->default(1);
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
        Schema::dropIfExists('indeks_kinerja_utamas');
    }
};
