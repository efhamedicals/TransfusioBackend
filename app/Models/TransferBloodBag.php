<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferBloodBag extends Model
{
    use HasFactory;

    protected $fillable = [
        'blood_center_id',
        'blood_bank_id',
    ];

    // Relation avec le centre de collecte de sang
    public function bloodCenter()
    {
        return $this->belongsTo(BloodCenter::class);
    }

    // Relation avec la banque de sang
    public function bloodBank()
    {
        return $this->belongsTo(BloodBank::class);
    }
}
