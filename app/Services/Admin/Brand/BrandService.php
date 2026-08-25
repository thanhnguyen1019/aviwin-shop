<?php

namespace App\Services\Admin\Brand;

use App\Models\Brand;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BrandService
{
    public function paginate(
        array $filters = []
    ): LengthAwarePaginator {
        $query = Brand::query();

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
                    'slug',
                    'like',
                    "%{$keyword}%"
                );
            });
        }

        if (
            array_key_exists(
                'is_active',
                $filters
            )
        ) {
            $query->where(
                'is_active',
                filter_var(
                    $filters['is_active'],
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
            ->orderBy('id')
            ->paginate($perPage);
    }

    public function create(
        array $data
    ): Brand {
        return Brand::create($data);
    }

    public function update(
        Brand $brand,
        array $data
    ): Brand {
        $brand->update($data);

        return $brand->refresh();
    }

    public function delete(
        Brand $brand
    ): void {
        $brand->delete();
    }
}