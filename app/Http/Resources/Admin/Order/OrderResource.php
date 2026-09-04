<?php

namespace App\Http\Resources\Admin\Order;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'code' => $this->code,

            'status' => $this->status,

            'payment_status' => $this->payment_status,

            'payment_method' => $this->payment_method,

            'subtotal' => (float) $this->subtotal,

            'discount_amount' => (float) $this->discount_amount,

            'shipping_fee' => (float) $this->shipping_fee,

            'total_amount' => (float) $this->total_amount,

            'customer' => $this->whenLoaded(
                'user',
                function () {
                    return [
                        'id' => $this->user?->id,
                        'name' => $this->user?->name,
                        'email' => $this->user?->email,
                    ];
                }
            ),

            'receiver' => [
                'name' => $this->receiver_name,
                'phone' => $this->receiver_phone,
                'province_name' => $this->province_name,
                'district_name' => $this->district_name,
                'ward_name' => $this->ward_name,
                'address_line' => $this->address_line,
            ],

            'note' => $this->note,

            'cancel_reason' => $this->cancel_reason,

            'items_count' => $this->when(
                isset($this->items_count),
                $this->items_count
            ),

            'items' => OrderItemResource::collection(
                $this->whenLoaded('items')
            ),

            'available_statuses' => $this
                ->getAvailableNextStatuses(),

            'ordered_at' => $this->ordered_at
                ?->toDateTimeString(),

            'cancelled_at' => $this->cancelled_at
                ?->toDateTimeString(),

            'created_at' => $this->created_at
                ?->toDateTimeString(),

            'updated_at' => $this->updated_at
                ?->toDateTimeString(),
        ];
    }

    private function getAvailableNextStatuses(): array
    {
        return match ($this->status) {
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

            Order::STATUS_COMPLETED,
            Order::STATUS_CANCELLED => [],

            default => [],
        };
    }
}