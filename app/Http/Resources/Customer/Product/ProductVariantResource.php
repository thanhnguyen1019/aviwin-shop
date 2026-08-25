<?php

namespace App\Http\Resources\Customer\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $product = $this->product;

        $price = $this->price !== null
            ? (float) $this->price
            : (float) $product->price;

        $salePrice = $this->sale_price !== null
            ? (float) $this->sale_price
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

            'sku' => $this->sku,

            'price' => $price,

            'sale_price' => $salePrice,

            'final_price' => $finalPrice,

            'stock' => $this->stock,

            'in_stock' => $this->stock > 0,

            'color' => $this->whenLoaded(
                'color',
                function () {
                    return $this->color
                        ? [
                            'id' => $this->color->id,
                            'name' => $this->color->name,
                            'code' => $this->color->code,
                        ]
                        : null;
                }
            ),

            'size' => $this->whenLoaded(
                'size',
                function () {
                    return $this->size
                        ? [
                            'id' => $this->size->id,
                            'name' => $this->size->name,
                        ]
                        : null;
                }
            ),
        ];
    }
}