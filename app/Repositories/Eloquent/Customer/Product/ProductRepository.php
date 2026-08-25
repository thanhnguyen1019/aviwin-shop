<?php

namespace App\Repositories\Eloquent\Customer\Product;

use App\Models\Product;
use App\Repositories\Contracts\Customer\Product\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    public function paginate(
        array $filters = []
    ): LengthAwarePaginator {
        $query = Product::query()
            ->where('is_active', true)
            ->with([
                'category',
                'brand',
                'images' => function ($query) {
                    $query
                        ->orderByDesc('is_primary')
                        ->orderBy('sort_order');
                },
            ])
            ->withSum([
                'variants as total_stock' => function ($query) {
                    $query->where('is_active', true);
                },
            ], 'stock');

        if (!empty($filters['keyword'])) {
            $keyword = trim($filters['keyword']);

            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('code', 'like', "%{$keyword}%")
                    ->orWhere('slug', 'like', "%{$keyword}%");
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

        if (!empty($filters['color_id'])) {
            $query->whereHas(
                'variants',
                function ($q) use ($filters) {
                    $q->where(
                        'color_id',
                        (int) $filters['color_id']
                    )
                    ->where('is_active', true);
                }
            );
        }

        if (!empty($filters['size_id'])) {
            $query->whereHas(
                'variants',
                function ($q) use ($filters) {
                    $q->where(
                        'size_id',
                        (int) $filters['size_id']
                    )
                    ->where('is_active', true);
                }
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

        if (
            isset($filters['min_price'])
            && $filters['min_price'] !== ''
        ) {
            $query->where(
                'price',
                '>=',
                (float) $filters['min_price']
            );
        }

        if (
            isset($filters['max_price'])
            && $filters['max_price'] !== ''
        ) {
            $query->where(
                'price',
                '<=',
                (float) $filters['max_price']
            );
        }

        $sort = $filters['sort'] ?? 'newest';

        match ($sort) {
            'price_asc' =>
                $query->orderBy('price'),

            'price_desc' =>
                $query->orderByDesc('price'),

            'name_asc' =>
                $query->orderBy('name'),

            'name_desc' =>
                $query->orderByDesc('name'),

            default =>
                $query->orderByDesc('id'),
        };

        $perPage = (int) ($filters['per_page'] ?? 12);

        $perPage = max(
            1,
            min($perPage, 100)
        );

        return $query->paginate($perPage);
    }

    public function findBySlug(
        string $slug
    ): Product {
        return Product::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'category',
                'brand',
                'images' => function ($query) {
                    $query
                        ->orderByDesc('is_primary')
                        ->orderBy('sort_order');
                },
                'variants' => function ($query) {
                    $query
                        ->where('is_active', true)
                        ->with([
                            'color',
                            'size',
                        ])
                        ->orderBy('id');
                },
            ])
            ->firstOrFail();
    }
}