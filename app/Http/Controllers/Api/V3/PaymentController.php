<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\OrderItems;
use App\Models\Orders;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Climate\Order;
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
    
    
    public function createCheckoutSession()
    {
        $orderController = new CommandController();
        $orderResponse = $orderController->create();
        $orderData = json_decode($orderResponse->getContent(), true)['data'];
        $order = Orders::find($orderData["id"])->with("items")->first();
        $priceData = [];
        foreach ($order->items as $item) {
            $priceData[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $item->product->name,
                    ],
                    'unit_amount' => $item->price * 100,
                ],
                'quantity' => $item->quantity,
            ];
        }
        if ($orderData["total_price"] > 0) {
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $priceData,
                'mode' => 'payment',
                'success_url' => env('APP_URL') . '/api/v3/client/payment/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => env('APP_URL') . '/api/v3/client/payment/cancel',
            ]);
            
            return response()->json(['id' => $session->url]);
        } 
        return response()->json(["message" => "You have no orders"]);
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
            // dd($session);
            if ($session->payment_status !== 'paid') {
                return response()->json(['message' => 'Le paiement n\'est pas encore complété'], 402);
            }
    
            $paymentIntent = PaymentIntent::retrieve($session->payment_intent);
    
            return response()->json([
                'transaction_id' => $paymentIntent->id,
                'amount' => $paymentIntent->amount_received / 100,
                'currency' => strtoupper($paymentIntent->currency),
                'status' => $paymentIntent->status,
                'payment_method' => $paymentIntent->payment_method_types[0] ?? 'N/A',
                'created_at' => date('Y-m-d H:i:s', $paymentIntent->created),
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }
}
