<?php

namespace App\Http\Resources\Admin\ProductVariant;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'sku' => $this->sku,

            'price' => $this->price,
            'sale_price' => $this->sale_price,
            'stock' => $this->stock,
            'is_active' => $this->is_active,

            'color' => $this->whenLoaded('color', function () {
                return $this->color
                    ? [
                        'id' => $this->color->id,
                        'name' => $this->color->name,
                        'code' => $this->color->code,
                    ]
                    : null;
            }),

            'size' => $this->whenLoaded('size', function () {
                return $this->size
                    ? [
                        'id' => $this->size->id,
                        'name' => $this->size->name,
                    ]
                    : null;
            }),

            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}