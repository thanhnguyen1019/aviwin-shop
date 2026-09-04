<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Order\CancelOrderRequest;
use App\Http\Resources\Customer\Order\OrderResource;
use App\Models\Order;
use App\Services\Customer\Order\OrderService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {
    }

    public function index(
        Request $request
    ): JsonResponse {
        $orders = $this->orderService
            ->paginate(
                $request->user(),
                $request->only([
                    'status',
                    'payment_status',
                    'per_page',
                ])
            );

        return ApiResponse::paginated(
            OrderResource::collection($orders),
            $orders,
            'Lấy danh sách đơn hàng thành công'
        );
    }

    public function show(
        Request $request,
        Order $order
    ): JsonResponse {
        $order = $this->orderService
            ->findByUser(
                $request->user(),
                $order
            );

        return ApiResponse::success(
            new OrderResource($order),
            'Lấy thông tin đơn hàng thành công'
        );
    }
    public function cancel(
    CancelOrderRequest $request,
    Order $order
): JsonResponse {
    $order = $this->orderService
        ->cancel(
            $request->user(),
            $order,
            $request->validated('reason')
        );

    return ApiResponse::success(
        new OrderResource($order),
        'Hủy đơn hàng thành công'
    );
}
}