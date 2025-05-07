<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PslRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'first_name',
        'last_name',
        'user_id',
        'prescription',
        'blood_report',
        'status',
        'end_verification',
        'prescription_date',
        'prescription_fullname',
        'prescription_birth_date',
        'prescription_age',
        'prescription_gender',
        'prescription_blood_type',
        'prescription_blood_rh',
        'prescription_diagnostic',
        'prescription_substitution'
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PslRequestProduct::class);
    }

    public function payment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
