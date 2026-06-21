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
            $table->string('no')->nullable();
            $table->string('rp_number')->nullable()->unique();
            $table->text('description')->nullable();
            $table->date('date_created')->nullable();
            $table->date('send_for_approval_general_director')->nullable();
            $table->string('buyer')->nullable();
            $table->date('te_in')->nullable();
            $table->date('te_out')->nullable();
            $table->date('re_te')->nullable();
            $table->string('po')->nullable();
            $table->string('vendor')->nullable();
            $table->date('delivery')->nullable();
            $table->string('so')->nullable();
            $table->string('qc')->nullable();
            $table->string('rr')->nullable();
            $table->string('status')->nullable()->default('Pending');
            $table->string('phase')->nullable();
            $table->date('tanggal_masuk')->nullable();
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
