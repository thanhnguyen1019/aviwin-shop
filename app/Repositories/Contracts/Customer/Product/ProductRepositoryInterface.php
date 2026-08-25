<?php

namespace App\Repositories\Contracts\Customer\Product;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function paginate(
        array $filters = []
    ): LengthAwarePaginator;

    public function findBySlug(
        string $slug
    ): Product;
}