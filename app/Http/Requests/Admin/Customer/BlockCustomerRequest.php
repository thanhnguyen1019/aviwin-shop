<?php

namespace App\Http\Requests\Admin\Customer;

use Illuminate\Foundation\Http\FormRequest;

class BlockCustomerRequest extends FormRequest
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
            'reason.required' => 'Lý do khóa tài khoản là bắt buộc.',
            'reason.string' => 'Lý do khóa tài khoản không hợp lệ.',
            'reason.max' => 'Lý do khóa tài khoản không được vượt quá 1000 ký tự.',
        ];
    }
}