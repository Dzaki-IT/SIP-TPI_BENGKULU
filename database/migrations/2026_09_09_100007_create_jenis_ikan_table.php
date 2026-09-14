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
        Schema::create('jenis_ikan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_ikan', 20)->unique();
            $table->string('nama_resmi', 100);
            $table->string('nama_lokal', 100)->nullable();
            $table->string('kategori', 100)->nullable();
            $table->string('satuan', 20)->default('Kg');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_ikan');
    }
};
