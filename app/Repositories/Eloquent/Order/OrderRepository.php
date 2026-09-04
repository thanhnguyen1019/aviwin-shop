<?php

namespace App\Repositories\Eloquent\Order;

use App\Models\Order;
use App\Repositories\Contracts\Order\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class OrderRepository implements OrderRepositoryInterface
{
    public function paginate(
        array $filters = []
    ): LengthAwarePaginator {
        $query = Order::query()
            ->with([
                'user:id,name,email',
            ])
            ->withCount('items');

        /*
        |--------------------------------------------------------------------------
        | Keyword
        |--------------------------------------------------------------------------
        |
        | Tìm theo:
        | - order code
        | - receiver_name
        | - receiver_phone
        | - user name
        | - user email
        |
        */

        if (!empty($filters['keyword'])) {
            $keyword = trim(
                $filters['keyword']
            );

            $query->where(function ($q) use ($keyword) {
                $q->where(
                    'code',
                    'like',
                    "%{$keyword}%"
                )
                    ->orWhere(
                        'receiver_name',
                        'like',
                        "%{$keyword}%"
                    )
                    ->orWhere(
                        'receiver_phone',
                        'like',
                        "%{$keyword}%"
                    )
                    ->orWhereHas(
                        'user',
                        function ($userQuery) use ($keyword) {
                            $userQuery
                                ->where(
                                    'name',
                                    'like',
                                    "%{$keyword}%"
                                )
                                ->orWhere(
                                    'email',
                                    'like',
                                    "%{$keyword}%"
                                );
                        }
                    );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['status'])) {
            $query->where(
                'status',
                $filters['status']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Payment Status
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['payment_status'])) {
            $query->where(
                'payment_status',
                $filters['payment_status']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Payment Method
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['payment_method'])) {
            $query->where(
                'payment_method',
                $filters['payment_method']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | User
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['user_id'])) {
            $query->where(
                'user_id',
                (int) $filters['user_id']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Date Filter
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['from_date'])) {
            $query->whereDate(
                'ordered_at',
                '>=',
                $filters['from_date']
            );
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate(
                'ordered_at',
                '<=',
                $filters['to_date']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Price Filter
        |--------------------------------------------------------------------------
        */

        if (
            isset($filters['min_total'])
            && $filters['min_total'] !== ''
        ) {
            $query->where(
                'total_amount',
                '>=',
                $filters['min_total']
            );
        }

        if (
            isset($filters['max_total'])
            && $filters['max_total'] !== ''
        ) {
            $query->where(
                'total_amount',
                '<=',
                $filters['max_total']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $perPage = (int) (
            $filters['per_page'] ?? 20
        );

        $perPage = max(
            1,
            min($perPage, 100)
        );

        return $query
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function findDetail(
        Order $order
    ): Order {
        return $order->load([
            'user:id,name,email',
            'items',
        ]);
    }
    public function histories(
    Order $order
): Collection {
    return $order->histories()
        ->with([
            'changer:id,name,email',
        ])
        ->get();
}
}