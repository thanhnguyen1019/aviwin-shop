<?php

namespace App\Services\Admin\Dashboard;

use App\Repositories\Contracts\Dashboard\DashboardRepositoryInterface;
use Carbon\CarbonPeriod;

class DashboardService
{
    public function __construct(
        protected DashboardRepositoryInterface $dashboardRepository
    ) {
    }

    public function getDashboard(
        array $filters = []
    ): array {
        [
    $fromDate,
    $toDate,
] = $this->resolvePeriod(
    $filters
);

        $recentLimit = max(
            1,
            min(
                (int) (
                    $filters['recent_limit']
                    ?? 10
                ),
                50
            )
        );

        $topProductLimit = max(
            1,
            min(
                (int) (
                    $filters['top_product_limit']
                    ?? 10
                ),
                50
            )
        );

        $revenueRows = $this
            ->dashboardRepository
            ->revenueChart(
                $fromDate,
                $toDate
            );

        return [
            'period' => [
                'from_date' => $fromDate,
                'to_date' => $toDate,
            ],

            'summary' => $this
                ->dashboardRepository
                ->summary(),

            'orders_by_status' => $this
                ->dashboardRepository
                ->ordersByStatus(),

            'revenue_chart' => $this
                ->fillMissingDates(
                    $revenueRows,
                    $fromDate,
                    $toDate
                ),

            'recent_orders' => $this
                ->dashboardRepository
                ->recentOrders(
                    $recentLimit
                ),

            'top_products' => $this
                ->dashboardRepository
                ->topProducts(
                    $fromDate,
                    $toDate,
                    $topProductLimit
                ),
        ];
    }

    private function fillMissingDates(
        $rows,
        string $fromDate,
        string $toDate
    ): array {
        $rowsByDate = $rows->keyBy(
            'date'
        );

        $result = [];

        foreach (
            CarbonPeriod::create(
                $fromDate,
                $toDate
            ) as $date
        ) {
            $dateString = $date
                ->toDateString();

            $row = $rowsByDate->get(
                $dateString
            );

            $result[] = [
                'date' => $dateString,

                'orders' => (int) (
                    $row->orders
                    ?? 0
                ),

                'revenue' => (float) (
                    $row->revenue
                    ?? 0
                ),
            ];
        }

        return $result;
    }

    private function resolvePeriod(
    array $filters
): array {
    $fromDate = $filters['from_date']
        ?? null;

    $toDate = $filters['to_date']
        ?? null;

    if (!$fromDate && !$toDate) {
        $toDate = now()->toDateString();

        $fromDate = now()
            ->subDays(6)
            ->toDateString();
    } elseif ($fromDate && !$toDate) {
        $toDate = now()->toDateString();
    } elseif (!$fromDate && $toDate) {
        $fromDate = \Carbon\Carbon::parse(
            $toDate
        )
            ->subDays(6)
            ->toDateString();
    }

    return [
        $fromDate,
        $toDate,
    ];
}
}