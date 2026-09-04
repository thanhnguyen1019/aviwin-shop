<?php

namespace App\Http\Resources\Customer\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'product_id' => $this->product_id,

            'product_variant_id' => $this->product_variant_id,

            'product_name' => $this->product_name,

            'product_slug' => $this->product_slug,

            'sku' => $this->sku,

            'color_name' => $this->color_name,

            'size_name' => $this->size_name,

            'unit_price' => (float) $this->unit_price,

            'quantity' => $this->quantity,

            'subtotal' => (float) $this->subtotal,
        ];
    }
}