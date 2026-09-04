<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Checkout\CheckoutRequest;
use App\Http\Resources\Customer\Order\OrderResource;
use App\Services\Customer\Checkout\CheckoutService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class CheckoutController extends Controller
{
    public function __construct(
        protected CheckoutService $checkoutService
    ) {
    }

    public function store(
        CheckoutRequest $request
    ): JsonResponse {
        $order = $this->checkoutService
            ->checkout(
                $request->user(),
                $request->validated()
            );

        return ApiResponse::success(
            new OrderResource($order),
            'Đặt hàng thành công',
            201
        );
    }
}