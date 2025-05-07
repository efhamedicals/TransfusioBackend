<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\TypeProductBlood;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('type_product_bloods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('status')->default(1);
            $table->timestamps();
        });

        TypeProductBlood::create([
            'name' => 'Culot globulaire',
            'status' => 1
        ]);
        TypeProductBlood::create([
            'name' => 'Plasma frais congelé',
            'status' => 1
        ]);
        TypeProductBlood::create([
            'name' => 'Concentrés de standards de plaquettes',
            'status' => 1
        ]);
        TypeProductBlood::create([
            'name' => 'Sang total',
            'status' => 1
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('type_product_bloods');
    }
};
