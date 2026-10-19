<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class products extends Model
{

    protected $fillable = [
        'id',
        'categorie_id',
        'name',
        'email',
        'description',
        'origin',
        'unit',
        'image',
        'price',
        'salle_price',
        'stock',
        'version',
        'active',
        'featured',
    ];

    protected $casts = [
        'active' => 'boolean',
        'featured' => 'boolean',
        'price' => 'integer',
        'salle_price' => 'integer',
        'stock' => 'integer',
    ];

   
    public function category()
    {
        return $this->belongsTo(categories::class, 'categorie_id');
    }
}


