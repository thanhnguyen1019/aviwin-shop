<?php

namespace App\Repositories\Eloquent\Inventory;

use App\Models\ProductVariant;
use App\Repositories\Contracts\Inventory\InventoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class InventoryRepository implements InventoryRepositoryInterface
{
    public function paginate(
        array $filters = []
    ): LengthAwarePaginator {
        $query = ProductVariant::query()
            ->with([
                'product:id,name,slug,code,thumbnail,is_active',
                'color:id,name,code',
                'size:id,name',
            ]);

        if (!empty($filters['keyword'])) {
            $keyword = trim($filters['keyword']);

            $query->where(function ($query) use ($keyword) {
                $query
                    ->where(
                        'sku',
                        'like',
                        "%{$keyword}%"
                    )
                    ->orWhereHas(
                        'product',
                        function ($productQuery) use ($keyword) {
                            $productQuery
                                ->where(
                                    'name',
                                    'like',
                                    "%{$keyword}%"
                                )
                                ->orWhere(
                                    'code',
                                    'like',
                                    "%{$keyword}%"
                                );
                        }
                    );
            });
        }

        if (!empty($filters['product_id'])) {
            $query->where(
                'product_id',
                $filters['product_id']
            );
        }

        if (!empty($filters['color_id'])) {
            $query->where(
                'color_id',
                $filters['color_id']
            );
        }

        if (!empty($filters['size_id'])) {
            $query->where(
                'size_id',
                $filters['size_id']
            );
        }

        if (
            isset($filters['is_active'])
            && $filters['is_active'] !== ''
        ) {
            $isActive = filter_var(
                $filters['is_active'],
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            );

            if ($isActive !== null) {
                $query->where(
                    'is_active',
                    $isActive
                );
            }
        }

        if (
            isset($filters['min_stock'])
            && $filters['min_stock'] !== ''
        ) {
            $query->where(
                'stock',
                '>=',
                (int) $filters['min_stock']
            );
        }

        if (
            isset($filters['max_stock'])
            && $filters['max_stock'] !== ''
        ) {
            $query->where(
                'stock',
                '<=',
                (int) $filters['max_stock']
            );
        }

        if (
            isset($filters['low_stock'])
            && $filters['low_stock'] !== ''
        ) {
            $lowStock = filter_var(
                $filters['low_stock'],
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            );

            if ($lowStock === true) {
                $threshold = max(
                    0,
                    (int) (
                        $filters['low_stock_threshold']
                        ?? 5
                    )
                );

                $query->where(
                    'stock',
                    '<=',
                    $threshold
                );
            }
        }

        if (
            isset($filters['out_of_stock'])
            && $filters['out_of_stock'] !== ''
        ) {
            $outOfStock = filter_var(
                $filters['out_of_stock'],
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            );

            if ($outOfStock === true) {
                $query->where('stock', 0);
            }
        }

        $sort = $filters['sort']
            ?? 'stock_asc';

        switch ($sort) {
            case 'stock_desc':
                $query
                    ->orderByDesc('stock')
                    ->orderByDesc('id');
                break;

            case 'sku_asc':
                $query
                    ->orderBy('sku')
                    ->orderBy('id');
                break;

            case 'sku_desc':
                $query
                    ->orderByDesc('sku')
                    ->orderByDesc('id');
                break;

            case 'latest':
                $query->orderByDesc('id');
                break;

            case 'stock_asc':
            default:
                $query
                    ->orderBy('stock')
                    ->orderBy('id');
                break;
        }

        $perPage = (int) (
            $filters['per_page'] ?? 20
        );

        $perPage = max(
            1,
            min($perPage, 100)
        );

        return $query->paginate($perPage);
    }

    public function histories(
        ProductVariant $variant,
        int $perPage = 20
    ): LengthAwarePaginator {
        $perPage = max(
            1,
            min($perPage, 100)
        );

        return $variant
            ->stockHistories()
            ->with([
                'changer:id,name,email',
            ])
            ->paginate($perPage);
    }
}