<?php

namespace App\Providers;

use App\Repositories\Contracts\Product\ProductRepositoryInterface;
use App\Repositories\Eloquent\Product\ProductRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\Customer\Product\ProductRepositoryInterface as CustomerProductRepositoryInterface;
use App\Repositories\Eloquent\Customer\Product\ProductRepository as CustomerProductRepository;
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ProductRepositoryInterface::class,
            ProductRepository::class
        );
        $this->app->bind(
        CustomerProductRepositoryInterface::class,
        CustomerProductRepository::class
    );
    }

    public function boot(): void
    {
        //
    }
}