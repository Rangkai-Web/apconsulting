<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $locale = $request->get('lang', 'id');

        $faqs = Faq::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->map(fn (Faq $faq) => $faq->toLocalizedArray($locale));

        return response()->json([
            'status' => 'success',
            'data' => $faqs,
        ]);
    }
}
