<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function index()
    {
        $cart = auth()->user()->carts;
        return response()->json($cart);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $sessionId = $request->header('X-Session-ID') ?? Str::uuid()->toString();

        $userId = Auth::id();

        $product = Product::where('id', $request->product_id)->firstOrFail();
        $price = $product->price * $request->quantity;

        $cartQuery = Cart::where('product_id', $request->product_id);

        if ($userId) {
            $cartQuery->where('user_id', $userId);
        } else {
            $cartQuery->where('session_id', $sessionId);
        }

        $cartItem = $cartQuery->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->price += $price;
            $cartItem->save();
        } else {
            $cartItem = new Cart();
            $cartItem->user_id = $userId;
            $cartItem->session_id = $userId ? null : $sessionId;
            $cartItem->product_id = $request->product_id;
            $cartItem->quantity = $request->quantity;
            $cartItem->price = $price;
            $cartItem->save();
        }

        return response()->json([
            'message' => 'Product added to cart',
            'cart' => $cartItem,
            'session_id' => $sessionId,
        ]);
    }
}
