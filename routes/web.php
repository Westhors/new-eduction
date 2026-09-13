<?php

use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StripeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});



Route::get('auth/google/redirect', [GoogleAuthController::class, 'redirect']);
Route::get('auth/google/callback', [GoogleAuthController::class, 'callback']);

Route::get('/stripe/checkout', [StripeController::class, 'checkout'])
    ->name('stripe.checkout');

Route::get('/stripe/success', [StripeController::class, 'success'])
    ->name('stripe.success');

Route::get('/stripe/cancel', [StripeController::class, 'cancel'])
    ->name('stripe.cancel');
