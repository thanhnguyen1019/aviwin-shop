<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\Size\SizeResource;
use App\Services\Customer\Size\SizeService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class SizeController extends Controller
{
    public function __construct(
        protected SizeService $sizeService
    ) {
    }

    public function index(): JsonResponse
    {
        $sizes = $this->sizeService->getActiveSizes();

        return ApiResponse::success(
            SizeResource::collection($sizes),
            'Lấy danh sách size thành công'
        );
    }
}