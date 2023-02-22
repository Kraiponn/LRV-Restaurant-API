<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
    ];

    protected $hidden = [
        //
    ];

    protected $appends = [];

    /*
        One-to-Many [Category to Products]
    */
    public function products()
    {
        return $this->hasMany(
            Product::class,
            'product_id',
            'id',
        );
    }
}
