<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\OrderStatusHistory;

class OrderStatusHistoryService
{
    public function create(
        Order $order,
        ?string $fromStatus,
        string $toStatus,
        ?string $note = null,
        ?int $changedBy = null,
        string $changedByType = OrderStatusHistory::CHANGED_BY_SYSTEM
    ): OrderStatusHistory {
        return $order->histories()->create([
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'note' => $note,
            'changed_by' => $changedBy,
            'changed_by_type' => $changedByType,
        ]);
    }
}