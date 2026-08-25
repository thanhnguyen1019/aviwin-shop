<?php

namespace App\Services\Customer\Category;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function getTree(): Collection
    {
        return Category::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->with([
                'children' => function ($query) {
                    $query
                        ->where('is_active', true)
                        ->with([
                            'children' => function ($query) {
                                $query->where('is_active', true);
                            }
                        ]);
                }
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function findBySlug(string $slug): Category
    {
        return Category::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'children' => function ($query) {
                    $query
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->orderBy('name');
                }
            ])
            ->firstOrFail();
    }
}