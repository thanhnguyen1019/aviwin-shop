<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Color\StoreColorRequest;
use App\Http\Requests\Admin\Color\UpdateColorRequest;
use App\Http\Resources\Admin\Color\ColorResource;
use App\Models\Color;
use App\Services\Admin\Color\ColorService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function __construct(
        protected ColorService $colorService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $colors = $this->colorService->paginate(
            $request->only([
                'keyword',
                'is_active',
                'per_page',
            ])
        );

        return ApiResponse::paginated(
            ColorResource::collection($colors),
            $colors,
            'Lấy danh sách màu thành công'
        );
    }

    public function store(StoreColorRequest $request): JsonResponse
    {
        $color = $this->colorService->create(
            $request->validated()
        );

        return ApiResponse::success(
            new ColorResource($color),
            'Tạo màu thành công',
            201
        );
    }

    public function show(Color $color): JsonResponse
    {
        return ApiResponse::success(
            new ColorResource($color),
            'Lấy thông tin màu thành công'
        );
    }

    public function update(
        UpdateColorRequest $request,
        Color $color
    ): JsonResponse {
        $color = $this->colorService->update(
            $color,
            $request->validated()
        );

        return ApiResponse::success(
            new ColorResource($color),
            'Cập nhật màu thành công'
        );
    }

    public function destroy(Color $color): JsonResponse
    {
        $this->colorService->delete($color);

        return ApiResponse::success(
            null,
            'Xóa màu thành công'
        );
    }
}