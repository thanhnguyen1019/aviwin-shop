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
        $this->ensureCustomer(
            $customer
        );

        return $this->customerRepository
            ->findDetail($customer);
    }

    public function block(
        User $customer,
        string $reason
    ): User {
        $this->ensureCustomer(
            $customer
        );

        if ($customer->isBlocked()) {
            return $customer;
        }

        $customer->update([
            'is_active' => false,
            'blocked_at' => now(),
            'blocked_reason' => $reason,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Revoke all tokens
        |--------------------------------------------------------------------------
        |
        | Customer bị khóa thì tất cả Bearer token hiện tại
        | phải mất hiệu lực ngay.
        |
        */

        $customer->tokens()->delete();

        return $customer->refresh();
    }

    public function unblock(
        User $customer
    ): User {
        $this->ensureCustomer(
            $customer
        );

        if ($customer->isActive()) {
            return $customer;
        }

        $customer->update([
            'is_active' => true,
            'blocked_at' => null,
            'blocked_reason' => null,
        ]);

        return $customer->refresh();
    }

    private function ensureCustomer(
        User $customer
    ): void {
        abort_unless(
            $customer->role
                === User::ROLE_CUSTOMER,
            404
        );
    }
}