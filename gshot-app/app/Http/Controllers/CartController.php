<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreCartRequest;
use App\Http\Requests\UpdateCartRequest;

class CartController extends Controller
{
    // Fetch all cart items for the logged-in user
    public function index()
    {
        $cart = Cart::with('product')
            ->where('user_id', Auth::id())
            ->get();

        return response()->json($cart); 
    }

    // Add a product to the cart
    public function store(StoreCartRequest $request)
    {
        $validated = $request->validated();

        $cartItem = Cart::firstOrCreate(
            ['user_id' => Auth::id(), 'product_id' => $validated['product_id']],
            ['quantity' => 0]
        );

        $cartItem->quantity += $validated['quantity'];
        $cartItem->save();

        return $this->index();
    }

    // Update quantity of a product in the cart
    public function update(UpdateCartRequest $request, $product_id)
    {
        $validated = $request->validated();

        $cartItem = Cart::where('user_id', Auth::id())
            ->where('product_id', $product_id)
            ->firstOrFail();

        Gate::authorize('update', $cartItem);

        $cartItem->quantity = $validated['quantity'];
        $cartItem->save();

        return $this->index();
    }

    // Remove a product from the cart
    public function destroy($product_id)
    {
        $cartItem = Cart::where('user_id', Auth::id())
            ->where('product_id', $product_id)
            ->first();

        if ($cartItem) {
            Gate::authorize('delete', $cartItem);
            $cartItem->delete();
        }

        return $this->index();
    }

    // Clear the entire cart
    public function clear()
    {
        Cart::where('user_id', Auth::id())->delete();

        return response()->json([]);
    }
}