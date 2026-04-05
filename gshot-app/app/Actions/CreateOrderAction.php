<?php

namespace App\Actions;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Events\OrderCreated;

class CreateOrderAction
{
    /**
     * Create a new order and its items.
     */
    public function execute(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $order = Order::create([
                'user_id' => Auth::id(),
                'name' => $data['name'],
                'phone' => $data['phone'],
                'payment_method' => $data['payment_method'],
                'order_type' => $data['order_type'],
                'address' => $data['address'] ?? null,
                'total' => $data['total'],
            ]);

            $order->items()->createMany($data['items']);

            $order->refresh();

            event(new OrderCreated($order));

            return $order;
        });
    }
}
