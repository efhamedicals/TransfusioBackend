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
        Schema::create('renew_stock_center_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedbigInteger('renew_stock_center_id');
            $table->foreign('renew_stock_center_id')->references('id')->on('renew_stock_centers');
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
        Schema::dropIfExists('renew_stock_center_items');
    }
};
