<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Size\StoreSizeRequest;
use App\Http\Requests\Admin\Size\UpdateSizeRequest;
use App\Http\Resources\Admin\Size\SizeResource;
use App\Models\Size;
use App\Services\Admin\Size\SizeService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    public function __construct(
        protected SizeService $sizeService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $sizes = $this->sizeService->paginate(
            $request->only([
                'keyword',
                'is_active',
                'per_page',
            ])
        );

        return ApiResponse::paginated(
            SizeResource::collection($sizes),
            $sizes,
            'Lấy danh sách size thành công'
        );
    }

    public function store(StoreSizeRequest $request): JsonResponse
    {
        $size = $this->sizeService->create(
            $request->validated()
        );

        return ApiResponse::success(
            new SizeResource($size),
            'Tạo size thành công',
            201
        );
    }

    public function show(Size $size): JsonResponse
    {
        return ApiResponse::success(
            new SizeResource($size),
            'Lấy thông tin size thành công'
        );
    }

    public function update(
        UpdateSizeRequest $request,
        Size $size
    ): JsonResponse {
        $size = $this->sizeService->update(
            $size,
            $request->validated()
        );

        return ApiResponse::success(
            new SizeResource($size),
            'Cập nhật size thành công'
        );
    }

    public function destroy(Size $size): JsonResponse
    {
        $this->sizeService->delete($size);

        return ApiResponse::success(
            null,
            'Xóa size thành công'
        );
    }
}