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
            $table->date('tanggal_out')->nullable()->after('tanggal_po');
            $table->date('tanggal_in')->nullable()->after('tanggal_out');
        });

        // Disable foreign key constraints during table drop to avoid constraint violations
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('technical_evaluations');
        Schema::dropIfExists('re_technical_evaluations');
        Schema::dropIfExists('request_purchasings');
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('procurements', function (Blueprint $table) {
            $table->dropColumn(['tanggal_out', 'tanggal_in']);
        });
    }
};
