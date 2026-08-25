<?php

namespace App\Repositories\Contracts\Product;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function paginate(
        array $filters = []
    ): LengthAwarePaginator;

    public function create(
        array $data
    ): Product;

    public function update(
        Product $product,
        array $data
    ): Product;

    public function delete(
        Product $product
    ): void;
}