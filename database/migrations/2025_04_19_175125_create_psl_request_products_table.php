<?php

use App\Models\PslRequest;
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
        Schema::create('psl_request_products', function (Blueprint $table) {
            $table->id();
            $table->enum('name', ['adult_unit_red_blood', 'children_unit_red_blood', 'standard_platelet_concentrate', 'fresh_frozen_plasma'])->default('adult_unit_red_blood');
            $table->string('blood_type')->nullable();
            $table->string('blood_rh')->nullable();
            $table->integer('count')->nullable();
            $table->foreignIdFor(PslRequest::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('psl_request_products');
    }
};
