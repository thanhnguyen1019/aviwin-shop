<?php

namespace App\Http\Resources\Admin\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray(
        Request $request
    ): array {
        return [
            'id' => $this->id,

            'name' => $this->name,

            'email' => $this->email,

            'role' => $this->role,

            'orders_count' => (int) (
                $this->orders_count ?? 0
            ),

            'total_spent' => (float) (
                $this->total_spent ?? 0
            ),

            'email_verified_at' => $this->email_verified_at
                ?->toDateTimeString(),

            'created_at' => $this->created_at
                ?->toDateTimeString(),
        ];
    }
}