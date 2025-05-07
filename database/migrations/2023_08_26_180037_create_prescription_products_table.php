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
        Schema::create('prescription_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedbigInteger('type_product_blood_id');
            $table->foreign('type_product_blood_id')->references('id')->on('type_product_bloods');
            $table->unsignedBigInteger('count_bags')->default(0);
            $table->unsignedBigInteger('priority')->default(1); //1:Vitale immédiate | 2:Vitale | 3:relative | 4:Non urgent
            $table->unsignedBigInteger('format')->default(1); //1:Adulte | 2:Pediatrique
            $table->unsignedBigInteger('is_chirurgical')->default(0); //0:Non | 1:Oui
            $table->unsignedBigInteger('is_replace')->default(0); //0:Non | 1:Oui
            $table->text('justifications')->nullable();
            $table->text('indications')->nullable();
            $table->text('instructions')->nullable();
            $table->unsignedbigInteger('prescription_id');
            $table->foreign('prescription_id')->references('id')->on('prescriptions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescription_products');
    }
};
