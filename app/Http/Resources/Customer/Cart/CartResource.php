<?php

namespace App\Http\Resources\Customer\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $items = CartItemResource::collection(
            $this->whenLoaded('items')
        );

        $totalQuantity = $this->items->sum(
            'quantity'
        );

        $totalAmount = $this->items->sum(
            function ($item) {
                $variant = $item->variant;
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

                return $finalPrice
                    * $item->quantity;
            }
        );

        return [
            'id' => $this->id,

            'items' => $items,

            'summary' => [
                'total_items' => $this->items->count(),
                'total_quantity' => $totalQuantity,
                'total_amount' => $totalAmount,
            ],
        ];
    }
}