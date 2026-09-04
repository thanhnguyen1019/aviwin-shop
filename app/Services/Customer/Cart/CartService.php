<?php

namespace App\Services\Customer\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function getCart(
        User $user
    ): Cart {
        $cart = Cart::firstOrCreate([
            'user_id' => $user->id,
        ]);

        return $this->loadCart($cart);
    }

    public function addItem(
        User $user,
        int $variantId,
        int $quantity
    ): Cart {
        return DB::transaction(function () use (
            $user,
            $variantId,
            $quantity
        ) {
            $variant = ProductVariant::query()
                ->with('product')
                ->lockForUpdate()
                ->findOrFail($variantId);

            $this->ensureVariantSellable(
                $variant
            );

            $cart = Cart::firstOrCreate([
                'user_id' => $user->id,
            ]);

            $item = $cart->items()
                ->where(
                    'product_variant_id',
                    $variant->id
                )
                ->first();

            $newQuantity = $quantity;

            if ($item) {
                $newQuantity += $item->quantity;
            }

            $this->ensureStockAvailable(
                $variant,
                $newQuantity
            );

            if ($item) {
                $item->update([
                    'quantity' => $newQuantity,
                ]);
            } else {
                $cart->items()->create([
                    'product_variant_id' => $variant->id,
                    'quantity' => $quantity,
                ]);
            }

            return $this->loadCart($cart);
        });
    }

    public function updateItem(
        User $user,
        CartItem $item,
        int $quantity
    ): Cart {
        $cart = $this->getUserCartForItem(
            $user,
            $item
        );

        DB::transaction(function () use (
            $item,
            $quantity
        ) {
            $variant = ProductVariant::query()
                ->with('product')
                ->lockForUpdate()
                ->findOrFail(
                    $item->product_variant_id
                );

            $this->ensureVariantSellable(
                $variant
            );

            $this->ensureStockAvailable(
                $variant,
                $quantity
            );

            $item->update([
                'quantity' => $quantity,
            ]);
        });

        return $this->loadCart($cart);
    }

    public function deleteItem(
        User $user,
        CartItem $item
    ): Cart {
        $cart = $this->getUserCartForItem(
            $user,
            $item
        );

        $item->delete();

        return $this->loadCart($cart);
    }

    public function clear(
        User $user
    ): Cart {
        $cart = Cart::firstOrCreate([
            'user_id' => $user->id,
        ]);

        $cart->items()->delete();

        return $this->loadCart($cart);
    }

    private function loadCart(
        Cart $cart
    ): Cart {
        return $cart->load([
            'items.variant.product',
            'items.variant.color',
            'items.variant.size',
        ]);
    }

    private function ensureVariantSellable(
        ProductVariant $variant
    ): void {
        if (!$variant->is_active) {
            throw ValidationException::withMessages([
                'product_variant_id' => [
                    'Biến thể sản phẩm hiện không khả dụng.',
                ],
            ]);
        }

        if (!$variant->product?->is_active) {
            throw ValidationException::withMessages([
                'product_variant_id' => [
                    'Sản phẩm hiện không khả dụng.',
                ],
            ]);
        }
    }

    private function ensureStockAvailable(
        ProductVariant $variant,
        int $quantity
    ): void {
        if ($quantity > $variant->stock) {
            throw ValidationException::withMessages([
                'quantity' => [
                    "Số lượng vượt quá tồn kho hiện tại ({$variant->stock}).",
                ],
            ]);
        }
    }

    private function getUserCartForItem(
        User $user,
        CartItem $item
    ): Cart {
        $cart = Cart::query()
            ->where('id', $item->cart_id)
            ->where('user_id', $user->id)
            ->first();

        abort_unless(
            $cart !== null,
            404
        );

        return $cart;
    }
}