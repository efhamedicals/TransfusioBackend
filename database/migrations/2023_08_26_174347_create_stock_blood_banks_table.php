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
        Schema::create('stock_blood_banks', function (Blueprint $table) {
            $table->id();
            $table->unsignedbigInteger('blood_bank_id');
            $table->foreign('blood_bank_id')->references('id')->on('blood_banks');
            $table->unsignedbigInteger('type_blood_id');
            $table->foreign('type_blood_id')->references('id')->on('type_bloods');
            $table->unsignedBigInteger('quantity')->default(0);
            $table->unsignedBigInteger('safety')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_blood_banks');
    }
};
