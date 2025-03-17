<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Product_images;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
class CartController extends Controller
{
    public function index(){
        $cart = auth()->user()->carts;
        return response()->json($cart);
    }

    public function store(Request $request){
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        $sessionId = $request->header('X-Session-ID') ?? Str::uuid()->toString();
    
        $cart = new Cart();
        
        if (Auth::check()) {
            $cart->user_id = Auth::id();
        } else {
            $cart->session_id = $sessionId;
        }
        
        $cart->product_id = $request->product_id;
        $cart->quantity = $request->quantity;
        $cart->price = $request->price;
        $cart->save();
    
        return response()->json([
            'message' => 'Product added to cart',
            'cart' => $cart,
            'session_id' => $sessionId,
        ]);
    }
}
