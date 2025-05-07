<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrescriptionProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_product_blood_id',
        'count_bags',
        'priority',
        'format',
        'is_chirurgical',
        'is_replace',
        'justifications',
        'indications',
        'instructions',
        'prescription_id',
    ];

    // Relation avec le type de produit sanguin
    public function typeProductBlood()
    {
        return $this->belongsTo(TypeProductBlood::class);
    }

    // Relation avec la prescription
    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }
}
