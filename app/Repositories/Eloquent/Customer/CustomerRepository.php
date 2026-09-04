<?php

namespace App\Repositories\Eloquent\Customer;

use App\Models\Order;
use App\Models\User;
use App\Repositories\Contracts\Customer\CustomerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function paginate(
        array $filters = []
    ): LengthAwarePaginator {
        $query = User::query()
            ->where(
                'role',
                User::ROLE_CUSTOMER
            )
            ->withCount('orders')
            ->withSum(
                [
                    'orders as total_spent' => function ($query) {
                        $query->where(
                            'status',
                            Order::STATUS_COMPLETED
                        );
                    },
                ],
                'total_amount'
            );

        /*
        |--------------------------------------------------------------------------
        | Keyword
        |--------------------------------------------------------------------------
        |
        | Search:
        | - name
        | - email
        |
        */

        if (!empty($filters['keyword'])) {
            $keyword = trim(
                $filters['keyword']
            );

            $query->where(function ($q) use ($keyword) {
                $q->where(
                    'name',
                    'like',
                    "%{$keyword}%"
                )->orWhere(
                    'email',
                    'like',
                    "%{$keyword}%"
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Registration Date
        |--------------------------------------------------------------------------
        */

        if (!empty($filters['from_date'])) {
            $query->whereDate(
                'created_at',
                '>=',
                $filters['from_date']
            );
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate(
                'created_at',
                '<=',
                $filters['to_date']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Has Orders
        |--------------------------------------------------------------------------
        */

        if (
            isset($filters['has_orders'])
            && $filters['has_orders'] !== ''
        ) {
            if (
                filter_var(
                    $filters['has_orders'],
                    FILTER_VALIDATE_BOOLEAN
                )
            ) {
                $query->has('orders');
            } else {
                $query->doesntHave('orders');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Sort
        |--------------------------------------------------------------------------
        */

        $sort = $filters['sort']
            ?? 'latest';

        switch ($sort) {
            case 'oldest':
                $query->orderBy('id');
                break;

            case 'name_asc':
                $query->orderBy('name');
                break;

            case 'name_desc':
                $query->orderByDesc('name');
                break;

            case 'orders_desc':
                $query->orderByDesc('orders_count');
                break;

            case 'spent_desc':
                $query->orderByDesc('total_spent');
                break;

            default:
                $query->orderByDesc('id');
                break;
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $perPage = (int) (
            $filters['per_page']
            ?? 20
        );

        $perPage = max(
            1,
            min($perPage, 100)
        );

        return $query->paginate(
            $perPage
        );
    }

    public function findDetail(
        User $customer
    ): User {
        return $customer
            ->load([
                'addresses' => function ($query) {
                    $query
                        ->orderByDesc('is_default')
                        ->orderByDesc('id');
                },

                'orders' => function ($query) {
                    $query
                        ->latest('id')
                        ->limit(10);
                },
            ])
            ->loadCount([
                'orders',

                'orders as completed_orders_count' => function ($query) {
                    $query->where(
                        'status',
                        Order::STATUS_COMPLETED
                    );
                },

                'orders as cancelled_orders_count' => function ($query) {
                    $query->where(
                        'status',
                        Order::STATUS_CANCELLED
                    );
                },
            ])
            ->loadSum(
                [
                    'orders as total_spent' => function ($query) {
                        $query->where(
                            'status',
                            Order::STATUS_COMPLETED
                        );
                    },
                ],
                'total_amount'
            );
    }
}