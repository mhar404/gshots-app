<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'payment_method' => 'required|string|max:50',
            'address' => 'nullable|string',
            'order_type' => 'required|in:delivery,pickup',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $hasActiveOrder = Order::where('user_id', Auth::id())
                ->whereIn('status', ['pending', 'preparing'])
                ->exists();

            if ($hasActiveOrder) {
                $validator->errors()->add('active_order', 'You already have an active order being processed.');
            }
        });
    }
}
