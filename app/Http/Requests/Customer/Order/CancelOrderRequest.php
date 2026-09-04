<?php

namespace App\Http\Requests\Customer\Order;

use Illuminate\Foundation\Http\FormRequest;

class CancelOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reason' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'reason.string' => 'Lý do hủy đơn không hợp lệ.',
            'reason.max' => 'Lý do hủy đơn không được vượt quá 1000 ký tự.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('reason')) {
            $this->merge([
                'reason' => trim(
                    $this->input('reason')
                ),
            ]);
        }
    }
}