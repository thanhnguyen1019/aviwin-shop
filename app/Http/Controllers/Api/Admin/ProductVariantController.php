<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductVariant\StoreProductVariantRequest;
use App\Http\Requests\Admin\ProductVariant\UpdateProductVariantRequest;
use App\Http\Resources\Admin\ProductVariant\ProductVariantResource;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\Admin\ProductVariant\ProductVariantService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class ProductVariantController extends Controller
{
    public function __construct(
        protected ProductVariantService $productVariantService
    ) {
    }

    public function index(
        Product $product
    ): JsonResponse {
        $variants = $this->productVariantService
            ->getByProduct($product);

        return ApiResponse::success(
            ProductVariantResource::collection($variants),
            'Lấy danh sách biến thể thành công'
        );
    }

    public function store(
        StoreProductVariantRequest $request,
        Product $product
    ): JsonResponse {
        $variant = $this->productVariantService
            ->create(
                $product,
                $request->validated()
            );

        return ApiResponse::success(
            new ProductVariantResource($variant),
            'Tạo biến thể thành công',
            201
        );
    }

    public function show(
        Product $product,
        ProductVariant $variant
    ): JsonResponse {
        abort_unless(
            $variant->product_id === $product->id,
            404
        );

        $variant->load([
            'color',
            'size',
        ]);

        return ApiResponse::success(
            new ProductVariantResource($variant),
            'Lấy thông tin biến thể thành công'
        );
    }

    public function update(
        UpdateProductVariantRequest $request,
        Product $product,
        ProductVariant $variant
    ): JsonResponse {
        $variant = $this->productVariantService
            ->update(
                $product,
                $variant,
                $request->validated()
            );

        return ApiResponse::success(
            new ProductVariantResource($variant),
            'Cập nhật biến thể thành công'
        );
    }

    public function destroy(
        Product $product,
        ProductVariant $variant
    ): JsonResponse {
        $this->productVariantService
            ->delete(
                $product,
                $variant
            );

        return ApiResponse::success(
            null,
            'Xóa biến thể thành công'
        );
    }
}