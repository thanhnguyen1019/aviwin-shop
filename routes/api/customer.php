<?php

use App\Http\Controllers\Api\Customer\AddressController;
use App\Http\Controllers\Api\Customer\Auth\AuthController;
use App\Http\Controllers\Api\Customer\BrandController;
use App\Http\Controllers\Api\Customer\CategoryController;
use App\Http\Controllers\Api\Customer\ColorController;
use App\Http\Controllers\Api\Customer\ProductController;
use App\Http\Controllers\Api\Customer\SizeController;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return ApiResponse::success(
        null,
        'Customer API hoạt động'
    );
});

Route::get('/categories', [CategoryController::class, 'index']);

Route::get('/categories/{slug}', [CategoryController::class, 'show']);

Route::get(
    '/brands',
    [BrandController::class, 'index']
);

Route::get(
    '/brands/{slug}',
    [BrandController::class, 'show']
);

Route::get(
    '/colors',
    [ColorController::class, 'index']
);

Route::get(
    '/sizes',
    [SizeController::class, 'index']
);

Route::get(
    '/products',
    [ProductController::class, 'index']
);

Route::get(
    '/products/{slug}',
    [ProductController::class, 'show']
);

Route::post(
    '/register',
    [AuthController::class, 'register']
);

Route::post(
    '/login',
    [AuthController::class, 'login']
);
Route::middleware('auth:sanctum')
    ->group(function () {

        Route::get(
            '/profile',
            [AuthController::class, 'profile']
        );

        Route::post(
            '/logout',
            [AuthController::class, 'logout']
        );

        Route::put(
    '/profile',
    [AuthController::class, 'updateProfile']
);
Route::put(
    '/profile/password',
    [AuthController::class, 'changePassword']
);

Route::get(
    '/addresses',
    [AddressController::class, 'index']
);

Route::post(
    '/addresses',
    [AddressController::class, 'store']
);

Route::put(
    '/addresses/{address}',
    [AddressController::class, 'update']
);

Route::patch(
    '/addresses/{address}',
    [AddressController::class, 'update']
);

Route::delete(
    '/addresses/{address}',
    [AddressController::class, 'destroy']
);

    });