<?php

namespace App\Services\Admin\Color;

use App\Models\Color;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ColorService
{
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $query = Color::query();

        if (!empty($filters['keyword'])) {
            $keyword = trim($filters['keyword']);

            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('code', 'like', "%{$keyword}%");
            });
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

    public function create(array $data): Color
    {
        return Color::create($data);
    }

    public function update(Color $color, array $data): Color
    {
        $color->update($data);

        return $color->refresh();
    }

    public function delete(Color $color): void
    {
        $color->delete();
    }
}