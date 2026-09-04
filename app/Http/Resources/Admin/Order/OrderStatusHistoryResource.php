<?php

namespace App\Http\Resources\Admin\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderStatusHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'from_status' => $this->from_status,

            'to_status' => $this->to_status,

            'note' => $this->note,

            'changed_by' => $this->changed_by,

            'changed_by_type' => $this->changed_by_type,

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