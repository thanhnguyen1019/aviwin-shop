<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Customer API Routes
|--------------------------------------------------------------------------
|
| Các API dành cho khách hàng.
|
| URL:
| /api/*
|
*/

Route::group([], base_path('routes/api/customer.php'));


/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
|
| Các API dành cho quản trị viên.
|
| URL:
| /api/admin/*
|
*/

Route::prefix('admin')
    ->group(base_path('routes/api/admin.php'));