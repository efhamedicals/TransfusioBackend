<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloodBag extends Model
{
    protected $fillable = [
        'blood_center_id',
        'blood_bank_id',
        'type_product_blood_id',
        'type_blood_id',
        'reference',
        'date_expiration',
        'format',
        'status',
        'price'
    ];

    // Relation avec le centre de collecte de sang
    public function bloodCenter()
    {
        return $this->belongsTo(BloodCenter::class);
    }

    // Relation avec la banque de sang (optionnelle)
    public function bloodBank()
    {
        return $this->belongsTo(BloodBank::class);
    }

    // Relation avec le type de produit sanguin
    public function typeProductBlood()
    {
        return $this->belongsTo(TypeProductBlood::class);
    }

    // Relation avec le type de sang (optionnelle)
    public function typeBlood()
    {
        return $this->belongsTo(TypeBlood::class);
    }
}
