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
        Schema::create('detail_pendaratan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaratan_id')->constrained('pendaratan')->cascadeOnDelete();
            $table->foreignId('jenis_ikan_id')->constrained('jenis_ikan')->cascadeOnDelete();
            $table->decimal('volume_kg', 12, 2);
            $table->decimal('harga_per_kg', 12, 2)->nullable();
            $table->decimal('nilai_produksi', 15, 2)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pendaratan');
    }
};
