<?php

use App\Support\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    */
    ->withMiddleware(function (Middleware $middleware): void {

        /*
        |--------------------------------------------------------------------------
        | Không redirect API guest sang route login
        |--------------------------------------------------------------------------
        |
        | Với request /api/*, nếu chưa đăng nhập thì không redirect sang
        | route('login'). Exception AuthenticationException sẽ được xử lý
        | phía dưới và trả JSON 401.
        |
        */

        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('api/*')) {
                return null;
            }

            return '/login';
        });
    })

    /*
    |--------------------------------------------------------------------------
    | Exceptions
    |--------------------------------------------------------------------------
    */
    ->withExceptions(function (Exceptions $exceptions): void {

        /*
        |--------------------------------------------------------------------------
        | Luôn render JSON cho API
        |--------------------------------------------------------------------------
        */

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) =>
                $request->is('api/*') || $request->expectsJson()
        );


        /*
        |--------------------------------------------------------------------------
        | 422 - Validation Error
        |--------------------------------------------------------------------------
        */

        $exceptions->render(function (
            ValidationException $exception,
            Request $request
        ) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    'Dữ liệu không hợp lệ',
                    422,
                    $exception->errors()
                );
            }
        });


        /*
        |--------------------------------------------------------------------------
        | 401 - Unauthenticated
        |--------------------------------------------------------------------------
        */

        $exceptions->render(function (
            AuthenticationException $exception,
            Request $request
        ) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
    $exception->getMessage() ?: 'Bạn chưa đăng nhập',
    401
);
            }
        });


        /*
        |--------------------------------------------------------------------------
        | 404 - Model Not Found
        |--------------------------------------------------------------------------
        */

        $exceptions->render(function (
            ModelNotFoundException $exception,
            Request $request
        ) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    'Không tìm thấy dữ liệu',
                    404
                );
            }
        });


        /*
        |--------------------------------------------------------------------------
        | 404 - Route Not Found
        |--------------------------------------------------------------------------
        */

        $exceptions->render(function (
            NotFoundHttpException $exception,
            Request $request
        ) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    'API không tồn tại',
                    404
                );
            }
        });


        /*
        |--------------------------------------------------------------------------
        | 403 - Forbidden
        |--------------------------------------------------------------------------
        */

        $exceptions->render(function (
            AccessDeniedHttpException $exception,
            Request $request
        ) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    'Bạn không có quyền thực hiện chức năng này',
                    403
                );
            }
        });
    })
    ->create();