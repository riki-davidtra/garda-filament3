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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('path')->nullable();
            $table->string('nama')->nullable();
            $table->string('tipe')->nullable();
            $table->string('ukuran')->nullable();
            $table->string('versi')->nullable();
            $table->string('tag')->nullable();
            $table->nullableMorphs('model');
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

            $table->index([
                'nama',
                'tag',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
