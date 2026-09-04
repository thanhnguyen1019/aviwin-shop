<?php

namespace App\Services\Customer\Checkout;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\ProductVariant;
use App\Models\StockHistory;
use App\Models\User;
use App\Services\Inventory\StockService;
use App\Services\Order\OrderStatusHistoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public function __construct(
    protected OrderStatusHistoryService $historyService,
    protected StockService $stockService
) {
}
    public function checkout(
    User $user,
    array $data
): Order {
    return DB::transaction(function () use ($user, $data) {
        /*
        |--------------------------------------------------------------------------
        | 1. Kiểm tra địa chỉ nhận hàng
        |--------------------------------------------------------------------------
        */

        $address = Address::query()
            ->where('id', $data['address_id'])
            ->where('user_id', $user->id)
            ->first();

        if (!$address) {
            abort(404);
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Lock giỏ hàng
        |--------------------------------------------------------------------------
        */

        $cart = Cart::query()
            ->where('user_id', $user->id)
            ->lockForUpdate()
            ->first();

        if (!$cart) {
            throw ValidationException::withMessages([
                'cart' => [
                    'Giỏ hàng đang trống.',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Lấy cart items
        |--------------------------------------------------------------------------
        */

        $cartItems = $cart->items()
            ->with([
                'variant.product',
                'variant.color',
                'variant.size',
            ])
            ->orderBy('product_variant_id')
            ->get();

        if ($cartItems->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => [
                    'Giỏ hàng đang trống.',
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Chuẩn bị dữ liệu Order Item + Stock changes
        |--------------------------------------------------------------------------
        */

        $orderItems = [];

        /*
         * Lưu các ProductVariant đã lock để sau khi tạo Order
         * mới thực hiện trừ stock và ghi stock history.
         */
        $stockChanges = [];

        $subtotal = 0;

        foreach ($cartItems as $cartItem) {
            /*
             * Lock từng variant.
             *
             * Vì cart items đã orderBy product_variant_id phía trên
             * nên thứ tự lock ổn định, giúp giảm nguy cơ deadlock.
             */
            $variant = ProductVariant::query()
                ->with([
                    'product',
                    'color',
                    'size',
                ])
                ->lockForUpdate()
                ->findOrFail(
                    $cartItem->product_variant_id
                );

            /*
             * Kiểm tra:
             * - product active
             * - variant active
             * - đủ stock
             */
            $this->ensureVariantCanCheckout(
                $variant,
                $cartItem->quantity
            );

            /*
             * Giá bán thực tế tại thời điểm checkout
             */
            $unitPrice = $this->getFinalPrice(
                $variant
            );

            $itemSubtotal =
                $unitPrice
                * $cartItem->quantity;

            $subtotal += $itemSubtotal;

            /*
             * Snapshot OrderItem
             */
            $orderItems[] = [
                'product_id' => $variant->product_id,

                'product_variant_id' => $variant->id,

                'product_name' => $variant->product->name,

                'product_slug' => $variant->product->slug,

                'sku' => $variant->sku,

                'color_name' => $variant->color?->name,

                'size_name' => $variant->size?->name,

                'unit_price' => $unitPrice,

                'quantity' => $cartItem->quantity,

                'subtotal' => $itemSubtotal,
            ];

            /*
             * CHƯA trừ stock ở đây.
             *
             * Chỉ lưu variant + quantity để sau khi tạo Order
             * mới gọi StockService.
             */
            $stockChanges[] = [
                'variant' => $variant,
                'quantity' => (int) $cartItem->quantity,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Tính tổng đơn hàng
        |--------------------------------------------------------------------------
        */

        $discountAmount = 0;

        $shippingFee = 0;

        $totalAmount =
            $subtotal
            - $discountAmount
            + $shippingFee;

        /*
        |--------------------------------------------------------------------------
        | 6. Tạo Order
        |--------------------------------------------------------------------------
        */

        $order = Order::create([
            'user_id' => $user->id,

            'code' => $this->generateOrderCode(),

            'status' => Order::STATUS_PENDING,

            'payment_status' => Order::PAYMENT_UNPAID,

            'payment_method' => $data['payment_method'],

            'subtotal' => $subtotal,

            'discount_amount' => $totalAmount > 0
                ? $discountAmount
                : 0,

            'shipping_fee' => $shippingFee,

            'total_amount' => $totalAmount,

            'receiver_name' => $address->full_name,

            'receiver_phone' => $address->phone,

            'province_name' => $address->province_name,

            'district_name' => $address->district_name,

            'ward_name' => $address->ward_name,

            'address_line' => $address->address_line,

            'note' => $data['note'] ?? null,

            'ordered_at' => now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | 7. Ghi Order Status History
        |--------------------------------------------------------------------------
        */

        $this->historyService->create(
            $order,
            null,
            Order::STATUS_PENDING,
            'Khách hàng tạo đơn hàng.',
            $user->id,
            OrderStatusHistory::CHANGED_BY_CUSTOMER
        );

        /*
        |--------------------------------------------------------------------------
        | 8. Tạo Order Items
        |--------------------------------------------------------------------------
        */

        $order->items()->createMany(
            $orderItems
        );

        /*
        |--------------------------------------------------------------------------
        | 9. Trừ Stock + ghi Stock History
        |--------------------------------------------------------------------------
        |
        | Variant đã được lockForUpdate ở bước trên và transaction
        | vẫn chưa kết thúc nên lock vẫn còn hiệu lực.
        |
        */

        foreach ($stockChanges as $stockChange) {
            /** @var ProductVariant $variant */
            $variant = $stockChange['variant'];

            $quantity = $stockChange['quantity'];

            $this->stockService->change(
                $variant,
                -$quantity,
                'Bán hàng theo đơn ' . $order->code,
                $user->id,
                StockHistory::TYPE_SALE
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 10. Xóa Cart Items
        |--------------------------------------------------------------------------
        */

        $cart->items()->delete();

        /*
        |--------------------------------------------------------------------------
        | 11. Return Order
        |--------------------------------------------------------------------------
        */

        return $order->load('items');
    });
}

    private function ensureVariantCanCheckout(
        ProductVariant $variant,
        int $quantity
    ): void {
        if (!$variant->is_active) {
            throw ValidationException::withMessages([
                'cart' => [
                    "Biến thể {$variant->sku} hiện không khả dụng.",
                ],
            ]);
        }

        if (!$variant->product?->is_active) {
            throw ValidationException::withMessages([
                'cart' => [
                    "Sản phẩm {$variant->product?->name} hiện không khả dụng.",
                ],
            ]);
        }

        if ($variant->stock < $quantity) {
            throw ValidationException::withMessages([
                'cart' => [
                    "Sản phẩm {$variant->product->name} chỉ còn {$variant->stock} sản phẩm.",
                ],
            ]);
        }
    }

    private function getFinalPrice(
        ProductVariant $variant
    ): float {
        $product = $variant->product;

        $price = $variant->price !== null
            ? (float) $variant->price
            : (float) $product->price;

        $salePrice = $variant->sale_price !== null
            ? (float) $variant->sale_price
            : (
                $product->sale_price !== null
                ? (float) $product->sale_price
                : null
            );

        return $salePrice !== null
            ? $salePrice
            : $price;
    }

    private function generateOrderCode(): string
    {
        do {
            $code = 'ORD'
                . now()->format('YmdHis')
                . random_int(1000, 9999);
        } while (
            Order::query()
                ->where('code', $code)
                ->exists()
        );

        return $code;
    }
}