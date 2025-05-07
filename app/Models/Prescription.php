<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'hospital_id',
        'patient_id',
        'user_id',
        'rai',
        'status','blood_bag_id'
    ];

    // Relation avec l'hôpital
    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    // Relation avec le patient
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    // Relation avec le patient
    public function bloodBag()
    {
        return $this->belongsTo(BloodBag::class);
    }

    // Relation avec l'utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(PrescriptionProduct::class, 'prescription_id', 'id');
    }
}
