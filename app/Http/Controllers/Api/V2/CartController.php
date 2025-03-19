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

        StockController::compareToStock($request->product_id, $request->quantity);

        $sessionId = $request->header('X-Session-ID') ?? Str::uuid()->toString();

        $userId = auth('sanctum')->user()->id ?? null;

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

    public function show($cart_id)
    {
        $cart = Cart::findOrFail($cart_id);
        return response()->json($cart);
    }

    public function update(Request $request, $cart_id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        if (!Cart::where('id', $cart_id)->exists()) {
            return response()->json([
                'message' => 'Product not found in cart',
            ], 404);
        }

        StockController::compareToStock($request->product_id, $request->quantity);

        $cart = Cart::findOrFail($cart_id);
        $cart->quantity = $request->quantity;
        $cart->price = $cart->product->price * $cart->quantity;
        $cart->save();
        return response()->json([
            'message' => 'Product quantity updated',
            'cart' => $cart,
        ]);
    }

    public function destroy($cart_id)
    {
        $cart = Cart::findOrFail($cart_id);
        $cart->delete();
        return response()->json([
            'message' => 'Product removed from cart',
        ]);
    }
    // calcul de total panier
    public function cartDetails($userId){
        $userCarts = Cart::where("user_id",$userId) -> get();
        $products = [];
        $totalPrice = 0;
        foreach($userCarts as $cart){
            $fees = (($cart -> price )- ($cart -> price * 0.6)) - 5;
           array_push($products,[
            "product" => $cart -> product -> name,
            "product_id" => $cart -> product_id,
            "quantity" => $cart -> quantity,
            "details" => [
                "TVA" => "6%",
                "livraison" => "5$",
                "Original_Price" => $cart -> price,
                "total_fees" => $fees,
                "After_fees_price" => $cart -> price + $fees,
            ],
           ]);
            $totalPrice +=  $cart -> price + $fees;
        }
        if (count($userCarts) > 0){
            return response() -> json(["products" => $products, "totalPrice" => $totalPrice],200);
        } 
        return   response() -> json(["message" => "empty cart"],422);
    }
}
