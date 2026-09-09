<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $locale = $request->get('lang', 'id');
        $query = Article::where('is_published', true);

        if ($request->has('category') && !empty($request->category)) {
            $category = $request->category;
            $query->where(function ($q) use ($category) {
                $q->where('category_id', $category)
                  ->orWhere('category_en', $category);
            });
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title_id', 'like', "%{$search}%")
                  ->orWhere('title_en', 'like', "%{$search}%")
                  ->orWhere('content_id', 'like', "%{$search}%")
                  ->orWhere('content_en', 'like', "%{$search}%");
            });
        }

        $articles = $query->orderBy('published_at', 'desc')
            ->paginate($request->get('per_page', 10));

        $items = collect($articles->items())
            ->map(fn (Article $article) => $article->toLocalizedArray($locale));

        return response()->json([
            'status' => 'success',
            'data' => $items,
            'meta' => [
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
                'per_page' => $articles->perPage(),
                'total' => $articles->total(),
            ],
        ]);
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        $locale = $request->get('lang', 'id');

        $article = Article::where('is_published', true)
            ->where(function ($q) use ($slug) {
                $q->where('slug_id', $slug)
                  ->orWhere('slug_en', $slug);
            })
            ->first();

        if (!$article) {
            return response()->json([
                'status' => 'error',
                'message' => 'Artikel tidak ditemukan',
            ], 404);
        }

        $article->increment('views_count');

        return response()->json([
            'status' => 'success',
            'data' => $article->toLocalizedArray($locale),
        ]);
    }
}
