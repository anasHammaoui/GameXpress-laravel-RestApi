<?php

namespace App\Http\Controllers\Api\V2;

use App\Models\Cart;
use App\Models\Product;

use Illuminate\Http\Request;

class StockController
{
    public function compareToStock($product_id, Request $request)
    {
        $product = Product::find($product_id);
        if ($product) {

            $quantity = $request->quantity;
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
        }
    }
}
