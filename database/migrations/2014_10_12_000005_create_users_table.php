<?php

use App\Models\User;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('token')->nullable();
            $table->string('password')->nullable();
            $table->string('avatar')->nullable();
            $table->boolean('email_verify')->default(false);
            $table->boolean('phone_verify')->default(false);
            $table->string('address')->nullable();
            $table->string('device_token')->nullable();
            $table->unsignedBigInteger('type_user')->default(1); //1:Transporter | 2:Doctor | 3:BloodBank | 4:CTS | 5:User
            $table->unsignedbigInteger('hospital_id')->nullable();
            $table->foreign('hospital_id')->references('id')->on('hospitals');
            $table->unsignedbigInteger('blood_bank_id')->nullable();
            $table->foreign('blood_bank_id')->references('id')->on('blood_banks');
            $table->unsignedbigInteger('blood_center_id')->nullable();
            $table->foreign('blood_center_id')->references('id')->on('blood_centers');
            $table->unsignedbigInteger('assurance_id')->nullable();
            $table->foreign('assurance_id')->references('id')->on('assurances');
            $table->unsignedBigInteger('status')->default(1);
            $table->rememberToken();
            $table->timestamps();
        });

        User::create([
            'last_name' => 'KOSSI',
            'first_name' => 'Désiré',
            'phone' => '+22891091201',
            'email' => 'kossidesire@gmail.com',
            'avatar' => '/avatars/desire.png',
            'token' => getRamdomText(20),
            'password' => bcrypt('123456789'),
            'type_user' => 1,
            'status' => 1,
        ]);
        User::create([
            'last_name' => 'ODANOU',
            'first_name' => 'Chabane',
            'phone' => '+22890129012',
            'email' => 'drchabane@chutokoin.tg',
            'avatar' => '/avatars/chutokoin.png',
            'token' => getRamdomText(20),
            'password' => bcrypt('123456789'),
            'type_user' => 2,
            'hospital_id' => 1,
            'status' => 1,
        ]);
        User::create([
            'last_name' => 'SANNI',
            'first_name' => 'Yasmine',
            'phone' => '+228901298121',
            'email' => 'bloodbank@chutokoin.tg',
            'avatar' => '/avatars/chutokoin.png',
            'token' => getRamdomText(20),
            'password' => bcrypt('123456789'),
            'type_user' => 3,
            'blood_bank_id' => 1,
            'status' => 1,
        ]);
        User::create([
            'last_name' => 'AYITEVI',
            'first_name' => 'Firmin',
            'phone' => '+22890129812',
            'email' => 'admin@cnts.tg',
            'avatar' => '/avatars/cnts.png',
            'token' => getRamdomText(20),
            'password' => bcrypt('123456789'),
            'type_user' => 4,
            'blood_center_id' => 1,
            'status' => 1,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
