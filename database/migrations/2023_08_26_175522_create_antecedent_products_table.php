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
        Schema::create('antecedent_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedbigInteger('antecedent_id');
            $table->foreign('antecedent_id')->references('id')->on('antecedents');
            $table->unsignedbigInteger('type_product_blood_id');
            $table->foreign('type_product_blood_id')->references('id')->on('type_product_bloods');
            $table->unsignedBigInteger('format')->default(1); //1:Adulte | 2:Pediatrique
            $table->string('blood_type')->nullable();
            $table->string('rhesus')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('antecedent_products');
    }
};
