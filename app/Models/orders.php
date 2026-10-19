<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class orders extends Model
{

    protected $fillable = [
        'reference',
        'ui_id',
        'deleivery_zone_id',
        'zone_name',
        'address',
        'fulfillmentMode',
        'note',
        'sub_total',
        'discount_percentage',
        'discount_amount',
        'total',
        'statut',
        'stock_applied',
        'stock_restored',
        'canceled_at',
    ];

    protected $casts = [
        'stock_applied' => 'boolean',
        'stock_restored' => 'boolean',
        'canceled_at' => 'datetime',
    ];

    
    public function items()
    {
        return $this->hasMany(orders_items ::class, 'order_id');
    }
}


