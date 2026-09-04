<?php

namespace App\Services\Admin\Order;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\ProductVariant;
use App\Repositories\Contracts\Order\OrderRepositoryInterface;
use App\Services\Order\OrderStatusHistoryService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
    protected OrderRepositoryInterface $orderRepository,
    protected OrderStatusHistoryService $historyService
) {
}

    public function paginate(
        array $filters = []
    ): LengthAwarePaginator {
        return $this->orderRepository
            ->paginate($filters);
    }

    public function findDetail(
        Order $order
    ): Order {
        return $this->orderRepository
            ->findDetail($order);
    }

    public function updateStatus(
    Order $order,
    string $newStatus,
    ?string $reason = null,
    ?int $changedBy = null
): Order {
    return DB::transaction(function () use (
        $order,
        $newStatus,
        $reason,
        $changedBy
    ) {
        $lockedOrder = Order::query()
            ->whereKey($order->id)
            ->lockForUpdate()
            ->firstOrFail();

        $currentStatus = $lockedOrder->status;

        if ($currentStatus === $newStatus) {
            return $lockedOrder->load([
                'user:id,name,email',
                'items',
            ]);
        }

        $this->ensureStatusTransitionAllowed(
            $currentStatus,
            $newStatus
        );

        if (
            $newStatus === Order::STATUS_CANCELLED
        ) {
            $this->cancelOrder(
                $lockedOrder,
                $reason
            );
        } else {
            $lockedOrder->update([
                'status' => $newStatus,
            ]);
        }

        $this->historyService->create(
            $lockedOrder,
            $currentStatus,
            $newStatus,
            $reason,
            $changedBy,
            OrderStatusHistory::CHANGED_BY_ADMIN
        );

        return $lockedOrder
            ->refresh()
            ->load([
                'user:id,name,email',
                'items',
            ]);
    });
}

    public function updatePaymentStatus(
        Order $order,
        string $paymentStatus
    ): Order {
        return DB::transaction(function () use (
            $order,
            $paymentStatus
        ) {
            $lockedOrder = Order::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensurePaymentTransitionAllowed(
                $lockedOrder,
                $paymentStatus
            );

            $lockedOrder->update([
                'payment_status' => $paymentStatus,
            ]);

            return $lockedOrder
                ->refresh()
                ->load([
                    'user:id,name,email',
                    'items',
                ]);
        });
    }

    private function ensureStatusTransitionAllowed(
        string $currentStatus,
        string $newStatus
    ): void {
        $transitions = [
            Order::STATUS_PENDING => [
                Order::STATUS_CONFIRMED,
                Order::STATUS_CANCELLED,
            ],

            Order::STATUS_CONFIRMED => [
                Order::STATUS_PROCESSING,
                Order::STATUS_CANCELLED,
            ],

            Order::STATUS_PROCESSING => [
                Order::STATUS_SHIPPING,
            ],

            Order::STATUS_SHIPPING => [
                Order::STATUS_COMPLETED,
            ],

            Order::STATUS_COMPLETED => [],

            Order::STATUS_CANCELLED => [],
        ];

        $allowedStatuses =
            $transitions[$currentStatus] ?? [];

        if (
            !in_array(
                $newStatus,
                $allowedStatuses,
                true
            )
        ) {
            throw ValidationException::withMessages([
                'status' => [
                    "Không thể chuyển trạng thái từ {$currentStatus} sang {$newStatus}.",
                ],
            ]);
        }
    }

    private function cancelOrder(
        Order $order,
        ?string $reason
    ): void {
        if (
            $order->payment_status
            === Order::PAYMENT_PAID
        ) {
            throw ValidationException::withMessages([
                'status' => [
                    'Đơn hàng đã thanh toán không thể hủy trực tiếp. Cần xử lý hoàn tiền trước.',
                ],
            ]);
        }

        $items = $order->items()
            ->orderBy('product_variant_id')
            ->get();

        foreach ($items as $item) {
            if (!$item->product_variant_id) {
                continue;
            }

            $variant = ProductVariant::query()
                ->whereKey(
                    $item->product_variant_id
                )
                ->lockForUpdate()
                ->first();

            if (!$variant) {
                continue;
            }

            $variant->increment(
                'stock',
                $item->quantity
            );
        }

        $order->update([
            'status' => Order::STATUS_CANCELLED,
            'cancel_reason' => $reason,
            'cancelled_at' => now(),
        ]);
    }

    private function ensurePaymentTransitionAllowed(
        Order $order,
        string $newStatus
    ): void {
        $currentStatus =
            $order->payment_status;

        if ($currentStatus === $newStatus) {
            return;
        }

        $transitions = [
            Order::PAYMENT_UNPAID => [
                Order::PAYMENT_PAID,
            ],

            Order::PAYMENT_PAID => [
                Order::PAYMENT_REFUNDED,
            ],

            Order::PAYMENT_REFUNDED => [],
        ];

        $allowedStatuses =
            $transitions[$currentStatus] ?? [];

        if (
            !in_array(
                $newStatus,
                $allowedStatuses,
                true
            )
        ) {
            throw ValidationException::withMessages([
                'payment_status' => [
                    "Không thể chuyển trạng thái thanh toán từ {$currentStatus} sang {$newStatus}.",
                ],
            ]);
        }

        if (
            $order->status
            === Order::STATUS_CANCELLED
            && $newStatus === Order::PAYMENT_PAID
        ) {
            throw ValidationException::withMessages([
                'payment_status' => [
                    'Không thể đánh dấu đã thanh toán cho đơn hàng đã hủy.',
                ],
            ]);
        }
    }
    public function histories(
    Order $order
): Collection {
    return $this->orderRepository
        ->histories($order);
}
}