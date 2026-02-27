<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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

    public function show(string $id): ProductResource
    {
        $product = Product::where('id', $id)->firstOrFail();

        return new ProductResource($product);
    }
}
