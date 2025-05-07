<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RenewStockCenterItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'renew_stock_center_id',
        'type_blood_id',
        'quantity',
    ];

    // Relation avec le renouvellement du stock du centre de collecte de sang
    public function renewStockCenter()
    {
        return $this->belongsTo(RenewStockCenter::class);
    }

    // Relation avec le type de sang
    public function typeBlood()
    {
        return $this->belongsTo(TypeBlood::class);
    }
}
