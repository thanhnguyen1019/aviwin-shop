<?php

namespace App\Services\Admin\Order;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\ProductVariant;
use App\Models\StockHistory;
use App\Repositories\Contracts\Order\OrderRepositoryInterface;
use App\Services\Inventory\StockService;
use App\Services\Order\OrderStatusHistoryService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        protected OrderRepositoryInterface $orderRepository,
        protected OrderStatusHistoryService $historyService,
        protected StockService $stockService
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
            /*
            |--------------------------------------------------------------------------
            | Lock Order
            |--------------------------------------------------------------------------
            */

            $lockedOrder = Order::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            $currentStatus = $lockedOrder->status;

            /*
            |--------------------------------------------------------------------------
            | Idempotent
            |--------------------------------------------------------------------------
            |
            | Nếu status mới giống status hiện tại thì không update,
            | không tạo history và đặc biệt không restore stock lần nữa.
            |
            */

            if ($currentStatus === $newStatus) {
                return $lockedOrder->load([
                    'user:id,name,email',
                    'items',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Validate Order Status Transition
            |--------------------------------------------------------------------------
            */

            $this->ensureStatusTransitionAllowed(
                $currentStatus,
                $newStatus
            );

            /*
            |--------------------------------------------------------------------------
            | Update Status
            |--------------------------------------------------------------------------
            */

            if (
                $newStatus === Order::STATUS_CANCELLED
            ) {
                $this->cancelOrder(
                    $lockedOrder,
                    $reason,
                    $changedBy
                );
            } else {
                $lockedOrder->update([
                    'status' => $newStatus,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Order Status History
            |--------------------------------------------------------------------------
            */

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

            /*
            |--------------------------------------------------------------------------
            | Idempotent
            |--------------------------------------------------------------------------
            */

            if (
                $lockedOrder->payment_status
                === $paymentStatus
            ) {
                return $lockedOrder->load([
                    'user:id,name,email',
                    'items',
                ]);
            }

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
        ?string $reason = null,
        ?int $changedBy = null
    ): void {
        /*
        |--------------------------------------------------------------------------
        | Chỉ cho hủy trực tiếp đơn chưa thanh toán
        |--------------------------------------------------------------------------
        |
        | Trước đây code chỉ chặn PAYMENT_PAID.
        | Như vậy PAYMENT_REFUNDED vẫn có thể lọt vào cancel.
        |
        | Quy tắc an toàn:
        | direct cancel chỉ dành cho PAYMENT_UNPAID.
        |
        */

        if (
            $order->payment_status
            !== Order::PAYMENT_UNPAID
        ) {
            throw ValidationException::withMessages([
                'status' => [
                    'Chỉ có thể hủy trực tiếp đơn hàng chưa thanh toán.',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Load Order Items theo thứ tự Variant ID
        |--------------------------------------------------------------------------
        |
        | Lock variant theo cùng một thứ tự giúp giảm nguy cơ deadlock.
        |
        */

        $items = $order->items()
            ->orderBy('product_variant_id')
            ->get();

        foreach ($items as $item) {
            /*
            |--------------------------------------------------------------------------
            | Variant đã bị xóa
            |--------------------------------------------------------------------------
            */

            if (!$item->product_variant_id) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Lock Variant
            |--------------------------------------------------------------------------
            */

            $variant = ProductVariant::query()
                ->whereKey(
                    $item->product_variant_id
                )
                ->lockForUpdate()
                ->first();

            if (!$variant) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Restore Stock + Create Stock History
            |--------------------------------------------------------------------------
            */

            $this->stockService->change(
                $variant,
                (int) $item->quantity,
                'Hoàn tồn kho do Admin hủy đơn '
                    . $order->code,
                $changedBy,
                StockHistory::TYPE_CANCEL_RESTORE
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Update Order
        |--------------------------------------------------------------------------
        */

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