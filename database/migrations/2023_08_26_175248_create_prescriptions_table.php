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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->string('reference');
            $table->unsignedbigInteger('hospital_id')->nullable();
            $table->foreign('hospital_id')->references('id')->on('hospitals');
            $table->unsignedbigInteger('patient_id')->nullable();
            $table->foreign('patient_id')->references('id')->on('patients');
            $table->unsignedbigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('rai')->default(1); //0:Négatif || 1:Positif
            $table->unsignedbigInteger('blood_bag_id')->nullable();
            $table->foreign('blood_bag_id')->references('id')->on('blood_bags');
            $table->unsignedBigInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
