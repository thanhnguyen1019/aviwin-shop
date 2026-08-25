<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\Color\ColorResource;
use App\Services\Customer\Color\ColorService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class ColorController extends Controller
{
    public function __construct(
        protected ColorService $colorService
    ) {
    }

    public function index(): JsonResponse
    {
        $colors = $this->colorService->getActiveColors();

        return ApiResponse::success(
            ColorResource::collection($colors),
            'Lấy danh sách màu thành công'
        );
    }
}