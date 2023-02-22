<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /*
        Many-to-One [Products to Category] Reverse
    */
    public function category()
    {
        return $this->belongsTo(Category::class, 'product_id', 'id');
    }

    /*
        Many-to-Many [Products to Orders]
    */
    public function orderDetails()
    {
        return $this->belongsToMany(
            Order::class,
            'order_details',
            'product_id',
            'order_id'
        );
    }

    /*
        Many-to-Many [Products to Users]
    */
    public function carts()
    {
        return $this->belongsToMany(
            User::class,
            'carts',
            'product_id',
            'user_id'
        );
    }
}
