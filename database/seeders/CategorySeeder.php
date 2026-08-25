<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $shoes = Category::create([
            'name' => 'Giày',
            'slug' => 'giay',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Category::create([
            'parent_id' => $shoes->id,
            'name' => 'Giày nam',
            'slug' => 'giay-nam',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Category::create([
            'parent_id' => $shoes->id,
            'name' => 'Giày nữ',
            'slug' => 'giay-nu',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        Category::create([
            'name' => 'Phụ kiện',
            'slug' => 'phu-kien',
            'is_active' => true,
            'sort_order' => 2,
        ]);
    }
}