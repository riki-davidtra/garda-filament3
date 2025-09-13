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
            $table->integer('batas_unggah')->default(0);
            $table->string('format_file')->nullable();
            $table->integer('maksimal_ukuran')->default(20480);
            $table->boolean('mode_status')->default(false);
            $table->boolean('mode_subkegiatan')->default(false);
            $table->boolean('mode_periode')->default(false);
            $table->timestamps();

            $table->index(['nama', 'created_at']);
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
