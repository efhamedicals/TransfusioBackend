<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'short_name',
        'geolocation_id',
        'phone',
        'email',
        'avatar',
        'status',
    ];

    // Relation avec la géolocalisation
    public function geolocation()
    {
        return $this->belongsTo(Geolocation::class);
    }

    public function doctors()
    {
        return $this->hasMany(User::class, 'hospital_id', 'id');
    }
}
