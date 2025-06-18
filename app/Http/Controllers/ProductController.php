<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller{

    use AuthorizesRequests;
    // List all products
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $products = Product::with('category')->paginate(15);
        return ProductResource::collection($products);
    }

    // Show single product
    public function show(Product $product): ProductResource
    {
        $product->load('category');
        return new ProductResource($product);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        if (Gate::denies('create', Product::class)) {
            return response()->json([
                'error' => 'Access Denied',
                'message' => 'Only admins and super admins can create products.'
            ], 403);
        }

        $product = Product::create($request->validated());

        return response()->json([
            'message' => 'Product created successfully.',
            'product' => new ProductResource($product)
        ], 201);
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        if (Gate::denies('update', $product)) {
            return response()->json([
                'error' => 'Access Denied',
                'message' => 'Only admins and super admins can update products.'
            ], 403);
        }

        $product->update($request->validated());
        $product->load('category');

        return response()->json([
            'message' => 'Product updated successfully.',
            'product' => new ProductResource($product)
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        if (Gate::denies('delete', $product)) {
            return response()->json([
                'error' => 'Access Denied',
                'message' => 'Only admins and super admins can delete products.'
            ], 403);
        }

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully.'
        ]);
    }

}
