<?php

namespace App\Http\Resources\Admin\Inventory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'sku' => $this->sku,

            'stock' => (int) $this->stock,

            'is_active' => (bool) $this->is_active,

            'price' => $this->price !== null
                ? (float) $this->price
                : null,

            'sale_price' => $this->sale_price !== null
                ? (float) $this->sale_price
                : null,

            'product' => $this->whenLoaded(
                'product',
                function () {
                    if (!$this->product) {
                        return null;
                    }

                    return [
                        'id' => $this->product->id,
                        'name' => $this->product->name,
                        'slug' => $this->product->slug,
                        'code' => $this->product->code,
                        'thumbnail' => $this->product->thumbnail,
                        'is_active' => (bool) $this->product->is_active,
                    ];
                }
            ),

            'color' => $this->whenLoaded(
                'color',
                function () {
                    if (!$this->color) {
                        return null;
                    }

                    return [
                        'id' => $this->color->id,
                        'name' => $this->color->name,
                        'code' => $this->color->code,
                    ];
                }
            ),

            'size' => $this->whenLoaded(
                'size',
                function () {
                    if (!$this->size) {
                        return null;
                    }

                    return [
                        'id' => $this->size->id,
                        'name' => $this->size->name,
                    ];
                }
            ),

            'created_at' => $this->created_at
                ?->toDateTimeString(),

            'updated_at' => $this->updated_at
                ?->toDateTimeString(),
        ];
    }
}