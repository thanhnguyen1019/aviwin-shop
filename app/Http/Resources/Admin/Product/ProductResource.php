<?php

namespace App\Http\Resources\Admin\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'name' => $this->name,
            'slug' => $this->slug,
            'code' => $this->code,

            'short_description' => $this->short_description,
            'description' => $this->description,

            'price' => $this->price,
            'sale_price' => $this->sale_price,

            'thumbnail' => $this->thumbnail,

            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,

            'sort_order' => $this->sort_order,

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

            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}