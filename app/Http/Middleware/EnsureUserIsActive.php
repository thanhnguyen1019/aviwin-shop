<?php

namespace App\Http\Middleware;

use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        if (!$user) {
            return ApiResponse::error(
                'Bạn chưa đăng nhập.',
                401
            );
        }

        if (!$user->isActive()) {
            return ApiResponse::error(
                'Tài khoản của bạn hiện đang bị khóa.',
                403
            );
        }

        return $next($request);
    }
}