<?php

namespace App\Http\Requests\Admin\Brand;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (
            !$this->filled('slug')
            && $this->filled('name')
        ) {
            $this->merge([
                'slug' => Str::slug(
                    $this->input('name')
                ),
            ]);
        }
    }

    public function rules(): array
    {
        $brand = $this->route('brand');

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:255',

                Rule::unique('brands', 'slug')
                    ->ignore($brand?->id),
            ],

            'logo' => [
                'sometimes',
                'nullable',
                'string',
                'max:2048',
            ],

            'description' => [
                'sometimes',
                'nullable',
                'string',
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
            'name.required' => 'Tên thương hiệu là bắt buộc.',
            'name.string' => 'Tên thương hiệu phải là chuỗi.',
            'name.max' => 'Tên thương hiệu không được vượt quá 255 ký tự.',

            'slug.required' => 'Slug là bắt buộc.',
            'slug.string' => 'Slug phải là chuỗi.',
            'slug.max' => 'Slug không được vượt quá 255 ký tự.',
            'slug.unique' => 'Slug thương hiệu đã tồn tại.',

            'logo.string' => 'Logo không hợp lệ.',
            'logo.max' => 'Đường dẫn logo quá dài.',

            'description.string' => 'Mô tả phải là chuỗi.',

            'is_active.boolean' => 'Trạng thái không hợp lệ.',

            'sort_order.integer' => 'Thứ tự hiển thị phải là số nguyên.',
            'sort_order.min' => 'Thứ tự hiển thị không được nhỏ hơn 0.',
        ];
    }
}