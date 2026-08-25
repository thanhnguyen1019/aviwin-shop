<?php

namespace App\Repositories\Eloquent\Product;

use App\Models\Product;
use App\Repositories\Contracts\Product\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    public function paginate(
        array $filters = []
    ): LengthAwarePaginator {
        $query = Product::query()
            ->with([
                'category',
                'brand',
            ]);

        if (!empty($filters['keyword'])) {
            $keyword = trim(
                $filters['keyword']
            );

            $query->where(function ($q) use ($keyword) {
                $q->where(
                    'name',
                    'like',
                    "%{$keyword}%"
                )
                ->orWhere(
                    'code',
                    'like',
                    "%{$keyword}%"
                )
                ->orWhere(
                    'slug',
                    'like',
                    "%{$keyword}%"
                );
            });
        }

        if (!empty($filters['category_id'])) {
            $query->where(
                'category_id',
                (int) $filters['category_id']
            );
        }

        if (!empty($filters['brand_id'])) {
            $query->where(
                'brand_id',
                (int) $filters['brand_id']
            );
        }

        if (array_key_exists('is_active', $filters)) {
            $query->where(
                'is_active',
                filter_var(
                    $filters['is_active'],
                    FILTER_VALIDATE_BOOLEAN
                )
            );
        }

        if (array_key_exists('is_featured', $filters)) {
            $query->where(
                'is_featured',
                filter_var(
                    $filters['is_featured'],
                    FILTER_VALIDATE_BOOLEAN
                )
            );
        }

        $perPage = (int) (
            $filters['per_page'] ?? 15
        );

        $perPage = max(
            1,
            min($perPage, 100)
        );

        return $query
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function create(
        array $data
    ): Product {
        return Product::create($data);
    }

    public function update(
        Product $product,
        array $data
    ): Product {
        $product->update($data);

        return $product->refresh();
    }

    public function delete(
        Product $product
    ): void {
        $product->delete();
    }
}