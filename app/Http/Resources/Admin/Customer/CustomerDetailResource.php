<?php

namespace App\Http\Resources\Admin\Customer;

use App\Http\Resources\Admin\Order\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerDetailResource extends JsonResource
{
    public function toArray(
        Request $request
    ): array {
        return [
            'id' => $this->id,

            'name' => $this->name,

            'email' => $this->email,

            'role' => $this->role,

            'email_verified_at' => $this->email_verified_at
                ?->toDateTimeString(),

            'statistics' => [
                'orders_count' => (int) (
                    $this->orders_count ?? 0
                ),

                'completed_orders_count' => (int) (
                    $this->completed_orders_count ?? 0
                ),

                'cancelled_orders_count' => (int) (
                    $this->cancelled_orders_count ?? 0
                ),

                'total_spent' => (float) (
                    $this->total_spent ?? 0
                ),
            ],

            'addresses' => CustomerAddressResource::collection(
                $this->whenLoaded(
                    'addresses'
                )
            ),

            'recent_orders' => OrderResource::collection(
                $this->whenLoaded(
                    'orders'
                )
            ),

            'created_at' => $this->created_at
                ?->toDateTimeString(),

            'updated_at' => $this->updated_at
                ?->toDateTimeString(),
                'account_status' => [
    'is_active' => (bool) $this->is_active,

    'blocked_at' => $this->blocked_at
        ?->toDateTimeString(),

    'blocked_reason' => $this->blocked_reason,
],
        ];
    }
}