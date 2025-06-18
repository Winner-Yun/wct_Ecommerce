<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        return CategoryResource::collection(Category::all());
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        if (Gate::denies('create', Category::class)) {
            return response()->json([
                'error' => 'Access Denied',
                'message' => 'Only admins and super admins can perform this action.'
            ], 403);
        }

        $category = Category::create($request->validated());

        return response()->json([
            'message' => 'Category created successfully.',
            'category' => new CategoryResource($category)
        ], 201);
    }

}
