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
        Schema::create('blood_bags', function (Blueprint $table) {
            $table->id();
            $table->unsignedbigInteger('blood_center_id');
            $table->foreign('blood_center_id')->references('id')->on('blood_centers');
            $table->unsignedbigInteger('blood_bank_id')->nullable();
            $table->foreign('blood_bank_id')->references('id')->on('blood_banks');
            $table->unsignedbigInteger('type_product_blood_id');
            $table->foreign('type_product_blood_id')->references('id')->on('type_product_bloods');
            $table->unsignedbigInteger('type_blood_id')->nullable();
            $table->foreign('type_blood_id')->references('id')->on('type_bloods');
            $table->string('reference')->nullable();
            $table->integer('price')->nullable();
            $table->date('date_expiration')->nullable();
            $table->unsignedBigInteger('format')->default(1); //1:Adulte | 2:Pédiatrie
            $table->unsignedBigInteger('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_bags');
    }
};
