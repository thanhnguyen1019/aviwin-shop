<?php

namespace App\Services\Admin\Category;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryService
{
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $query = Category::query()
            ->with('parent');

        if (!empty($filters['keyword'])) {
            $keyword = trim($filters['keyword']);

            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('slug', 'like', "%{$keyword}%");
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

        if (array_key_exists('parent_id', $filters)) {
            $parentId = $filters['parent_id'];

            if (
                $parentId === null
                || $parentId === ''
                || $parentId === 'null'
            ) {
                $query->whereNull('parent_id');
            } else {
                $query->where(
                    'parent_id',
                    (int) $parentId
                );
            }
        }

        $perPage = (int) ($filters['per_page'] ?? 15);

        $perPage = max(
            1,
            min($perPage, 100)
        );

        return $query
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate($perPage);
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(
        Category $category,
        array $data
    ): Category {
        $category->update($data);

        return $category->refresh();
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
    private function isDescendant(
    Category $category,
    int $parentId
): bool {
    $current = Category::find($parentId);

    while ($current) {
        if ($current->id === $category->id) {
            return true;
        }

        $current = $current->parent;
    }

    return false;
}
}