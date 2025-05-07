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
        Schema::create('transfusions', function (Blueprint $table) {
            $table->id();
            $table->string('image');
            $table->string('reference');
            $table->unsignedbigInteger('quantity');
            $table->unsignedbigInteger('rythm');
            $table->unsignedbigInteger('hemo_file');
            $table->unsignedbigInteger('prescription_id');
            $table->foreign('prescription_id')->references('id')->on('prescriptions');
            $table->unsignedBigInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfusions');
    }
};
