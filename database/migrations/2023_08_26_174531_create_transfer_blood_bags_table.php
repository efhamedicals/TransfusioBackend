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
        Schema::create('transfer_blood_bags', function (Blueprint $table) {
            $table->id();
            $table->unsignedbigInteger('blood_center_id');
            $table->foreign('blood_center_id')->references('id')->on('blood_centers');
            $table->unsignedbigInteger('blood_bank_id');
            $table->foreign('blood_bank_id')->references('id')->on('blood_banks');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_blood_bags');
    }
};
