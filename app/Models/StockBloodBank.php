<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockBloodBank extends Model
{
    use HasFactory;

    protected $fillable = [
        'blood_bank_id',
        'type_blood_id',
        'quantity',
        'safety',
    ];

    // Relation avec la banque de sang
    public function bloodBank()
    {
        return $this->belongsTo(BloodBank::class);
    }

    // Relation avec le type de sang
    public function typeBlood()
    {
        return $this->belongsTo(TypeBlood::class);
    }
}
