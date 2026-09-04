<?php

namespace App\Http\Resources\Customer\Order;

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

            'receiver' => [
                'name' => $this->receiver_name,
                'phone' => $this->receiver_phone,
                'province_name' => $this->province_name,
                'district_name' => $this->district_name,
                'ward_name' => $this->ward_name,
                'address_line' => $this->address_line,
            ],

            'note' => $this->note,

            'items' => OrderItemResource::collection(
                $this->whenLoaded('items')
            ),

            'ordered_at' => $this->ordered_at
                ?->toDateTimeString(),

            'created_at' => $this->created_at
                ?->toDateTimeString(),
                'cancel_reason' => $this->cancel_reason,

'cancelled_at' => $this->cancelled_at
    ?->toDateTimeString(),
    'can_cancel' => in_array(
    $this->status,
    [
        Order::STATUS_PENDING,
        Order::STATUS_CONFIRMED,
    ],
    true
) && $this->payment_status
    === Order::PAYMENT_UNPAID,
        ];
    }
}