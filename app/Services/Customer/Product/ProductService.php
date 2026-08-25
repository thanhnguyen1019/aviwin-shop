<?php

namespace App\Services\Customer\Product;

use App\Models\Product;
use App\Repositories\Contracts\Customer\Product\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository
    ) {
    }

    public function paginate(
        array $filters = []
    ): LengthAwarePaginator {
        return $this->productRepository
            ->paginate($filters);
    }

    public function findBySlug(
        string $slug
    ): Product {
        return $this->productRepository
            ->findBySlug($slug);
    }
}