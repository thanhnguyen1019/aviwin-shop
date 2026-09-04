<?php

namespace App\Services\Customer\Order;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\ProductVariant;
use App\Models\StockHistory;
use App\Models\User;
use App\Repositories\Contracts\Order\OrderRepositoryInterface;
use App\Services\Inventory\StockService;
use App\Services\Order\OrderStatusHistoryService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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
        User $user,
        array $filters = []
    ): LengthAwarePaginator {
        $query = $user->orders()
            ->with('items');

        if (!empty($filters['status'])) {
            $query->where(
                'status',
                $filters['status']
            );
        }

        if (!empty($filters['payment_status'])) {
            $query->where(
                'payment_status',
                $filters['payment_status']
            );
        }

        $perPage = (int) (
            $filters['per_page'] ?? 15
        );

        $perPage = max(
            1,
            min($perPage, 100)
        );

        return $query
            ->latest('id')
            ->paginate($perPage);
    }

    public function findByUser(
        User $user,
        Order $order
    ): Order {
        abort_unless(
            $order->user_id === $user->id,
            404
        );

        return $order->load('items');
    }

    public function cancel(
        User $user,
        Order $order,
        ?string $reason = null
    ): Order {
        abort_unless(
            $order->user_id === $user->id,
            404
        );

        return DB::transaction(function () use (
            $user,
            $order,
            $reason
        ) {
            $lockedOrder = Order::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            $fromStatus = $lockedOrder->status;

            $this->ensureOrderCanBeCancelled(
                $lockedOrder
            );

            $items = $lockedOrder->items()
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

                $this->stockService->change(
                    $variant,
                    (int) $item->quantity,
                    'Hoàn tồn kho do khách hàng hủy đơn '
                        . $lockedOrder->code,
                    $user->id,
                    StockHistory::TYPE_CANCEL_RESTORE
                );
            }

            $lockedOrder->update([
                'status' => Order::STATUS_CANCELLED,
                'cancel_reason' => $reason,
                'cancelled_at' => now(),
            ]);

            $this->historyService->create(
                $lockedOrder,
                $fromStatus,
                Order::STATUS_CANCELLED,
                $reason,
                $user->id,
                OrderStatusHistory::CHANGED_BY_CUSTOMER
            );

            return $lockedOrder->load('items');
        });
    }

    private function ensureOrderCanBeCancelled(
        Order $order
    ): void {
        $allowedStatuses = [
            Order::STATUS_PENDING,
            Order::STATUS_CONFIRMED,
        ];

        if (
            !in_array(
                $order->status,
                $allowedStatuses,
                true
            )
        ) {
            throw ValidationException::withMessages([
                'order' => [
                    'Đơn hàng hiện tại không thể hủy.',
                ],
            ]);
        }

        if (
            $order->payment_status
            !== Order::PAYMENT_UNPAID
        ) {
            throw ValidationException::withMessages([
                'order' => [
                    'Đơn hàng đã thanh toán không thể hủy trực tiếp.',
                ],
            ]);
        }
    }
}