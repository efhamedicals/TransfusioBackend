<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RenewStockCenter extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'reference',
        'blood_center_id',
    ];

    // Relation avec le centre de collecte de sang
    public function bloodCenter()
    {
        return $this->belongsTo(BloodCenter::class);
    }
}
