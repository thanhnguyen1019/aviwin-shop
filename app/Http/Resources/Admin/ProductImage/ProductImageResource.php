<?php

namespace App\Http\Resources\Admin\ProductImage;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,

            'image' => $this->image,

            'url' => $this->image
                ? Storage::disk('public')->url($this->image)
                : null,

            'alt' => $this->alt,

            'sort_order' => $this->sort_order,

            'is_primary' => $this->is_primary,

            'created_at' => $this->created_at?->toDateTimeString(),

            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}