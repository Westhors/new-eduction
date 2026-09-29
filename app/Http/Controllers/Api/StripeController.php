<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\StripeClient;

class StripeController extends Controller
{
    public function checkout(Request $request)
    {
        // التأكد أن التوكن صالح وأن المستخدم مسجل دخول
        $student = Auth::user();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        // التأكد من البيانات المطلوبة
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string|size:3',
            'product_name' => 'required|string|max:255',
            'course_id' => 'required|integer',
        ]);

        // التأكد أن course_id موجود فعلًا
        if (!$request->course_id) {
            return response()->json([
                'success' => false,
                'message' => 'course_id is required',
            ], 422);
        }

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

            'success_url' =>
                'https://teachersmarkettest.dentin.cloud/payment/success',

            'cancel_url' =>
                'https://teachersmarkettest.dentin.cloud/payment/cancel',

            // نحفظ بيانات الطالب والكورس مع Stripe Session
            'metadata' => [
                'student_id' => $student->id,
                'course_id' => $request->course_id,
            ],
        ]);

        return response()->json([
            'success' => true,

            // للتأكد أن التوكن وصل وأن الطالب معروف
            'student_id' => $student->id,

            // للتأكد أن course_id وصل
            'course_id' => $request->course_id,

            'session_id' => $session->id,
            'checkout_url' => $session->url,
        ]);
    }
}
