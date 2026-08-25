<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\Product\ProductDetailResource;
use App\Http\Resources\Customer\Product\ProductListResource;
use App\Services\Customer\Product\ProductService;
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
                    'color_id',
                    'size_id',
                    'min_price',
                    'max_price',
                    'is_featured',
                    'sort',
                    'per_page',
                ])
            );

        return ApiResponse::paginated(
            ProductListResource::collection($products),
            $products,
            'Lấy danh sách sản phẩm thành công'
        );
    }

    public function show(
        string $slug
    ): JsonResponse {
        $product = $this->productService
            ->findBySlug($slug);

        return ApiResponse::success(
            new ProductDetailResource($product),
            'Lấy thông tin sản phẩm thành công'
        );
    }
}