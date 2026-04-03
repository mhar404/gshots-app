<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\OrderUpdated;
use App\Http\Requests\StoreOrderRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request)
    {
        $validated = $request->validated();

        $order = DB::transaction(function () use ($validated) {
            $order = Order::create([
                'user_id' => Auth::id(),
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'payment_method' => $validated['payment_method'],
                'order_type' => $validated['order_type'],
                'address' => $validated['address'] ?? null,
                'total' => $validated['total'],
            ]);

            $order->items()->createMany($validated['items']);

            return $order;
        });

        $order->refresh(); // Load the default status

        event(new \App\Events\OrderCreated($order));

        return response()->json([
            'message' => 'Order created successfully',
            'order_id' => $order->id
        ]);
    }

    public function index(Request $request)
    {
        $query = Order::with('items')->latest();

        if ($request->user() && $request->user()->role !== 'admin') {
            $query->where('user_id', $request->user()->id);
        }

        return response()->json($query->get());
    }

    public function show($id)
    {
        $order = Order::with('items')->findOrFail($id);

        Gate::authorize('view', $order);

        return response()->json($order);
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        Gate::authorize('update', $order);

        $order->status = $request->status;
        $order->save();

        event(new OrderUpdated($order));

        return response()->json([
            'message' => 'Status updated',
            'order' => $order
        ]);
    }

    public function destroyAll()
    {
        // Gate::authorize('deleteAny', Order::class);

        \App\Models\OrderItem::query()->delete();
        Order::query()->delete();

        return response()->json([
            'message' => 'All orders deleted successfully'
        ]);
    }
}
