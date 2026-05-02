<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $appends = ['image_url'];

    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'category',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
