<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloodBank extends Model
{
    use HasFactory;

    protected $fillable = [
        'hospital_id',
        'status',
    ];

    // Relation avec l'hôpital
    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function employees()
    {
        return $this->hasMany(User::class, 'blood_bank_id', 'id');
    }
}
