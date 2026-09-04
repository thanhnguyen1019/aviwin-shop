<?php

namespace App\Http\Requests\Admin\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class AdjustStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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

    public function rules(): array
    {
        return [
            'quantity_change' => [
                'required',
                'integer',
                'not_in:0',
            ],

            'reason' => [
                'required',
                'string',
                'max:1000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'quantity_change.required' =>
                'Số lượng thay đổi là bắt buộc.',

            'quantity_change.integer' =>
                'Số lượng thay đổi phải là số nguyên.',

            'quantity_change.not_in' =>
                'Số lượng thay đổi phải khác 0.',

            'reason.required' =>
                'Lý do điều chỉnh tồn kho là bắt buộc.',

            'reason.max' =>
                'Lý do không được vượt quá 1000 ký tự.',
        ];
    }
}