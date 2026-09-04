<?php

namespace App\Http\Resources\Customer\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $variant = $this->variant;
        $product = $variant->product;

        $price = $variant->price !== null
            ? (float) $variant->price
            : (float) $product->price;

        $salePrice = $variant->sale_price !== null
            ? (float) $variant->sale_price
            : (
                $product->sale_price !== null
                    ? (float) $product->sale_price
                    : null
            );

        $finalPrice = $salePrice !== null
            ? $salePrice
            : $price;

        return [
            'id' => $this->id,

            'quantity' => $this->quantity,

            'unit_price' => $finalPrice,

            'subtotal' => $finalPrice * $this->quantity,

            'variant' => [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'stock' => $variant->stock,

                'color' => $variant->color
                    ? [
                        'id' => $variant->color->id,
                        'name' => $variant->color->name,
                        'code' => $variant->color->code,
                    ]
                    : null,

                'size' => $variant->size
                    ? [
                        'id' => $variant->size->id,
                        'name' => $variant->size->name,
                    ]
                    : null,
            ],

            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,

                'thumbnail' => $product->thumbnail,

                'thumbnail_url' => $product->thumbnail
                    ? Storage::disk('public')->url(
                        $product->thumbnail
                    )
                    : null,
            ],
        ];
    }
}