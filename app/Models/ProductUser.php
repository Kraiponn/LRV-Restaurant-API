<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductUser extends Model
{
    use HasFactory;

    protected $table = 'product_users';

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity'
    ];

    protected $hidden = [];
}
