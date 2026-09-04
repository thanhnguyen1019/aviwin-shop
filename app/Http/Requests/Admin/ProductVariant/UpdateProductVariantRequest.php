<?php

namespace App\Http\Requests\Admin\ProductVariant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        foreach (
            [
                'color_id',
                'size_id',
                'price',
                'sale_price',
            ] as $field
        ) {
            if (
                $this->has($field)
                && $this->input($field) === ''
            ) {
                $this->merge([
                    $field => null,
                ]);
            }
        }
    }

    public function rules(): array
    {
        $variant = $this->route('variant');

        return [
            'color_id' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists(
                    'colors',
                    'id'
                ),
            ],

            'size_id' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists(
                    'sizes',
                    'id'
                ),
            ],

            'sku' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                Rule::unique(
                    'product_variants',
                    'sku'
                )->ignore(
                    $variant?->id
                ),
            ],

            'price' => [
                'sometimes',
                'nullable',
                'numeric',
                'min:0',
            ],

            'sale_price' => [
                'sometimes',
                'nullable',
                'numeric',
                'min:0',
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'color_id.integer' =>
                'Màu không hợp lệ.',

            'color_id.exists' =>
                'Màu không tồn tại.',

            'size_id.integer' =>
                'Size không hợp lệ.',

            'size_id.exists' =>
                'Size không tồn tại.',

            'sku.required' =>
                'SKU là bắt buộc.',

            'sku.string' =>
                'SKU phải là chuỗi.',

            'sku.max' =>
                'SKU không được vượt quá 100 ký tự.',

            'sku.unique' =>
                'SKU đã tồn tại.',

            'price.numeric' =>
                'Giá biến thể phải là số.',

            'price.min' =>
                'Giá biến thể không được nhỏ hơn 0.',

            'sale_price.numeric' =>
                'Giá khuyến mãi phải là số.',

            'sale_price.min' =>
                'Giá khuyến mãi không được nhỏ hơn 0.',

            'is_active.boolean' =>
                'Trạng thái không hợp lệ.',
        ];
    }
}