<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Cart\AddCartItemRequest;
use App\Http\Requests\Customer\Cart\UpdateCartItemRequest;
use App\Http\Resources\Customer\Cart\CartResource;
use App\Models\CartItem;
use App\Services\Customer\Cart\CartService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {
    }

    public function show(
        Request $request
    ): JsonResponse {
        $cart = $this->cartService
            ->getCart(
                $request->user()
            );

        return ApiResponse::success(
            new CartResource($cart),
            'Lấy giỏ hàng thành công'
        );
    }

    public function addItem(
        AddCartItemRequest $request
    ): JsonResponse {
        $cart = $this->cartService
            ->addItem(
                $request->user(),
                (int) $request->validated(
                    'product_variant_id'
                ),
                (int) $request->validated(
                    'quantity'
                )
            );

        return ApiResponse::success(
            new CartResource($cart),
            'Thêm sản phẩm vào giỏ hàng thành công',
            201
        );
    }

    public function updateItem(
        UpdateCartItemRequest $request,
        CartItem $item
    ): JsonResponse {
        $cart = $this->cartService
            ->updateItem(
                $request->user(),
                $item,
                (int) $request->validated(
                    'quantity'
                )
            );

        return ApiResponse::success(
            new CartResource($cart),
            'Cập nhật giỏ hàng thành công'
        );
    }

    public function deleteItem(
        Request $request,
        CartItem $item
    ): JsonResponse {
        $cart = $this->cartService
            ->deleteItem(
                $request->user(),
                $item
            );

        return ApiResponse::success(
            new CartResource($cart),
            'Xóa sản phẩm khỏi giỏ hàng thành công'
        );
    }

    public function clear(
        Request $request
    ): JsonResponse {
        $cart = $this->cartService
            ->clear(
                $request->user()
            );

        return ApiResponse::success(
            new CartResource($cart),
            'Xóa toàn bộ giỏ hàng thành công'
        );
    }
}