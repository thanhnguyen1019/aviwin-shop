<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Brand\StoreBrandRequest;
use App\Http\Requests\Admin\Brand\UpdateBrandRequest;
use App\Http\Resources\Admin\Brand\BrandResource;
use App\Models\Brand;
use App\Services\Admin\Brand\BrandService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function __construct(
        protected BrandService $brandService
    ) {
    }

    public function index(
        Request $request
    ): JsonResponse {
        $brands = $this->brandService->paginate(
            $request->only([
                'keyword',
                'is_active',
                'per_page',
            ])
        );

        return ApiResponse::paginated(
            BrandResource::collection($brands),
            $brands,
            'Lấy danh sách thương hiệu thành công'
        );
    }

    public function store(
        StoreBrandRequest $request
    ): JsonResponse {
        $brand = $this->brandService->create(
            $request->validated()
        );

        return ApiResponse::success(
            new BrandResource($brand),
            'Tạo thương hiệu thành công',
            201
        );
    }

    public function show(
        Brand $brand
    ): JsonResponse {
        return ApiResponse::success(
            new BrandResource($brand),
            'Lấy thông tin thương hiệu thành công'
        );
    }

    public function update(
        UpdateBrandRequest $request,
        Brand $brand
    ): JsonResponse {
        $brand = $this->brandService->update(
            $brand,
            $request->validated()
        );

        return ApiResponse::success(
            new BrandResource($brand),
            'Cập nhật thương hiệu thành công'
        );
    }

    public function destroy(
        Brand $brand
    ): JsonResponse {
        $this->brandService->delete(
            $brand
        );

        return ApiResponse::success(
            null,
            'Xóa thương hiệu thành công'
        );
    }
}