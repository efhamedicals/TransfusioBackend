<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockBloodCenter extends Model
{
    use HasFactory;

    protected $fillable = [
        'blood_center_id',
        'type_blood_id',
        'quantity',
        'safety',
    ];

    // Relation avec le centre de collecte de sang
    public function bloodCenter()
    {
        return $this->belongsTo(BloodCenter::class);
    }

    // Relation avec le type de sang
    public function typeBlood()
    {
        return $this->belongsTo(TypeBlood::class);
    }
}
