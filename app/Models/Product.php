<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'unit_price',
        'in_stock',
        'category_id',
    ];

    // Appends fields for resposne
    protected $appends = [
        // 'image_path',
    ];

    // Hidden response fields
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /*
        Many-to-One [Products to Category] Reverse
    */
    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class, 'category_id', 'id');
    }

    /*
        One-to-Many [Product to ProductImages]
    */
    public function imageProducts()
    {
        return $this->hasMany(\App\Models\ImageProduct::class, 'product_id', 'id');
    }

    /*
        Many-to-Many [Products to Orders] => Order details
    */
    public function orderProducts()
    {
        return $this->belongsToMany(
            \App\Models\Order::class,
            'order_products',
            'product_id',
            'order_id'
        );
    }

    /*
        Many-to-Many [Products to Users] => Cart
    */
    public function users()
    {
        return $this->belongsToMany(
            \App\Models\User::class,
            'product_users',
            'product_id',
            'user_id'
        );
    }
}
