<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Category\StoreCategoryRequest;
use App\Http\Requests\Admin\Category\UpdateCategoryRequest;
use App\Http\Resources\Admin\Category\CategoryResource;
use App\Models\Category;
use App\Services\Admin\Category\CategoryService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService
    ) {
    }


    public function index(Request $request): JsonResponse
    {
        $categories = $this->categoryService->paginate(
            $request->only([
                'keyword',
                'is_active',
                'parent_id',
                'per_page',
            ])
        );

        return ApiResponse::paginated(
        CategoryResource::collection($categories),
            $categories,
            'Lấy danh sách danh mục thành công'
        );
    }


    public function store(
        StoreCategoryRequest $request
    ): JsonResponse {
        $category = $this->categoryService->create(
            $request->validated()
        );

        $category->load('parent');

        return ApiResponse::success(
            new CategoryResource($category),
            'Tạo danh mục thành công',
            201
        );
    }


    public function show(Category $category): JsonResponse
    {
        $category->load('parent');

        return ApiResponse::success(
            new CategoryResource($category),
            'Lấy thông tin danh mục thành công'
        );
    }


    public function update(
        UpdateCategoryRequest $request,
        Category $category
    ): JsonResponse {
        $category = $this->categoryService->update(
            $category,
            $request->validated()
        );

        $category->load('parent');

        return ApiResponse::success(
            new CategoryResource($category),
            'Cập nhật danh mục thành công'
        );
    }


    public function destroy(Category $category): JsonResponse
    {
        $this->categoryService->delete($category);

        return ApiResponse::success(
            null,
            'Xóa danh mục thành công'
        );
    }
}