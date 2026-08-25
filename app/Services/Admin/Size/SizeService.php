<?php

namespace App\Services\Admin\Size;

use App\Models\Size;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SizeService
{
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $query = Size::query();

        if (!empty($filters['keyword'])) {
            $keyword = trim($filters['keyword']);

            $query->where(
                'name',
                'like',
                "%{$keyword}%"
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

        $perPage = (int) ($filters['per_page'] ?? 15);
        $perPage = max(1, min($perPage, 100));

        return $query
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate($perPage);
    }

    public function create(array $data): Size
    {
        return Size::create($data);
    }

    public function update(Size $size, array $data): Size
    {
        $size->update($data);

        return $size->refresh();
    }

    public function delete(Size $size): void
    {
        $size->delete();
    }
}