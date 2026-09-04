<?php

namespace App\Repositories\Contracts\Order;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryInterface
{
    public function paginate(
        array $filters = []
    ): LengthAwarePaginator;

    public function findDetail(
        Order $order
    ): Order;

    public function histories(
        Order $order
    ): Collection;
}