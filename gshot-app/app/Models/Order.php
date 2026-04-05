<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;

class Order extends Model
{
    protected $fillable = ['user_id','name', 'phone', 'payment_method','order_type', 'address', 'total'];

    protected $casts = [
        'status' => \App\Enums\OrderStatus::class,
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include orders for the current user, or all orders if admin.
     */
    public function scopeForUser($query, $user)
    {
        if ($user && $user->role !== 'admin') {
            return $query->where('user_id', $user->id);
        }

        return $query;
    }
}
