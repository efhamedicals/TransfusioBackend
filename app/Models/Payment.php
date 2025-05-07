<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'identifier',
        'payment_code',
        'amount',
        'status',
        'user_id',
        'psl_request_id',
        'phone_number',
        'network'
    ];
}
