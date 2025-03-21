<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\OrderItems;
use App\Models\Orders;
use App\Models\Product;
use App\Models\User;
use App\Notifications\PaymentSuccessfulNotification;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Climate\Order;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Exception;
/**
 * @OA\Info(
 *      title="GameExpress Ecommerce platform",
 *      version="3",
 *      description="Api documentation for GameExpress platform"
 * )
 *
 * @OA\Server(
 *      url="http://127.0.0.1:8000/api",
 *      description="Local development server"
 * )
 */
class PaymentController extends Controller
{
      /**
     * @OA\Post(
     *     path="/api/v3/client/cart/payment",
     *     summary="Create a Stripe Checkout Session",
     *     description="Generates a Stripe checkout session for an order and returns the session URL.",
     *     operationId="createCheckoutSession",
     *     tags={"Payment"},
     *     @OA\Response(
     *         response=200,
     *         description="Checkout session created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="string", example="https://checkout.stripe.com/pay/cs_test_abc123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No orders available",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You have no orders")
     *         )
     *     ),
     *     @OA\SecurityScheme(
     *         securityScheme="BearerToken",
     *         type="http",
     *         scheme="bearer"
     *     )
     * )
     */
    public function createCheckoutSession()
    {
        $orderController = new CommandController();
        $orderResponse = $orderController->create();
        // dd(json_decode($orderResponse->getContent(), true)["data"]);
        $orderData = json_decode($orderResponse->getContent(), true)['data'];
        $order = Orders::where('id' ,$orderData["id"])->with("items")->first();
        $priceData = [];
        $products = [];
        foreach ($order->items as $item) {
            $priceData[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $item->product->name,
                    ],
                    'unit_amount' => intval((($item->product->price) - (($item->product->price * 0.6)) - 5) + $item->product->price) * 100,
                ],
                'quantity' => $item->quantity,
            ];
            $products[] = [
                'product_id' => $item->product->id,
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
                'metadata'=>  [
                    'order_id' => $orderData["id"],
                    'products' => json_encode($products),
                    'user_id' => auth()->user()->id
                ]
            ]);

            return response()->json(['id' => $session->url]);
        }
        return response()->json(["message" => "You have no orders"]);
    }
    public function cancel()
    {
        
        return response()->json(["message" => "order canceled"], 422);
    }
      /**
     * @OA\Get(
     *     path="/api/v3/client/payment/success",
     *     summary="Handle successful Stripe payment",
     *     description="Retrieves payment details using the session ID and updates stock.",
     *     operationId="successPayment",
     *     tags={"Payment"},
     *     @OA\Parameter(
     *         name="session_id",
     *         in="query",
     *         required=true,
     *         description="The Stripe checkout session ID",
     *         @OA\Schema(type="string", example="cs_test_abc123")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="transaction_id", type="string", example="pi_3Jabc123"),
     *             @OA\Property(property="amount", type="number", example=29.99),
     *             @OA\Property(property="currency", type="string", example="USD"),
     *             @OA\Property(property="status", type="string", example="succeeded"),
     *             @OA\Property(property="payment_method", type="string", example="card"),
     *             @OA\Property(property="created_at", type="string", example="2025-03-21 15:30:00")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No session ID provided",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No session ID provided")
     *         )
     *     ),
     *     @OA\Response(
     *         response=402,
     *         description="Payment not completed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Le paiement n'est pas encore complété")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Stripe API error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erreur : API Stripe indisponible")
     *         )
     *     ),
     *     @OA\SecurityScheme(
     *         securityScheme="BearerToken",
     *         type="http",
     *         scheme="bearer"
     *     )
     * )
     */
    public function success(Request $request)
    {
        if (!$request->has('session_id')) {
            return response()->json(['message' => 'No session ID provided'], 400);
        }

        $sessionId = $request->query('session_id');

        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $session = Session::retrieve($sessionId);
            $products = json_decode($session->metadata->products, true);
            foreach ($products as $product) {
                $this->handleStock($product['product_id'], $product['quantity']);
            }
            if ($session->payment_status !== 'paid') {
                return response()->json(['message' => 'Le paiement n\'est pas encore complété'], 402);
            }

            $paymentIntent = PaymentIntent::retrieve($session->payment_intent);
            $order = Orders::find($session->metadata->order_id);
            $user = User::find($order->user_id);
            if ($user) 
            {
                $user->notify(new PaymentSuccessfulNotification($paymentIntent, $products));
            }

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
    public function handleStock($productId,$quantity){
        if ($productId){
            $product = Product::find($productId);
            if ($product) {
                $product->stock = $product->stock - $quantity;
                $product->save();
            }
        }
        return;
    }

    public function transactions()
    {
        try {
            // Stripe::setApiKey(env('STRIPE_KEY'));
             Stripe::setApiKey(config('services.stripe.secret'));
            // if (!env('STRIPE_KEY')) {
            //     return response()->json(['message' => 'Clé API Stripe non définie.'], 400);
            // }
            $sessions = Session::all();
            // dd($sessions);
            $transactions = [];
            foreach ($sessions->data as $session) {
                $transactions[] = [
                    'session_id' => $session->id,
                    'amount' => $session->amount_total,
                    'currency' => strtoupper($session->currency),
                    'status' => $session->payment_status,
                    'created_at' => date('Y-m-d H:i:s', $session->created),
                ];
            }

            return response()->json($transactions, 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }
}
