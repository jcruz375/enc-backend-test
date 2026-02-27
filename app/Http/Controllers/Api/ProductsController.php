<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductsController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Product::query();

        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->where('title', 'like', '%' . $request->search . '%');
        });

        $query->when($request->filled('category'), function ($q) use ($request) {
            $q->where('category', $request->category);
        });

        $query->when($request->filled('price_min'), function ($q) use ($request) {
            $q->where('price', '>=', $request->price_min);
        });

        $query->when($request->filled('price_max'), function ($q) use ($request) {
            $q->where('price', '<=', $request->price_max);
        });

        $query->when($request->filled('rating_min'), function ($q) use ($request) {
            $q->where('rating_rate', '>=', $request->rating_min);
        });

        $allowedSorts = ['price', 'title', 'rating_rate', 'id'];
        $sortField = $request->input('sort_by', 'id');
        $sortOrder = $request->input('order', 'asc');

        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortOrder === 'desc' ? 'desc' : 'asc');
        }

        return ProductResource::collection($query->paginate(10));
    }

    public function stats(): JsonResponse
    {
        return response()->json([
            'total_products'   => Product::count(),
            'average_price'    => round((float) Product::avg('price'), 2),
            'highest_price'    => (float) Product::max('price'),
            'lowest_price'     => (float) Product::min('price'),
            'categories_count' => Product::groupBy('category')
                ->selectRaw('category, count(*) as count')
                ->pluck('count', 'category'),
        ]);
    }

    public function show(string $id): ProductResource
    {
        $product = Product::findOrFail($id);

        return new ProductResource($product);
    }

    public function update(ProductRequest $request, string $id): ProductResource
    {
        $product = Product::findOrFail($id);
        $validated = $request->validated();

        $changes = [];
        foreach ($validated as $key => $value) {
            if ($product->$key != $value) {
                $changes[$key] = ['old' => $product->$key, 'new' => $value];
                $product->$key = $value;
            }
        }

        if (!empty($changes)) {
            $log = $product->update_log ?? [];
            $log[] = ['timestamp' => now()->toIso8601String(), 'changes' => $changes];
            $product->update_log = $log;
            $product->save();
        }

        return new ProductResource($product);
    }

    public function destroy(string $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        if ($product->rating_rate > 4.5) {
            return response()->json([
                'message' => 'Não é permitido excluir produtos com avaliação superior a 4.5'
            ], 422);
        }

        $product->delete();

        return response()->json(null, 204);
    }
}
