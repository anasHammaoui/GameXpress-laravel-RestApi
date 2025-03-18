<?php

namespace App\Http\Controllers\Api\V2;

use App\Models\Cart;
use App\Models\Product;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class StockController
{
    public function compareToStock($product_id, $quantity)
    
    {
        $product = Product::where('id', $product_id)->first();
        if ($product) {

            if ($quantity === null || $quantity <= 0) {
                return response()->json(['message' => 'ajouter une valeur valide', 'status' => 'error'], 400);
            }
            $cart = Cart::where('product_id', $product_id)->first();
            if ($cart) {
                if ($quantity > $product->stock) {
                    return response()->json(['message' => 'stock insuffisant', 'status' => 'error'], 400);
                } else {
                    return response()->json(['message' => 'stock suffisant', 'status' => 'success'], 200);
                }
            } else {
                return response()->json(['message' => 'produit non trouvé dans le panier', 'status' => 'error'], 404);
            }
        } else {
            return response()->json(['message' => 'produit non trouvé', 'status' => 'error'], 404);
        }
    }

    public function mergeGuestCart($sessionId, $user_id){
        $cartItemsGuest = Cart::where('session_id', $sessionId)->get();
        $cartItemsUser = Cart::where('user_id', $user_id)->get();
        foreach ($cartItemsGuest as $cartItemGuest) {
            //verifier si le produit existe dans le panier de l'utilisateur
            $cartItemUser = $cartItemsUser->where('product_id', $cartItemGuest->product_id)->first();
            if ($cartItemUser) {
                $cartItemUser->quantity += $cartItemGuest->quantity;
                if($cartItemUser->quantity > Product::find($cartItemUser->product_id)->stock){
                    return response()->json(['message' => 'stock insuffisant', 'status' => 'error'], 400);
                }
                $cartItemUser->price += $cartItemGuest->price;
                $cartItemUser->save();
                $cartItemGuest->delete();
                $cartItemGuest->user_id = $user_id;
                $cartItemGuest->session_id = null;
                $cartItemGuest->save();

            } else {
                $cartItemGuest->user_id = $user_id;
                $cartItemGuest->session_id = null;
                $cartItemGuest->save();
            }
        }
        return response()->json(['message' => 'panier fusionné avec succès', 'status' => 'success'], 200);

    }
}   
