<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Inventory\AdjustStockRequest;
use App\Http\Requests\Admin\Inventory\InventoryIndexRequest;
use App\Http\Resources\Admin\Inventory\InventoryResource;
use App\Http\Resources\Admin\Inventory\StockHistoryResource;
use App\Models\ProductVariant;
use App\Services\Admin\Inventory\InventoryService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {
    }

    public function index(
        InventoryIndexRequest $request
    ): JsonResponse {
        $variants = $this->inventoryService
            ->paginate(
                $request->validated()
            );

        return ApiResponse::paginated(
            InventoryResource::collection($variants),
            $variants,
            'Lấy danh sách tồn kho thành công'
        );
    }

    public function histories(
        Request $request,
        ProductVariant $variant
    ): JsonResponse {
        $perPage = max(
            1,
            min(
                (int) $request->input(
                    'per_page',
                    20
                ),
                100
            )
        );

        $histories = $this->inventoryService
            ->histories(
                $variant,
                $perPage
            );

        return ApiResponse::paginated(
            StockHistoryResource::collection(
                $histories
            ),
            $histories,
            'Lấy lịch sử tồn kho thành công'
        );
    }

    public function adjust(
        AdjustStockRequest $request,
        ProductVariant $variant
    ): JsonResponse {
        $data = $request->validated();

        $variant = $this->inventoryService
            ->adjustStock(
                $variant,
                (int) $data['quantity_change'],
                $data['reason'],
                $request->user()->id
            );

        return ApiResponse::success(
            new InventoryResource($variant),
            'Điều chỉnh tồn kho thành công'
        );
    }
}