<?php

namespace App\Http\Middleware;

use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
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

        if (!$user->isAdmin()) {
            return ApiResponse::error(
                'Bạn không có quyền truy cập chức năng này.',
                403
            );
        }

        return $next($request);
    }
}