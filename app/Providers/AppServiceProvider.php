<?php

namespace App\Providers;

use App\Repositories\Contracts\Customer\CustomerRepositoryInterface;
use App\Repositories\Contracts\Dashboard\DashboardRepositoryInterface;
use App\Repositories\Contracts\Order\OrderRepositoryInterface;
use App\Repositories\Contracts\Product\ProductRepositoryInterface;
use App\Repositories\Eloquent\Customer\CustomerRepository;
use App\Repositories\Eloquent\Dashboard\DashboardRepository;
use App\Repositories\Eloquent\Order\OrderRepository;
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
        $this->app->bind(
            OrderRepositoryInterface::class,
            OrderRepository::class
        );
        $this->app->bind(
            CustomerRepositoryInterface::class,
            CustomerRepository::class
        );
        $this->app->bind(
            DashboardRepositoryInterface::class,
            DashboardRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}