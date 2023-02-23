<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class ProductImage extends Model
{
    use HasFactory;

    protected $table = 'product_images';

    protected $appends = [
        // 'image_path',
    ];

    // Accessors(getter)
    protected function imagePath(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => asset('storage/upload/products/' . $this->image)
        );
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
