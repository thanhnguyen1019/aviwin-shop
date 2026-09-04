<?php

namespace App\Services\Admin\Inventory;

use App\Models\ProductVariant;
use App\Repositories\Contracts\Inventory\InventoryRepositoryInterface;
use App\Services\Inventory\StockService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function __construct(
        protected InventoryRepositoryInterface $inventoryRepository,
        protected StockService $stockService
    ) {
    }

    public function paginate(
        array $filters = []
    ): LengthAwarePaginator {
        return $this->inventoryRepository
            ->paginate($filters);
    }

    public function histories(
        ProductVariant $variant,
        int $perPage = 20
    ): LengthAwarePaginator {
        return $this->inventoryRepository
            ->histories(
                $variant,
                $perPage
            );
    }

    public function adjustStock(
        ProductVariant $variant,
        int $quantityChange,
        string $reason,
        ?int $changedBy = null
    ): ProductVariant {
        return DB::transaction(function () use (
            $variant,
            $quantityChange,
            $reason,
            $changedBy
        ) {
            $lockedVariant = ProductVariant::query()
                ->whereKey($variant->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->stockService->change(
                $lockedVariant,
                $quantityChange,
                $reason,
                $changedBy
            );

            return $lockedVariant
                ->refresh()
                ->load([
                    'product:id,name,slug,code,thumbnail,is_active',
                    'color:id,name,code',
                    'size:id,name',
                ]);
        });
    }
}