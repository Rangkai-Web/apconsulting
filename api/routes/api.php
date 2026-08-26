<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\ShippingController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\PaymentMethodController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

// Stricter throttle than the global API limiter — these are brute-force targets
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

// Public order checkout & guest lookup routes
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/lookup', [OrderController::class, 'lookup']);
Route::post('/orders/{order_number}/payment-proof', [OrderController::class, 'uploadPaymentProof']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/password', [ProfileController::class, 'updatePassword']);

    // Address routes
    Route::get('/addresses', [AddressController::class, 'index']);
    Route::post('/addresses', [AddressController::class, 'store']);
    Route::put('/addresses/{id}', [AddressController::class, 'update']);
    Route::delete('/addresses/{id}', [AddressController::class, 'destroy']);

    // Order routes (member)
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order_number}', [OrderController::class, 'show']);

    // Rating routes
    Route::post('/order-items/{id}/rating', [RatingController::class, 'rate']);
});

// Public catalog routes (no auth required)
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);

// Public settings route
Route::get('/settings', [SettingsController::class, 'show']);

// Public banners route
Route::get('/banners', [BannerController::class, 'index']);

// Public payment methods route
Route::get('/payment-methods', [PaymentMethodController::class, 'index']);

// Public shipping route — stricter throttle: this proxies a paid-per-call RajaOngkir endpoint
Route::post('/shipping/cost', [ShippingController::class, 'cost'])->middleware('throttle:10,1');

// Public shipping area lookups
Route::get('/shipping/provinces', [ShippingController::class, 'provinces']);
Route::get('/shipping/cities', [ShippingController::class, 'cities']);
Route::get('/shipping/districts', [ShippingController::class, 'districts']);
