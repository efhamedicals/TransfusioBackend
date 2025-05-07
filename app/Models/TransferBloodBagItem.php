<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferBloodBagItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_blood_bag_id',
        'type_blood_id',
        'quantity',
    ];

    // Relation avec le transfert de poches de sang
    public function transferBloodBag()
    {
        return $this->belongsTo(TransferBloodBag::class);
    }

    // Relation avec le type de sang
    public function typeBlood()
    {
        return $this->belongsTo(TypeBlood::class);
    }
}
