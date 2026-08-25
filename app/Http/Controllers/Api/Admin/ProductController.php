<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\StoreProductRequest;
use App\Http\Requests\Admin\Product\UpdateProductRequest;
use App\Http\Resources\Admin\Product\ProductResource;
use App\Models\Product;
use App\Services\Admin\Product\ProductService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {
    }

    public function index(
        Request $request
    ): JsonResponse {
        $products = $this->productService
            ->paginate(
                $request->only([
                    'keyword',
                    'category_id',
                    'brand_id',
                    'is_active',
                    'is_featured',
                    'per_page',
                ])
            );

        return ApiResponse::paginated(
            ProductResource::collection($products),
            $products,
            'Lấy danh sách sản phẩm thành công'
        );
    }

    public function store(
        StoreProductRequest $request
    ): JsonResponse {
        $product = $this->productService
            ->create(
                $request->validated()
            );

        $product->load([
            'category',
            'brand',
        ]);

        return ApiResponse::success(
            new ProductResource($product),
            'Tạo sản phẩm thành công',
            201
        );
    }

    public function show(
        Product $product
    ): JsonResponse {
        $product->load([
            'category',
            'brand',
        ]);

        return ApiResponse::success(
            new ProductResource($product),
            'Lấy thông tin sản phẩm thành công'
        );
    }

    public function update(
        UpdateProductRequest $request,
        Product $product
    ): JsonResponse {
        $product = $this->productService
            ->update(
                $product,
                $request->validated()
            );

        $product->load([
            'category',
            'brand',
        ]);

        return ApiResponse::success(
            new ProductResource($product),
            'Cập nhật sản phẩm thành công'
        );
    }

    public function destroy(
        Product $product
    ): JsonResponse {
        $this->productService
            ->delete($product);

        return ApiResponse::success(
            null,
            'Xóa sản phẩm thành công'
        );
    }
}