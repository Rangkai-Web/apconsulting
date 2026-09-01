<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private const PER_PAGE = 8;

    public function index(Request $request)
    {
        $query = Product::query()
            ->with('variants')
            ->where('is_active', true)
            ->withMin('variants', 'price');

        if ($request->filled('ids')) {
            $ids = array_filter(array_map('intval', explode(',', $request->string('ids'))));
            $query->whereIn('id', $ids);
        }

        if ($request->filled('category')) {
            $category = Category::query()->where('slug', $request->string('category'))->first();
            if ($category) {
                $categoryIds = Category::query()
                    ->where('parent_id', $category->id)
                    ->pluck('id')
                    ->push($category->id);
                $query->whereIn('category_id', $categoryIds);
            } else {
                $query->where('category_id', 0);
            }
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->string('search').'%');
        }

        if ($request->filled('min')) {
            $query->having('variants_min_price', '>=', (int) $request->input('min'));
        }

        if ($request->filled('max')) {
            $query->having('variants_min_price', '<=', (int) $request->input('max'));
        }

        match ($request->string('sort')->toString()) {
            'price_asc' => $query->orderBy('variants_min_price', 'asc'),
            'price_desc' => $query->orderBy('variants_min_price', 'desc'),
            'rating' => $query->orderBy('rating_avg', 'desc')->orderBy('created_at', 'desc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        $page = max(1, (int) $request->input('page', 1));
        $total = $query->clone()->count();
        $products = $query->forPage($page, self::PER_PAGE)->get();

        return ProductResource::collection($products)->additional([
            'meta' => [
                'has_more' => $page * self::PER_PAGE < $total,
                'next_page' => ($page * self::PER_PAGE < $total) ? $page + 1 : null,
                'total' => $total,
            ],
        ]);
    }

    public function show(string $slug)
    {
        $product = Product::query()
            ->with('variants')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return new ProductResource($product);
    }
}
