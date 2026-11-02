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
        Schema::table('alat_tangkap', function (Blueprint $table) {
            $table->foreignId('nelayan_id')->nullable()->after('jenis_kategori')->constrained('nelayan')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alat_tangkap', function (Blueprint $table) {
            $table->dropForeign(['nelayan_id']);
            $table->dropColumn('nelayan_id');
        });
    }
};
