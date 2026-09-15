<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\JsonResponse;

class ServiceCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = ServiceCategory::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories,
        ]);
    }
}
