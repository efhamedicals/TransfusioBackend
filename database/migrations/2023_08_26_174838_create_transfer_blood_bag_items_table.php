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
        Schema::create('transfer_blood_bag_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedbigInteger('transfer_blood_bag_id');
            $table->foreign('transfer_blood_bag_id')->references('id')->on('transfer_blood_bags');
            $table->unsignedbigInteger('type_blood_id');
            $table->foreign('type_blood_id')->references('id')->on('type_bloods');
            $table->unsignedBigInteger('quantity')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_blood_bag_items');
    }
};
