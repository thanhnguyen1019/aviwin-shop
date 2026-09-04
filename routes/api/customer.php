<?php

use App\Http\Controllers\Api\Customer\AddressController;
use App\Http\Controllers\Api\Customer\Auth\AuthController;
use App\Http\Controllers\Api\Customer\BrandController;
use App\Http\Controllers\Api\Customer\CartController;
use App\Http\Controllers\Api\Customer\CategoryController;
use App\Http\Controllers\Api\Customer\CheckoutController;
use App\Http\Controllers\Api\Customer\ColorController;
use App\Http\Controllers\Api\Customer\OrderController;
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
Route::middleware([
    'auth:sanctum',
    'active',
])
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
        Route::get(
            '/cart',
            [CartController::class, 'show']
        );

        Route::post(
            '/cart/items',
            [CartController::class, 'addItem']
        );

        Route::patch(
            '/cart/items/{item}',
            [CartController::class, 'updateItem']
        );

        Route::delete(
            '/cart/items/{item}',
            [CartController::class, 'deleteItem']
        );

        Route::delete(
            '/cart',
            [CartController::class, 'clear']
        );

        Route::post(
            '/checkout',
            [CheckoutController::class, 'store']
        );

        Route::get(
            '/orders',
            [OrderController::class, 'index']
        );

        Route::get(
            '/orders/{order}',
            [OrderController::class, 'show']
        );
        Route::patch(
            '/orders/{order}/cancel',
            [OrderController::class, 'cancel']
        );

    });