<?php

use App\Models\Geolocation;
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
        Schema::create('geolocations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->timestamps();
        });

        Geolocation::create([
            'name' => 'Tokoin',
            'latitude' => 6.12912,
            'longitude' =>  1.87182
        ]);

        Geolocation::create([
            'name' => 'Nukafu',
            'latitude' => 6.13012,
            'longitude' =>  1.77182
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geolocations');
    }
};
