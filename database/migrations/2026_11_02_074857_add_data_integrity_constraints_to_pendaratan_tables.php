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
        Schema::create('landing_number_sequences', function (Blueprint $table) {
            $table->date('sequence_date')->primary();
            $table->unsignedInteger('last_number')->default(0);
            $table->timestamps();
        });

        Schema::table('detail_pendaratan', function (Blueprint $table) {
            $table->unique(['pendaratan_id', 'jenis_ikan_id'], 'detail_pendaratan_landing_fish_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_pendaratan', function (Blueprint $table) {
            $table->dropUnique('detail_pendaratan_landing_fish_unique');
        });

        Schema::dropIfExists('landing_number_sequences');
    }
};
