<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AntecedentProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'antecedent_id',
        'type_product_blood_id',
        'format',
        'blood_type',
        'rhesus',
    ];

    // Relation avec l'antécédent
    public function antecedent()
    {
        return $this->belongsTo(Antecedent::class);
    }

    // Relation avec le type de produit sanguin
    public function typeProductBlood()
    {
        return $this->belongsTo(TypeProductBlood::class);
    }
}
