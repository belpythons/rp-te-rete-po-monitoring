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
        Schema::table('procurements', function (Blueprint $table) {
            $table->string('quantity')->nullable()->after('vendor');
            $table->string('departemen')->nullable()->after('quantity');
            $table->text('keterangan')->nullable()->after('departemen');
            $table->text('hasil_evaluasi')->nullable()->after('keterangan');
            $table->text('catatan')->nullable()->after('hasil_evaluasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('procurements', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'departemen', 'keterangan', 'hasil_evaluasi', 'catatan']);
        });
    }
};
