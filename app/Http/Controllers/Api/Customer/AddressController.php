<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Address\StoreAddressRequest;
use App\Http\Requests\Customer\Address\UpdateAddressRequest;
use App\Http\Resources\Customer\Address\AddressResource;
use App\Models\Address;
use App\Services\Customer\Address\AddressService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function __construct(
        protected AddressService $addressService
    ) {
    }

    public function index(
        Request $request
    ): JsonResponse {
        $addresses = $this->addressService
            ->getByUser(
                $request->user()
            );

        return ApiResponse::success(
            AddressResource::collection($addresses),
            'Lấy danh sách địa chỉ thành công'
        );
    }

    public function store(
        StoreAddressRequest $request
    ): JsonResponse {
        $address = $this->addressService
            ->create(
                $request->user(),
                $request->validated()
            );

        return ApiResponse::success(
            new AddressResource($address),
            'Tạo địa chỉ thành công',
            201
        );
    }

    public function update(
        UpdateAddressRequest $request,
        Address $address
    ): JsonResponse {
        $address = $this->addressService
            ->update(
                $request->user(),
                $address,
                $request->validated()
            );

        return ApiResponse::success(
            new AddressResource($address),
            'Cập nhật địa chỉ thành công'
        );
    }

    public function destroy(
        Request $request,
        Address $address
    ): JsonResponse {
        $this->addressService
            ->delete(
                $request->user(),
                $address
            );

        return ApiResponse::success(
            null,
            'Xóa địa chỉ thành công'
        );
    }
}