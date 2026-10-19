<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class orders_items extends Model
{
    protected $fillable = [
        'order_id',
        'products_id',
        'name',
        'unit',
        'unit_price',
        'quantity',
        'line_total',
    ];

    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }
}


