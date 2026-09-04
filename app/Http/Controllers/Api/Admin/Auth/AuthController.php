<?php

namespace App\Http\Controllers\Api\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\LoginRequest;
use App\Http\Resources\Admin\Auth\AdminResource;
use App\Services\Admin\Auth\AuthService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {
    }

    public function login(
        LoginRequest $request
    ): JsonResponse {
        $result = $this->authService
            ->login(
                $request->validated()
            );

        return ApiResponse::success(
            [
                'admin' => new AdminResource(
                    $result['admin']
                ),

                'token' => $result['token'],
            ],
            'Đăng nhập quản trị thành công'
        );
    }

    public function profile(
        Request $request
    ): JsonResponse {
        return ApiResponse::success(
            new AdminResource(
                $request->user()
            ),
            'Lấy thông tin quản trị viên thành công'
        );
    }

    public function logout(
        Request $request
    ): JsonResponse {
        $this->authService
            ->logout(
                $request->user()
            );

        return ApiResponse::success(
            null,
            'Đăng xuất quản trị thành công'
        );
    }
}