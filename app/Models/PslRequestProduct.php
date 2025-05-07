<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PslRequestProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'blood_type',
        'blood_rh',
        'count',
        'psl_request_id',
        'blood_bag_id'
    ];

    public function pslRequest(): BelongsTo
    {
        return $this->belongsTo(PslRequest::class);
    }
}
