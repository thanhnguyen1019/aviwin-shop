<?php

namespace App\Services\Admin\Product;

use App\Models\Product;
use App\Repositories\Contracts\Product\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class ProductService
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository
    ) {
    }

    public function paginate(
        array $filters = []
    ): LengthAwarePaginator {
        return $this->productRepository
            ->paginate($filters);
    }

    public function create(
        array $data
    ): Product {
        $this->validatePrices(
            $data['price'],
            $data['sale_price'] ?? null
        );

        return $this->productRepository
            ->create($data);
    }

    public function update(
        Product $product,
        array $data
    ): Product {
        $price = array_key_exists('price', $data)
            ? (float) $data['price']
            : (float) $product->price;

        $salePrice = array_key_exists('sale_price', $data)
            ? $data['sale_price']
            : $product->sale_price;

        $this->validatePrices(
            $price,
            $salePrice
        );

        return $this->productRepository
            ->update(
                $product,
                $data
            );
    }

    public function delete(
        Product $product
    ): void {
        $this->productRepository
            ->delete($product);
    }

    private function validatePrices(
        float $price,
        mixed $salePrice
    ): void {
        if ($salePrice === null) {
            return;
        }

        if ((float) $salePrice > $price) {
            throw ValidationException::withMessages([
                'sale_price' => [
                    'Giá khuyến mãi không được lớn hơn giá bán.',
                ],
            ]);
        }
    }
}