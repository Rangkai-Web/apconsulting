<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\InquiryController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\TeamController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

// Public Company Profile API Endpoints
Route::get('/faqs', [FaqController::class, 'index']);
Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/{slug}', [ArticleController::class, 'show']);
Route::get('/team', [TeamController::class, 'index']);
Route::get('/settings', [SettingsController::class, 'show']);
Route::post('/contact', [InquiryController::class, 'store'])->middleware('throttle:10,1');
