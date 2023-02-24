<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageProduct extends Model
{
    use HasFactory;

    /*
        Many-to-One [ImageProducts to Product]
    */
    public function imageProducts()
    {
        return $this->hasMany(\App\Models\Product::class, 'product_id', 'id');
    }
}
