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
        Schema::create('tpi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tpi', 20)->unique();
            $table->string('nama_tpi', 100);
            $table->foreignId('wilayah_id')->constrained('wilayah')->cascadeOnDelete();
            $table->text('alamat')->nullable();
            $table->string('instansi_pengelola', 100)->nullable();
            $table->enum('jenis_perairan', ['laut'])->default('laut');
            $table->enum('status_operasi', ['aktif', 'tidak_aktif'])->default('aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tpi');
    }
};
