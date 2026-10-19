<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class promotions extends Model
{

    protected $fillable = [
        'code_promo',
        'percentage',
        'active',
        'expired_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'expired_at' => 'datetime',
    ];
}


