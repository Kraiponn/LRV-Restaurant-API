<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'image',
        'device_name',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'image_path',
        'full_name',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected function imagePath(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => asset('storage/upload/accounts/' . $this->image)
        );
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->first_name . ' ' . $this->last_name
        );
    }

    /*
    |------------------------------------------------------------------------------------
    |   Virsual table
    |------------------------------------------------------------------------------------
     */
    public function orders()
    {
        return $this->hasMany(
            \App\Models\Order::class,
            'user_id',
            'id'
        );
    }

    /*
        Many-to-Many [The Users That Belong To This Products] => product_users
    */
    public function products()
    {
        return $this->belongsToMany(
            \App\Models\Product::class,
            'product_users',
            'user_id',
            'product_id',
        )
            ->withTimestamps('created_at', 'updated_at')
            ->withPivot('quantity');
    }
}
