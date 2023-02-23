<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\Order;

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

    protected $appends = [
        'image_path',
    ];

    // Accessors(getter)
    protected function imagePath(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => asset('/storage/upload/products/')
        );
    }

    /*
        Many-to-One [Products to Category] Reverse
    */
    public function category()
    {
        return $this->belongsTo(Category::class, 'product_id', 'id');
    }

    /*
        One-to-Many [Product to ProductImages]
    */
    public function productImages()
    {
        return $this->hasMany(ProductImage::class);
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
