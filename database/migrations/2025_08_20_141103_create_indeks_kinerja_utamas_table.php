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
            $table->string('tahun');
            $table->enum('periode', ['Triwulan I', 'Triwulan II', 'Triwulan III', 'Triwulan IV']);
            $table->unsignedSmallInteger('nilai_bulan_1')->nullable();
            $table->unsignedSmallInteger('nilai_bulan_2')->nullable();
            $table->unsignedSmallInteger('nilai_bulan_3')->nullable();
            $table->unsignedInteger('perubahan_ke')->default(1);

            $table->auditColumns();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['tahun', 'periode', 'perubahan_ke', 'created_at']);
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
