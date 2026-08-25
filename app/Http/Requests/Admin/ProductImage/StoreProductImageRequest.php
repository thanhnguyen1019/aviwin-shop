<?php

namespace App\Http\Requests\Admin\ProductImage;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'alt' => [
                'nullable',
                'string',
                'max:255',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'is_primary' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'Ảnh sản phẩm là bắt buộc.',
            'image.image' => 'File tải lên phải là hình ảnh.',
            'image.mimes' => 'Ảnh chỉ hỗ trợ jpg, jpeg, png hoặc webp.',
            'image.max' => 'Ảnh không được lớn hơn 5MB.',

            'alt.string' => 'Alt ảnh phải là chuỗi.',
            'alt.max' => 'Alt ảnh không được vượt quá 255 ký tự.',

            'sort_order.integer' => 'Thứ tự ảnh phải là số nguyên.',
            'sort_order.min' => 'Thứ tự ảnh không được nhỏ hơn 0.',

            'is_primary.boolean' => 'Trạng thái ảnh chính không hợp lệ.',
        ];
    }
}
