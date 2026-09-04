<?php

namespace App\Http\Resources\Admin\Inventory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'product_variant_id' => $this->product_variant_id,

            'type' => $this->type,

            'quantity_change' => (int) $this->quantity_change,

            'stock_before' => (int) $this->stock_before,

            'stock_after' => (int) $this->stock_after,

            'reason' => $this->reason,

            'changed_by' => $this->changed_by,

            'changer' => $this->whenLoaded(
                'changer',
                function () {
                    if (!$this->changer) {
                        return null;
                    }

                    return [
                        'id' => $this->changer->id,
                        'name' => $this->changer->name,
                        'email' => $this->changer->email,
                    ];
                }
            ),

            'created_at' => $this->created_at
                ?->toDateTimeString(),
        ];
    }
}