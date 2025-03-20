<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Api\V2\CartController;
use App\Http\Controllers\Controller;
use App\Models\Orders;
use Illuminate\Http\Request;

class CommandController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     // list all commands
    public function index()
    {
       $order = Orders::all();
       return response()->json($order);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user_id = auth('sanctum')->id();
        $Controller = new CartController();

        $response = $Controller->cartDetails($user_id); 
        $data = json_decode($response->getContent(), true);

        if (isset($data['totalPrice'])) {
            $totalPrice = $data['totalPrice'];
        } else { 
            $totalPrice = 0;
        }
        $order = Orders::create([
            'user_id' => $user_id,
            'total_price' => $totalPrice,
            'status' => 'en attente',
        ]);
        $order->save();
        if($order){
            $cartsJson = $Controller->index();
            $carts = json_decode($cartsJson->getContent(), true);
            foreach ($carts as $cart) {
                $order->items()->create([
                    'product_id' => $cart['product_id'],
                    'quantity' => $cart['quantity'],
                    'price' => $cart['price'],
                ]);
                $Controller->destroy($cart['id']);
            }   
            return response()->json(['message' => 'Order created successfully', 'data' => $order]);
        }
        return response()->json(['message' => 'Order not created']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */

     // view a specific command
    public function show(string $id)
    {
        $order = Orders::with('user')->findOrFail($id);
        return response()->json($order);
    }

    /**
     * Update the specified resource in storage.
     */
    
     // update order status
    public function update(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:en attente,en cours,expédiée,annulée'
        ]);

        $order = Orders::findOrFail($id);
        if($order)
        {
            $order->update(['status' => $request->status]);
            return response()->json(['message' => 'Commande modifié avec succès']);
        } 
        else 
        {
            return response()->json(['message' => 'Order Not Found']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */

    // Cancel an order
    public function destroy(string $id)
    {
        $order = Orders::findOrFail($id);
        $order->update(['status' => 'annulée']);

        return response()->json(['message' => 'Commande annulée avec succès']);
    }
}
