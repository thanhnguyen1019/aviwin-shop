<?php

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