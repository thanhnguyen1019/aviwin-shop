<?php

namespace App\Http\Controllers\Api\Customer\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Auth\LoginRequest;
use App\Http\Requests\Customer\Auth\RegisterRequest;
use App\Http\Requests\Customer\Profile\ChangePasswordRequest;
use App\Http\Requests\Customer\Profile\UpdateProfileRequest;
use App\Http\Resources\Customer\Auth\UserResource;
use App\Services\Customer\Auth\AuthService;
use App\Services\Customer\Profile\ProfileService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
         protected AuthService $authService,
    protected ProfileService $profileService
    ) {
    }

    public function register(
        RegisterRequest $request
    ): JsonResponse {
        $result = $this->authService
            ->register(
                $request->validated()
            );

        return ApiResponse::success(
            [
                'user' => new UserResource(
                    $result['user']
                ),
                'token' => $result['token'],
                'token_type' => 'Bearer',
            ],
            'Đăng ký tài khoản thành công',
            201
        );
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
                'user' => new UserResource(
                    $result['user']
                ),
                'token' => $result['token'],
                'token_type' => 'Bearer',
            ],
            'Đăng nhập thành công'
        );
    }

    public function profile(
        Request $request
    ): JsonResponse {
        return ApiResponse::success(
            new UserResource(
                $request->user()
            ),
            'Lấy thông tin tài khoản thành công'
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
            'Đăng xuất thành công'
        );
    }
    public function updateProfile(
    UpdateProfileRequest $request
): JsonResponse {
    $user = $this->profileService
        ->update(
            $request->user(),
            $request->validated()
        );

    return ApiResponse::success(
        new UserResource($user),
        'Cập nhật thông tin tài khoản thành công'
    );
}
public function changePassword(
    ChangePasswordRequest $request
): JsonResponse {
    $this->profileService
        ->changePassword(
            $request->user(),
            $request->validated('current_password'),
            $request->validated('password')
        );

    return ApiResponse::success(
        null,
        'Đổi mật khẩu thành công'
    );
}
}