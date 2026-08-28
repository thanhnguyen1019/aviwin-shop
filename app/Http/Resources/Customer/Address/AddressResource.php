<?php

namespace App\Http\Resources\Customer\Address;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'full_name' => $this->full_name,

            'phone' => $this->phone,

            'province_name' => $this->province_name,

            'district_name' => $this->district_name,

            'ward_name' => $this->ward_name,

            'address_line' => $this->address_line,

            'label' => $this->label,

            'is_default' => $this->is_default,

            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}