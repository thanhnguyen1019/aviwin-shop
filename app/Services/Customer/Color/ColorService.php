<?php

namespace App\Services\Customer\Color;

use App\Models\Color;
use Illuminate\Database\Eloquent\Collection;

class ColorService
{
    public function getActiveColors(): Collection
    {
        return Color::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }
}