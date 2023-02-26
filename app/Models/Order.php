<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'order_date',
        'shipping_date',
        'location',
        'table_no',
        'status',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    /*
        Many-to-One [Orders to User]
    */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    /*
        Many-to-Many [Orders to Products] => Order Details
    */
    public function products()
    {
        return $this->belongsToMany(
            \App\Models\Product::class,
            'order_products',
            'order_id',
            'product_id'
        )->withTimestamps('created_at', 'updated_at')->withPivot('quantity');
    }
}
