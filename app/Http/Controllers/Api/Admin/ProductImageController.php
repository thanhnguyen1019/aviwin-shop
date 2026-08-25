<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductImage\StoreProductImageRequest;
use App\Http\Resources\Admin\ProductImage\ProductImageResource;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\Admin\ProductImage\ProductImageService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class ProductImageController extends Controller
{
    public function __construct(
        protected ProductImageService $productImageService
    ) {
    }

    public function index(
        Product $product
    ): JsonResponse {
        $images = $this->productImageService
            ->getByProduct($product);

        return ApiResponse::success(
            ProductImageResource::collection($images),
            'Lấy danh sách ảnh sản phẩm thành công'
        );
    }

    public function store(
        StoreProductImageRequest $request,
        Product $product
    ): JsonResponse {
        $image = $this->productImageService
            ->create(
                $product,
                $request->validated()
            );

        return ApiResponse::success(
            new ProductImageResource($image),
            'Thêm ảnh sản phẩm thành công',
            201
        );
    }

    public function setPrimary(
        Product $product,
        ProductImage $image
    ): JsonResponse {
        $image = $this->productImageService
            ->setPrimary(
                $product,
                $image
            );

        return ApiResponse::success(
            new ProductImageResource($image),
            'Đặt ảnh chính thành công'
        );
    }

    public function destroy(
        Product $product,
        ProductImage $image
    ): JsonResponse {
        $this->productImageService
            ->delete(
                $product,
                $image
            );

        return ApiResponse::success(
            null,
            'Xóa ảnh sản phẩm thành công'
        );
    }
}