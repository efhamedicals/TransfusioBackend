<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AntecedentReaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'antecedent_id',
    ];

    // Relation avec l'antécédent
    public function antecedent()
    {
        return $this->belongsTo(Antecedent::class);
    }
}
