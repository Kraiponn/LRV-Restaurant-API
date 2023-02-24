<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /*
        Many-to-One [Orders to User]
    */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /*
        Many-to-Many [Orders to Products] => Cart
    */
    public function orderProducts()
    {
        return $this->belongsToMany(
            \App\Models\Product::class,
            'order_products',
            'order_id',
            'product_id'
        );
    }
}
