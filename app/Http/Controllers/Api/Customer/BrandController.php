<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\Brand\BrandResource;
use App\Services\Customer\Brand\BrandService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class BrandController extends Controller
{
    public function __construct(
        protected BrandService $brandService
    ) {
    }

    public function index(): JsonResponse
    {
        $brands = $this->brandService
            ->getActiveBrands();

        return ApiResponse::success(
            BrandResource::collection($brands),
            'Lấy danh sách thương hiệu thành công'
        );
    }

    public function show(
        string $slug
    ): JsonResponse {
        $brand = $this->brandService
            ->findBySlug($slug);

        return ApiResponse::success(
            new BrandResource($brand),
            'Lấy thông tin thương hiệu thành công'
        );
    }
}