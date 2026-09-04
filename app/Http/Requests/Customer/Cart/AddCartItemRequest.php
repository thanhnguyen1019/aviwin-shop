<?php

namespace App\Http\Requests\Customer\Cart;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_variant_id' => [
                'required',
                'integer',
                Rule::exists(
                    'product_variants',
                    'id'
                ),
            ],

            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'product_variant_id.required' => 'Biến thể sản phẩm là bắt buộc.',
            'product_variant_id.integer' => 'Biến thể sản phẩm không hợp lệ.',
            'product_variant_id.exists' => 'Biến thể sản phẩm không tồn tại.',

            'quantity.required' => 'Số lượng là bắt buộc.',
            'quantity.integer' => 'Số lượng phải là số nguyên.',
            'quantity.min' => 'Số lượng phải lớn hơn hoặc bằng 1.',
        ];
    }
}