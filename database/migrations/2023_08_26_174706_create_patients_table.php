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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('last_name')->nullable();
            $table->string('married_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('gender')->nullable();
            $table->date('birth')->nullable();
            $table->string('academic_level')->nullable();
            $table->string('blood_type')->nullable();
            $table->string('rhesus')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('photo')->nullable();
            $table->string('cni')->nullable();
            $table->unsignedBigInteger('count_pregnancies')->default(0);
            $table->unsignedbigInteger('hospital_id')->nullable();
            $table->foreign('hospital_id')->references('id')->on('hospitals');
            $table->unsignedbigInteger('blood_bank_id')->nullable();
            $table->unsignedBigInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
