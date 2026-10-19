<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class categories extends Model
{

    protected $fillable = [
        'id', 
        'name', 
        'color', 
        'image'
        ];

    public function products()
    {
        return $this->hasMany(products::class, 'categorie_id');
    }


}
