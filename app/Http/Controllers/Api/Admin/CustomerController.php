<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Customer\BlockCustomerRequest;
use App\Http\Resources\Admin\Customer\CustomerDetailResource;
use App\Http\Resources\Admin\Customer\CustomerResource;
use App\Models\User;
use App\Services\Admin\Customer\CustomerService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(
        protected CustomerService $customerService
    ) {
    }

    public function index(
        Request $request
    ): JsonResponse {
        $customers = $this->customerService
            ->paginate(
                $request->only([
                    'keyword',
                    'from_date',
                    'to_date',
                    'has_orders',
                    'is_active',
                    'sort',
                    'per_page'
                ])
            );

        return ApiResponse::paginated(
            CustomerResource::collection(
                $customers
            ),
            $customers,
            'Lấy danh sách khách hàng thành công'
        );
    }

    public function show(
        User $customer
    ): JsonResponse {
        $customer = $this->customerService
            ->findDetail(
                $customer
            );

        return ApiResponse::success(
            new CustomerDetailResource(
                $customer
            ),
            'Lấy thông tin khách hàng thành công'
        );
    }

    public function block(
        BlockCustomerRequest $request,
        User $customer
    ): JsonResponse {
        $customer = $this->customerService
            ->block(
                $customer,
                $request->validated('reason')
            );

        return ApiResponse::success(
            new CustomerDetailResource(
                $this->customerService
                    ->findDetail($customer)
            ),
            'Khóa tài khoản khách hàng thành công'
        );
    }
    public function unblock(
        User $customer
    ): JsonResponse {
        $customer = $this->customerService
            ->unblock(
                $customer
            );

        return ApiResponse::success(
            new CustomerDetailResource(
                $this->customerService
                    ->findDetail($customer)
            ),
            'Mở khóa tài khoản khách hàng thành công'
        );
    }
}