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
        Schema::create('verification_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaratan_id')->constrained('pendaratan')->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('action', ['diajukan', 'disetujui', 'ditolak']);
            $table->string('from_status', 20)->nullable();
            $table->string('to_status', 20);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['pendaratan_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verification_histories');
    }
};
