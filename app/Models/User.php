<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    protected $fillable = [
        'last_name',
        'first_name',
        'email',
        'phone',
        'token',
        'password',
        'avatar',
        'type_user',
        'hospital_id',
        'blood_bank_id',
        'blood_center_id',
        'status',
        'email_verify',
        'phone_verify',
        'address',
        'device_token'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relation avec l'hôpital
    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    // Relation avec la banque de sang
    public function bloodBank()
    {
        return $this->belongsTo(BloodBank::class);
    }

    // Relation avec le centre de collecte de sang
    public function bloodCenter()
    {
        return $this->belongsTo(BloodCenter::class);
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
