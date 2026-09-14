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
        Schema::create('nelayan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_nelayan', 20)->unique();
            $table->string('nama', 100);
            $table->string('nik', 16)->nullable()->unique();
            $table->text('alamat')->nullable();
            $table->string('kelompok_nelayan', 100)->nullable();
            $table->foreignId('tpi_id')->nullable()->constrained('tpi')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nelayan');
    }
};
