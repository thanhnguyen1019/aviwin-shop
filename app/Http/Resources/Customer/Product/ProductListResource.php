<?php

namespace App\Http\Resources\Customer\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $price = (float) $this->price;

        $salePrice = $this->sale_price !== null
            ? (float) $this->sale_price
            : null;

        $finalPrice = $salePrice !== null
            ? $salePrice
            : $price;

        return [
            'id' => $this->id,

            'name' => $this->name,

            'slug' => $this->slug,

            'code' => $this->code,

            'short_description' => $this->short_description,

            'price' => $price,

            'sale_price' => $salePrice,

            'final_price' => $finalPrice,

            'is_sale' => (
                $salePrice !== null
                && $salePrice < $price
            ),

            'is_featured' => $this->is_featured,

            'thumbnail' => $this->thumbnail,

            'thumbnail_url' => $this->thumbnail
                ? Storage::disk('public')->url($this->thumbnail)
                : null,

            'total_stock' => (int) ($this->total_stock ?? 0),

            'in_stock' => (int) ($this->total_stock ?? 0) > 0,

            'category' => $this->whenLoaded(
                'category',
                function () {
                    return [
                        'id' => $this->category->id,
                        'name' => $this->category->name,
                        'slug' => $this->category->slug,
                    ];
                }
            ),

            'brand' => $this->whenLoaded(
                'brand',
                function () {
                    if (!$this->brand) {
                        return null;
                    }

                    return [
                        'id' => $this->brand->id,
                        'name' => $this->brand->name,
                        'slug' => $this->brand->slug,
                    ];
                }
            ),
        ];
    }
}