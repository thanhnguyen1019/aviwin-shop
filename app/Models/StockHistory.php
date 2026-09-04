<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockHistory extends Model
{
    use HasFactory;

    public const TYPE_INCREASE = 'increase';

    public const TYPE_DECREASE = 'decrease';

    public const TYPE_SALE = 'sale';

    public const TYPE_CANCEL_RESTORE = 'cancel_restore';

    protected $fillable = [
        'product_variant_id',
        'type',
        'quantity_change',
        'stock_before',
        'stock_after',
        'reason',
        'changed_by',
    ];

    protected function casts(): array
    {
        return [
            'quantity_change' => 'integer',
            'stock_before' => 'integer',
            'stock_after' => 'integer',
            'changed_by' => 'integer',
        ];
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(
            ProductVariant::class,
            'product_variant_id'
        );
    }

    public function changer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'changed_by'
        );
    }
}