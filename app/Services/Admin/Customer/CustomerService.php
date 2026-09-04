<?php

namespace App\Services\Admin\Customer;

use App\Models\User;
use App\Repositories\Contracts\Customer\CustomerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CustomerService
{
    public function __construct(
        protected CustomerRepositoryInterface $customerRepository
    ) {
    }

    public function paginate(
        array $filters = []
    ): LengthAwarePaginator {
        return $this->customerRepository
            ->paginate($filters);
    }

    public function findDetail(
        User $customer
    ): User {
        abort_unless(
            $customer->role
                === User::ROLE_CUSTOMER,
            404
        );

        return $this->customerRepository
            ->findDetail($customer);
    }
}