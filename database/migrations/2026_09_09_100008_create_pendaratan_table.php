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
        Schema::create('pendaratan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pendaratan', 30)->unique();
            $table->foreignId('tpi_id')->constrained('tpi')->cascadeOnDelete();
            $table->date('tanggal_pendaratan');
            $table->foreignId('kapal_id')->nullable()->constrained('kapal')->nullOnDelete();
            $table->foreignId('nelayan_id')->nullable()->constrained('nelayan')->nullOnDelete();
            $table->foreignId('alat_tangkap_id')->nullable()->constrained('alat_tangkap')->nullOnDelete();
            $table->enum('status_verifikasi', ['draft', 'diajukan', 'diverifikasi', 'ditolak'])->default('draft');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('verified_at')->nullable();
            $table->text('catatan_verifikasi')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaratan');
    }
};
