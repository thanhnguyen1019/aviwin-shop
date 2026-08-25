<?php

namespace App\Services\Customer\Size;

use App\Models\Size;
use Illuminate\Database\Eloquent\Collection;

class SizeService
{
    public function getActiveSizes(): Collection
    {
        return Size::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }
}