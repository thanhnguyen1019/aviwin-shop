<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Dashboard\DashboardRequest;
use App\Http\Resources\Admin\Dashboard\DashboardResource;
use App\Services\Admin\Dashboard\DashboardService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {
    }

    public function index(
    DashboardRequest $request
): JsonResponse {
    $dashboard = $this
        ->dashboardService
        ->getDashboard(
            $request->validated()
        );

    return ApiResponse::success(
        new DashboardResource(
            $dashboard
        ),
        'Lấy dữ liệu dashboard thành công'
    );
}
}