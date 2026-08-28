<?php

namespace App\Http\Requests\Customer\Address;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => [
                'required',
                'string',
                'max:255',
            ],

            'phone' => [
                'required',
                'string',
                'max:30',
            ],

            'province_name' => [
                'required',
                'string',
                'max:255',
            ],

            'district_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'ward_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'address_line' => [
                'required',
                'string',
                'max:500',
            ],

            'label' => [
                'nullable',
                'string',
                'max:100',
            ],

            'is_default' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Tên người nhận là bắt buộc.',

            'phone.required' => 'Số điện thoại là bắt buộc.',

            'province_name.required' => 'Tỉnh/thành phố là bắt buộc.',

            'address_line.required' => 'Địa chỉ chi tiết là bắt buộc.',

            'is_default.boolean' => 'Trạng thái mặc định không hợp lệ.',
        ];
    }
}