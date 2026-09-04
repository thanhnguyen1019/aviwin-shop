<?php

namespace App\Http\Requests\Customer\Checkout;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'address_id' => [
                'required',
                'integer',
                Rule::exists('addresses', 'id'),
            ],

            'payment_method' => [
                'required',
                'string',
                Rule::in([
                    'cod',
                ]),
            ],

            'note' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'address_id.required' => 'Địa chỉ nhận hàng là bắt buộc.',
            'address_id.exists' => 'Địa chỉ nhận hàng không tồn tại.',

            'payment_method.required' => 'Phương thức thanh toán là bắt buộc.',
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ.',

            'note.max' => 'Ghi chú không được vượt quá 2000 ký tự.',
        ];
    }
}