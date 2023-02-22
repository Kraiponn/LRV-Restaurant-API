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
        return $this->belongsTo(User::class, 'order_id', 'id');
    }

    /*
        Many-to-Many [Orders to Products]
    */
    public function orderDetails()
    {
        return $this->belongsToMany(
            Product::class,
            'order_details',
            'order_id',
            'product_id'
        );
    }
}
