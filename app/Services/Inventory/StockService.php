<?php

namespace App\Services\Inventory;

use App\Models\ProductVariant;
use App\Models\StockHistory;
use Illuminate\Validation\ValidationException;

class StockService
{
    public function change(
        ProductVariant $variant,
        int $quantityChange,
        string $reason,
        ?int $changedBy = null,
        ?string $type = null
    ): ProductVariant {
        if ($quantityChange === 0) {
            throw ValidationException::withMessages([
                'stock' => [
                    'Số lượng thay đổi tồn kho phải khác 0.',
                ],
            ]);
        }

        $stockBefore = (int) $variant->stock;

        $stockAfter = $stockBefore + $quantityChange;

        if ($stockAfter < 0) {
            throw ValidationException::withMessages([
                'stock' => [
                    'Tồn kho không đủ để thực hiện thao tác.',
                ],
            ]);
        }

        $type ??= $quantityChange > 0
            ? StockHistory::TYPE_INCREASE
            : StockHistory::TYPE_DECREASE;

        $variant->update([
            'stock' => $stockAfter,
        ]);

        StockHistory::create([
            'product_variant_id' => $variant->id,
            'type' => $type,
            'quantity_change' => $quantityChange,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'reason' => $reason,
            'changed_by' => $changedBy,
        ]);

        return $variant;
    }
}