<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Events\OrderUpdated;
use App\Http\Requests\StoreOrderRequest;
use App\Actions\CreateOrderAction;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    /**
     * Store a newly created order in storage.
     */
    public function store(StoreOrderRequest $request, CreateOrderAction $createOrderAction)
    {
        $order = $createOrderAction->execute($request->validated());

        return response()->json([
            'message' => 'Order created successfully',
            'order_id' => $order->id
        ], 201);
    }

    /**
     * Display a listing of the orders.
     */
    public function index(Request $request)
    {
        $orders = Order::with('items')
            ->forUser($request->user())
            ->latest()
            ->get();

        return OrderResource::collection($orders)->resolve();
    }

    /**
     * Display the specified order.
     */
    public function show($id)
    {
        $order = Order::with('items')->findOrFail($id);

        Gate::authorize('view', $order);

        return (new OrderResource($order))->resolve();
    }

    /**
     * Update the status of the specified order.
     */
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        Gate::authorize('update', $order);

        $request->validate([
            'status' => ['required', 'string', \Illuminate\Validation\Rule::enum(\App\Enums\OrderStatus::class)],
        ]);

        $order->status = $request->status;
        $order->save();

        event(new OrderUpdated($order));

        return response()->json([
            'message' => 'Status updated',
            'order' => (new OrderResource($order))->resolve()
        ]);
    }

    /**
     * Remove all orders from storage.
     */
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
