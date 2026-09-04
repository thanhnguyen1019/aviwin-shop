<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Order\UpdateOrderStatusRequest;
use App\Http\Requests\Admin\Order\UpdatePaymentStatusRequest;
use App\Http\Resources\Admin\Order\OrderResource;
use App\Http\Resources\Admin\Order\OrderStatusHistoryResource;
use App\Models\Order;
use App\Services\Admin\Order\OrderService;
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
                $request->only([
                    'keyword',
                    'status',
                    'payment_status',
                    'payment_method',
                    'user_id',
                    'from_date',
                    'to_date',
                    'min_total',
                    'max_total',
                    'per_page',
                ])
            );

        return ApiResponse::paginated(
            OrderResource::collection(
                $orders
            ),
            $orders,
            'Lấy danh sách đơn hàng thành công'
        );
    }

    public function show(
        Order $order
    ): JsonResponse {
        $order = $this->orderService
            ->findDetail($order);

        return ApiResponse::success(
            new OrderResource($order),
            'Lấy chi tiết đơn hàng thành công'
        );
    }

    public function updateStatus(
    UpdateOrderStatusRequest $request,
    Order $order
): JsonResponse {
    $order = $this->orderService
        ->updateStatus(
            $order,
            $request->validated('status'),
            $request->validated('reason'),
            $request->user()?->id
        );

    return ApiResponse::success(
        new OrderResource($order),
        'Cập nhật trạng thái đơn hàng thành công'
    );
}

    public function updatePaymentStatus(
        UpdatePaymentStatusRequest $request,
        Order $order
    ): JsonResponse {
        $order = $this->orderService
            ->updatePaymentStatus(
                $order,
                $request->validated(
                    'payment_status'
                )
            );

        return ApiResponse::success(
            new OrderResource($order),
            'Cập nhật trạng thái thanh toán thành công'
        );
    }
    public function histories(
    Order $order
): JsonResponse {
    $histories = $this->orderService
        ->histories($order);

    return ApiResponse::success(
        OrderStatusHistoryResource::collection(
            $histories
        ),
        'Lấy lịch sử trạng thái đơn hàng thành công'
    );
}
}