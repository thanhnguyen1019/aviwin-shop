<?php

namespace App\Services\Customer\Brand;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Collection;

class BrandService
{
    public function getActiveBrands(): Collection
    {
        return Brand::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function findBySlug(
        string $slug
    ): Brand {
        return Brand::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
    }
}