<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    public function index($id)
    {
        $cart = Cart::find($id) -> first();
        if ($cart){
         return view('checkout',["product_id"=> $cart -> product_id, "quantity" => $cart-> quantity, "price" => $cart -> price]);
        }
        return response() -> json([
            "message" => "product not found",
        ]);
    }
    
    
    public function createCheckoutSession(Request $request)
    {
        // Validate request
        $request->validate([
            'product_id' => 'required',
            'price' => 'required|numeric',
            'name' => 'required|string',
        ]);
        
        Stripe::setApiKey(env('STRIPE_SECRET'));
        
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $request->name,
                    ],
                    'unit_amount' => $request->price * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => env('APP_URL') . '/api/v3/client/payment/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => env('APP_URL') . '/api/v3/client/payment/cancel',
            'metadata' => [
                'product_id' => $request->product_id,
            ],
        ]);
        
        return response()->json(['id' => $session->url]);
    }
    public function cancel(){
        return response() -> json(["message"=> "order canceled"],422);
    }
    public function success(Request $request)
    {
        if (!$request->has('session_id')) {
            return response()->json(['message' => 'No session ID provided'], 400);
        }
        
        $sessionId = $request->query('session_id');
        
        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $session = Session::retrieve($sessionId);
            
            // Check if payment was successful
            if ($session->payment_status === 'paid') {
                // Extract metadata
                $productId = $session->metadata->product_id;
                
                // Here you could update your database to mark the order as paid
                // Example: Order::where('product_id', $productId)->update(['payment_status' => 'paid']);
                
                return response()->json([
                    'message' => 'Payment successful',
                    'product_id' => $productId
                ], 200);
            } else {
                return response()->json(['message' => 'Payment not completed'], 402);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error processing payment: ' . $e->getMessage()], 500);
        }
    }
}
