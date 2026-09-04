<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
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
                    'sort',
                    'per_page',
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
}