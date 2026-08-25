<?php

namespace App\Http\Requests\Admin\ProductVariant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('color_id') && $this->input('color_id') === '') {
            $this->merge([
                'color_id' => null,
            ]);
        }

        if ($this->has('size_id') && $this->input('size_id') === '') {
            $this->merge([
                'size_id' => null,
            ]);
        }

        if ($this->has('price') && $this->input('price') === '') {
            $this->merge([
                'price' => null,
            ]);
        }

        if ($this->has('sale_price') && $this->input('sale_price') === '') {
            $this->merge([
                'sale_price' => null,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'color_id' => [
                'nullable',
                'integer',
                Rule::exists('colors', 'id'),
            ],

            'size_id' => [
                'nullable',
                'integer',
                Rule::exists('sizes', 'id'),
            ],

            'sku' => [
                'required',
                'string',
                'max:100',
                Rule::unique('product_variants', 'sku'),
            ],

            'price' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'sale_price' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'stock' => [
                'required',
                'integer',
                'min:0',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'color_id.integer' => 'Màu không hợp lệ.',
            'color_id.exists' => 'Màu không tồn tại.',

            'size_id.integer' => 'Size không hợp lệ.',
            'size_id.exists' => 'Size không tồn tại.',

            'sku.required' => 'SKU là bắt buộc.',
            'sku.string' => 'SKU phải là chuỗi.',
            'sku.max' => 'SKU không được vượt quá 100 ký tự.',
            'sku.unique' => 'SKU đã tồn tại.',

            'price.numeric' => 'Giá biến thể phải là số.',
            'price.min' => 'Giá biến thể không được nhỏ hơn 0.',

            'sale_price.numeric' => 'Giá khuyến mãi phải là số.',
            'sale_price.min' => 'Giá khuyến mãi không được nhỏ hơn 0.',

            'stock.required' => 'Tồn kho là bắt buộc.',
            'stock.integer' => 'Tồn kho phải là số nguyên.',
            'stock.min' => 'Tồn kho không được nhỏ hơn 0.',

            'is_active.boolean' => 'Trạng thái không hợp lệ.',
        ];
    }
}