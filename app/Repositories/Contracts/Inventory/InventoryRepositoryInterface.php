<?php

namespace App\Repositories\Contracts\Inventory;

use App\Models\ProductVariant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface InventoryRepositoryInterface
{
    public function paginate(
        array $filters = []
    ): LengthAwarePaginator;

    public function histories(
        ProductVariant $variant,
        int $perPage = 20
    ): LengthAwarePaginator;
}