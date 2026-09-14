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
        Schema::create('kapal', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kapal', 20)->unique();
            $table->string('nama_kapal', 100);
            $table->foreignId('nelayan_id')->nullable()->constrained('nelayan')->nullOnDelete();
            $table->enum('jenis_kapal', ['tanpa_motor', 'motor_tempel', 'kapal_motor']);
            $table->decimal('gt', 8, 2)->nullable();
            $table->string('no_sipi', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kapal');
    }
};
