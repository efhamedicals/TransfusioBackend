<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Antecedent extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_antecedent',
        'clinic',
        'result_treatment',
        'treatments',
        'results_treatments',
        'patient_id'
    ];

    // Relation avec le patient
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function products()
    {
        return $this->hasMany(AntecedentProduct::class, 'antecedent_id', 'id');
    }

    public function reactions()
    {
        return $this->hasMany(AntecedentReaction::class, 'antecedent_id', 'id');
    }
}
