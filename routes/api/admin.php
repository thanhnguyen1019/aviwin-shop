<?php

use App\Http\Controllers\Api\Admin\Auth\AuthController;
use App\Http\Controllers\Api\Admin\BrandController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\ColorController;
use App\Http\Controllers\Api\Admin\CustomerController;
use App\Http\Controllers\Api\Admin\OrderController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\Admin\ProductImageController;
use App\Http\Controllers\Api\Admin\ProductVariantController;
use App\Http\Controllers\Api\Admin\SizeController;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return ApiResponse::success(
        null,
        'Admin API hoạt động'
    );
});

Route::post(
    '/login',
    [AuthController::class, 'login']
);
Route::middleware([
    'auth:sanctum',
    'admin',
])->group(function () {
    Route::apiResource(
        'categories',
        CategoryController::class
    );
    Route::apiResource(
        'brands',
        BrandController::class
    );
    Route::apiResource(
        'colors',
        ColorController::class
    );

    Route::apiResource(
        'sizes',
        SizeController::class
    );
    Route::apiResource(
        'products',
        ProductController::class
    );

    Route::get(
        '/products/{product}/images',
        [ProductImageController::class, 'index']
    );

    Route::post(
        '/products/{product}/images',
        [ProductImageController::class, 'store']
    );

    Route::patch(
        '/products/{product}/images/{image}/primary',
        [ProductImageController::class, 'setPrimary']
    );

    Route::delete(
        '/products/{product}/images/{image}',
        [ProductImageController::class, 'destroy']
    );

    Route::get(
        '/products/{product}/variants',
        [ProductVariantController::class, 'index']
    );

    Route::post(
        '/products/{product}/variants',
        [ProductVariantController::class, 'store']
    );

    Route::get(
        '/products/{product}/variants/{variant}',
        [ProductVariantController::class, 'show']
    );

    Route::put(
        '/products/{product}/variants/{variant}',
        [ProductVariantController::class, 'update']
    );

    Route::patch(
        '/products/{product}/variants/{variant}',
        [ProductVariantController::class, 'update']
    );

    Route::delete(
        '/products/{product}/variants/{variant}',
        [ProductVariantController::class, 'destroy']
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
        '/orders/{order}/status',
        [OrderController::class, 'updateStatus']
    );

    Route::patch(
        '/orders/{order}/payment-status',
        [OrderController::class, 'updatePaymentStatus']
    );
    Route::get(
        '/orders/{order}/histories',
        [OrderController::class, 'histories']
    );

    Route::get(
    '/customers',
    [CustomerController::class, 'index']
);

Route::get(
    '/customers/{customer}',
    [CustomerController::class, 'show']
);
});