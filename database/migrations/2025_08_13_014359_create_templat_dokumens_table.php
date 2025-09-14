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
        Schema::create('templat_dokumens', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('jenis_dokumen_id')->nullable()->constrained('jenis_dokumens')->nullOnDelete();
            $table->string('nama');

            $table->auditColumns();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['nama', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('templat_dokumens');
    }
};
