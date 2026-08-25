<?php

namespace App\Http\Requests\Admin\Color;

use Illuminate\Foundation\Http\FormRequest;

class UpdateColorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'code' => [
                'sometimes',
                'nullable',
                'string',
                'max:50',
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],

            'sort_order' => [
                'sometimes',
                'integer',
                'min:0',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên màu là bắt buộc.',
            'name.string' => 'Tên màu phải là chuỗi.',
            'name.max' => 'Tên màu không được vượt quá 255 ký tự.',

            'code.string' => 'Mã màu phải là chuỗi.',
            'code.max' => 'Mã màu không được vượt quá 50 ký tự.',

            'is_active.boolean' => 'Trạng thái không hợp lệ.',

            'sort_order.integer' => 'Thứ tự hiển thị phải là số nguyên.',
            'sort_order.min' => 'Thứ tự hiển thị không được nhỏ hơn 0.',
        ];
    }
}