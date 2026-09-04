<?php

namespace App\Services\Admin\ProductVariant;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class ProductVariantService
{
    public function getByProduct(
        Product $product
    ): Collection {
        return $product->variants()
            ->with([
                'color',
                'size',
            ])
            ->orderBy('id')
            ->get();
    }

    public function create(
        Product $product,
        array $data
    ): ProductVariant {
        $this->validatePrices(
            $product,
            $data['price'] ?? null,
            $data['sale_price'] ?? null
        );

        $this->ensureCombinationUnique(
            $product,
            $data['color_id'] ?? null,
            $data['size_id'] ?? null
        );

        $variant = $product->variants()
            ->create($data);

        return $variant->load([
            'color',
            'size',
        ]);
    }

    public function update(
    Product $product,
    ProductVariant $variant,
    array $data
): ProductVariant {
    $this->ensureBelongsToProduct(
        $product,
        $variant
    );

    /*
    |--------------------------------------------------------------------------
    | Stock không được update từ Variant CRUD
    |--------------------------------------------------------------------------
    |
    | Mọi thay đổi tồn kho sau khi Variant đã được tạo
    | phải đi qua Inventory / StockService để có stock history.
    |
    */

    unset(
        $data['stock']
    );

    $colorId = array_key_exists(
        'color_id',
        $data
    )
        ? $data['color_id']
        : $variant->color_id;

    $sizeId = array_key_exists(
        'size_id',
        $data
    )
        ? $data['size_id']
        : $variant->size_id;

    $price = array_key_exists(
        'price',
        $data
    )
        ? $data['price']
        : $variant->price;

    $salePrice = array_key_exists(
        'sale_price',
        $data
    )
        ? $data['sale_price']
        : $variant->sale_price;

    $this->validatePrices(
        $product,
        $price,
        $salePrice
    );

    $this->ensureCombinationUnique(
        $product,
        $colorId,
        $sizeId,
        $variant->id
    );

    $variant->update(
        $data
    );

    return $variant
        ->refresh()
        ->load([
            'color',
            'size',
        ]);
}

    public function delete(
        Product $product,
        ProductVariant $variant
    ): void {
        $this->ensureBelongsToProduct(
            $product,
            $variant
        );

        $variant->delete();
    }

    private function validatePrices(
        Product $product,
        mixed $variantPrice,
        mixed $variantSalePrice
    ): void {
        $effectivePrice = $variantPrice !== null
            ? (float) $variantPrice
            : (float) $product->price;

        $effectiveSalePrice = $variantSalePrice !== null
            ? (float) $variantSalePrice
            : ($product->sale_price !== null
                ? (float) $product->sale_price
                : null);

        if (
            $effectiveSalePrice !== null
            && $effectiveSalePrice > $effectivePrice
        ) {
            throw ValidationException::withMessages([
                'sale_price' => [
                    'Giá khuyến mãi không được lớn hơn giá bán hiệu lực.',
                ],
            ]);
        }
    }

    private function ensureCombinationUnique(
        Product $product,
        mixed $colorId,
        mixed $sizeId,
        ?int $ignoreVariantId = null
    ): void {
        $query = $product->variants();

        if ($colorId === null) {
            $query->whereNull('color_id');
        } else {
            $query->where('color_id', $colorId);
        }

        if ($sizeId === null) {
            $query->whereNull('size_id');
        } else {
            $query->where('size_id', $sizeId);
        }

        if ($ignoreVariantId !== null) {
            $query->where('id', '!=', $ignoreVariantId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'variant' => [
                    'Biến thể với màu và size này đã tồn tại.',
                ],
            ]);
        }
    }

    private function ensureBelongsToProduct(
        Product $product,
        ProductVariant $variant
    ): void {
        abort_unless(
            $variant->product_id === $product->id,
            404
        );
    }
}