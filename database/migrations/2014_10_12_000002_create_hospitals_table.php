<?php

use App\Models\Hospital;
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
        Schema::create('hospitals', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('short_name')->nullable();
            $table->unsignedbigInteger('geolocation_id');
            $table->foreign('geolocation_id')->references('id')->on('geolocations');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('avatar')->nullable();
            $table->unsignedBigInteger('status')->default(1);
            $table->timestamps();
        });

        Hospital::create([
            'name' => 'Centre Hospitalier Universitaire Tokoin',
            'short_name' => 'CHU Tokoin',
            'geolocation_id' =>  1,
            'phone' => '22091281',
            'email' => 'contact@chutokoin.tg',
            'avatar' => '/avatars/chutokoin.png',
            'status' => 1,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospitals');
    }
};
