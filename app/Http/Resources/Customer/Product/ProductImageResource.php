<?php

namespace App\Http\Resources\Customer\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'image' => $this->image,

            'url' => $this->image
                ? Storage::disk('public')->url($this->image)
                : null,

            'alt' => $this->alt,

            'is_primary' => $this->is_primary,

            'sort_order' => $this->sort_order,
        ];
    }
}