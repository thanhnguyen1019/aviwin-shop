<?php

namespace App\Http\Requests\Admin\Order;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePaymentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_status' => [
                'required',
                'string',
                Rule::in([
                    Order::PAYMENT_UNPAID,
                    Order::PAYMENT_PAID,
                    Order::PAYMENT_REFUNDED,
                ]),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_status.required' => 'Trạng thái thanh toán là bắt buộc.',
            'payment_status.in' => 'Trạng thái thanh toán không hợp lệ.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('payment_status')) {
            $this->merge([
                'payment_status' => strtolower(
                    trim(
                        $this->input(
                            'payment_status'
                        )
                    )
                ),
            ]);
        }
    }
}