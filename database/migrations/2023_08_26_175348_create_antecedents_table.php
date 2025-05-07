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
        Schema::create('antecedents', function (Blueprint $table) {
            $table->id();
            $table->date('date_antecedent')->nullable();
            $table->unsignedBigInteger('clinic')->default(1); //1:Grossesse | 2:Autre
            $table->unsignedBigInteger('result_treatment')->default(1); //1:Bon | 2:Mauvais
            $table->text('treatments')->nullable();
            $table->text('results_treatments')->nullable();
            $table->unsignedbigInteger('patient_id');
            $table->foreign('patient_id')->references('id')->on('patients');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('antecedents');
    }
};
