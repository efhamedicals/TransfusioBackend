<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'last_name',
        'married_name',
        'first_name',
        'gender',
        'birth',
        'academic_level',
        'blood_type',
        'rhesus',
        'email',
        'phone',
        'photo',
        'cni',
        'count_pregnancies',
        'hospital_id',
        'blood_bank_id',
        'status',
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
}
