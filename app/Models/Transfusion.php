<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfusion extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_id',
        'status', 'image', 'quantity', 'rythm', 'end_transfusion', 'start_transfusion', 'reference', 'hemo_file'
    ];

    // Relation avec la prescription
    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function reactions()
    {
        return $this->hasMany(TransfusionReaction::class, 'transfusion_id', 'id');
    }
    public function constantes()
    {
        return $this->hasMany(TransfusionConstant::class, 'transfusion_id', 'id');
    }
}
