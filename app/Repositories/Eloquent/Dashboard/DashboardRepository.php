<?php

namespace App\Repositories\Eloquent\Dashboard;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Repositories\Contracts\Dashboard\DashboardRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardRepository implements DashboardRepositoryInterface
{
    public function summary(): array
{
    $startOfDay = now()
        ->startOfDay();

    $endOfDay = now()
        ->endOfDay();

    return [
        'customers' => User::query()
            ->where(
                'role',
                User::ROLE_CUSTOMER
            )
            ->count(),

        'orders' => Order::query()
            ->count(),

        'completed_orders' => Order::query()
            ->where(
                'status',
                Order::STATUS_COMPLETED
            )
            ->count(),

        'revenue' => (float) Order::query()
            ->where(
                'status',
                Order::STATUS_COMPLETED
            )
            ->sum('total_amount'),

        'today_orders' => Order::query()
            ->whereBetween(
                'ordered_at',
                [
                    $startOfDay,
                    $endOfDay,
                ]
            )
            ->count(),

        'today_revenue' => (float) Order::query()
            ->where(
                'status',
                Order::STATUS_COMPLETED
            )
            ->whereBetween(
                'ordered_at',
                [
                    $startOfDay,
                    $endOfDay,
                ]
            )
            ->sum('total_amount'),
    ];
}

    public function ordersByStatus(): array
    {
        $rows = Order::query()
            ->select(
                'status',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('status')
            ->pluck(
                'total',
                'status'
            );

        return [
            Order::STATUS_PENDING => (int) (
                $rows[
                    Order::STATUS_PENDING
                ] ?? 0
            ),

            Order::STATUS_CONFIRMED => (int) (
                $rows[
                    Order::STATUS_CONFIRMED
                ] ?? 0
            ),

            Order::STATUS_PROCESSING => (int) (
                $rows[
                    Order::STATUS_PROCESSING
                ] ?? 0
            ),

            Order::STATUS_SHIPPING => (int) (
                $rows[
                    Order::STATUS_SHIPPING
                ] ?? 0
            ),

            Order::STATUS_COMPLETED => (int) (
                $rows[
                    Order::STATUS_COMPLETED
                ] ?? 0
            ),

            Order::STATUS_CANCELLED => (int) (
                $rows[
                    Order::STATUS_CANCELLED
                ] ?? 0
            ),
        ];
    }

    public function revenueChart(
        string $fromDate,
        string $toDate
    ): Collection {
        return Order::query()
            ->selectRaw(
                'DATE(ordered_at) as date'
            )
            ->selectRaw(
                'COUNT(*) as orders'
            )
            ->selectRaw(
                'SUM(total_amount) as revenue'
            )
            ->where(
                'status',
                Order::STATUS_COMPLETED
            )
            ->whereDate(
                'ordered_at',
                '>=',
                $fromDate
            )
            ->whereDate(
                'ordered_at',
                '<=',
                $toDate
            )
            ->groupByRaw(
                'DATE(ordered_at)'
            )
            ->orderBy('date')
            ->get();
    }

    public function recentOrders(
        int $limit = 10
    ): Collection {
        return Order::query()
            ->with([
                'user:id,name,email',
            ])
            ->withCount('items')
            ->latest('id')
            ->limit($limit)
            ->get();
    }

    public function topProducts(
        string $fromDate,
        string $toDate,
        int $limit = 10
    ): Collection {
        return OrderItem::query()
            ->join(
                'orders',
                'orders.id',
                '=',
                'order_items.order_id'
            )
            ->select([
                'order_items.product_id',
                'order_items.product_name',
                'order_items.product_slug',
            ])
            ->selectRaw(
                'SUM(order_items.quantity) as sold_quantity'
            )
            ->selectRaw(
                'SUM(order_items.subtotal) as revenue'
            )
            ->where(
                'orders.status',
                Order::STATUS_COMPLETED
            )
            ->whereDate(
                'orders.ordered_at',
                '>=',
                $fromDate
            )
            ->whereDate(
                'orders.ordered_at',
                '<=',
                $toDate
            )
            ->groupBy([
                'order_items.product_id',
                'order_items.product_name',
                'order_items.product_slug',
            ])
            ->orderByDesc(
                'sold_quantity'
            )
            ->limit($limit)
            ->get();
    }
}