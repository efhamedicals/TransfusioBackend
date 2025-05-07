<?php

use App\Models\BloodCenter;
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
        Schema::create('blood_centers', function (Blueprint $table) {
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

        BloodCenter::create([
            'name' => 'Centre National de Transfusion Sanguine',
            'short_name' => 'CNTS',
            'geolocation_id' =>  2,
            'phone' => '21350712',
            'email' => 'contact@cnts.tg',
            'avatar' => '/avatars/cnts.png',
            'status' => 1,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_centers');
    }
};
