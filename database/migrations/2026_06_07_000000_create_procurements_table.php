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
        Schema::create('procurements', function (Blueprint $table) {
            $table->id();
            $table->string('no');
            $table->string('rp_number')->unique();
            $table->string('description');
            $table->string('date_created');
            $table->string('send_for_approval_general_director')->nullable();
            $table->string('buyer')->nullable();
            $table->string('te_in')->nullable();
            $table->string('te_out')->nullable();
            $table->string('re_te')->nullable();
            $table->string('po')->nullable();
            $table->string('vendor')->nullable();
            $table->string('delivery')->nullable();
            $table->string('so')->nullable();
            $table->string('qc')->nullable();
            $table->string('rr')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procurements');
    }
};
