<?php

namespace App\Http\Resources\Admin\Dashboard;

use App\Http\Resources\Admin\Order\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    public function toArray(
        Request $request
    ): array {
        return [
            'period' => $this->resource[
                'period'
            ],

            'summary' => [
                'customers' => (int) (
                    $this->resource[
                        'summary'
                    ]['customers'] ?? 0
                ),

                'orders' => (int) (
                    $this->resource[
                        'summary'
                    ]['orders'] ?? 0
                ),

                'completed_orders' => (int) (
                    $this->resource[
                        'summary'
                    ]['completed_orders'] ?? 0
                ),

                'revenue' => (float) (
                    $this->resource[
                        'summary'
                    ]['revenue'] ?? 0
                ),

                'today_orders' => (int) (
                    $this->resource[
                        'summary'
                    ]['today_orders'] ?? 0
                ),

                'today_revenue' => (float) (
                    $this->resource[
                        'summary'
                    ]['today_revenue'] ?? 0
                ),
            ],

            'orders_by_status' => $this
                ->resource[
                    'orders_by_status'
                ],

            'revenue_chart' => $this
                ->resource[
                    'revenue_chart'
                ],

            'recent_orders' => OrderResource::collection(
                $this->resource[
                    'recent_orders'
                ]
            ),

            'top_products' => $this
                ->resource[
                    'top_products'
                ]
                ->map(function ($item) {
                    return [
                        'product_id' => $item
                            ->product_id,

                        'product_name' => $item
                            ->product_name,

                        'product_slug' => $item
                            ->product_slug,

                        'sold_quantity' => (int) $item
                            ->sold_quantity,

                        'revenue' => (float) $item
                            ->revenue,
                    ];
                })
                ->values(),
        ];
    }
}