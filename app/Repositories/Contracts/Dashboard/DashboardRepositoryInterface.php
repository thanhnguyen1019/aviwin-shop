<?php

namespace App\Repositories\Contracts\Dashboard;

use Illuminate\Support\Collection;

interface DashboardRepositoryInterface
{
    public function summary(): array;

    public function ordersByStatus(): array;

    public function revenueChart(
        string $fromDate,
        string $toDate
    ): Collection;

    public function recentOrders(
        int $limit = 10
    ): Collection;

    public function topProducts(
        string $fromDate,
        string $toDate,
        int $limit = 10
    ): Collection;
}