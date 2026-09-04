<?php

namespace App\Repositories\Contracts\Customer;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CustomerRepositoryInterface
{
    public function paginate(
        array $filters = []
    ): LengthAwarePaginator;

    public function findDetail(
        User $customer
    ): User;
}