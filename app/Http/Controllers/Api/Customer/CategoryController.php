<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\Category\CategoryResource;
use App\Services\Customer\Category\CategoryService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService
    ) {
    }

    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getTree();

        return ApiResponse::success(
            CategoryResource::collection($categories),
            'Lấy danh mục thành công'
        );
    }

    public function show(string $slug): JsonResponse
    {
        $category = $this->categoryService->findBySlug($slug);

        return ApiResponse::success(
            new CategoryResource($category),
            'Lấy thông tin danh mục thành công'
        );
    }
}