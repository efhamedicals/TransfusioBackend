<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\TypeBlood;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('type_bloods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('status')->default(1);
            $table->timestamps();
        });

        TypeBlood::create([
            'name' => 'A+',
            'status' => 1
        ]);
        TypeBlood::create([
            'name' => 'A-',
            'status' => 1
        ]);
        TypeBlood::create([
            'name' => 'B+',
            'status' => 1
        ]);
        TypeBlood::create([
            'name' => 'B-',
            'status' => 1
        ]);
        TypeBlood::create([
            'name' => 'AB+',
            'status' => 1
        ]);
        TypeBlood::create([
            'name' => 'AB-',
            'status' => 1
        ]);
        TypeBlood::create([
            'name' => 'O+',
            'status' => 1
        ]);
        TypeBlood::create([
            'name' => 'O-',
            'status' => 1
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('type_bloods');
    }
};
