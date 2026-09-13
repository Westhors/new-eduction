<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\StripeClient;

class StripeController extends Controller
{
    public function checkout(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string|size:3',
            'product_name' => 'required|string|max:255',
        ]);

        $stripe = new StripeClient(
            config('services.stripe.secret')
        );

        $session = $stripe->checkout->sessions->create([
            'mode' => 'payment',

            'line_items' => [
                [
                    'price_data' => [
                        'currency' => strtolower($request->currency),

                        'product_data' => [
                            'name' => $request->product_name,
                        ],

                        'unit_amount' => (int) ($request->amount * 100),
                    ],

                    'quantity' => 1,
                ],
            ],

            'success_url' => 'http://localhost:3000/payment/success',

            'cancel_url' => 'http://localhost:3000/payment/cancel',
        ]);

        return response()->json([
            'success' => true,
            'session_id' => $session->id,
            'checkout_url' => $session->url,
        ]);
    }
}
