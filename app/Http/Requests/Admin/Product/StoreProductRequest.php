<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
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

        if ($this->has('brand_id') && $this->input('brand_id') === '') {
            $this->merge([
                'brand_id' => null,
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
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id'),
            ],

            'brand_id' => [
                'nullable',
                'integer',
                Rule::exists('brands', 'id'),
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'slug'),
            ],

            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('products', 'code'),
            ],

            'short_description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'sale_price' => [
                'nullable',
                'numeric',
                'min:0',
                'lte:price',
            ],

            'thumbnail' => [
                'nullable',
                'string',
                'max:2048',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

            'is_featured' => [
                'nullable',
                'boolean',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Danh mục là bắt buộc.',
            'category_id.integer' => 'Danh mục không hợp lệ.',
            'category_id.exists' => 'Danh mục không tồn tại.',

            'brand_id.integer' => 'Thương hiệu không hợp lệ.',
            'brand_id.exists' => 'Thương hiệu không tồn tại.',

            'name.required' => 'Tên sản phẩm là bắt buộc.',
            'name.string' => 'Tên sản phẩm phải là chuỗi.',
            'name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự.',

            'slug.required' => 'Slug là bắt buộc.',
            'slug.unique' => 'Slug sản phẩm đã tồn tại.',

            'code.required' => 'Mã sản phẩm là bắt buộc.',
            'code.unique' => 'Mã sản phẩm đã tồn tại.',

            'price.required' => 'Giá sản phẩm là bắt buộc.',
            'price.numeric' => 'Giá sản phẩm phải là số.',
            'price.min' => 'Giá sản phẩm không được nhỏ hơn 0.',

            'sale_price.numeric' => 'Giá khuyến mãi phải là số.',
            'sale_price.min' => 'Giá khuyến mãi không được nhỏ hơn 0.',
            'sale_price.lte' => 'Giá khuyến mãi không được lớn hơn giá bán.',

            'is_active.boolean' => 'Trạng thái sản phẩm không hợp lệ.',
            'is_featured.boolean' => 'Trạng thái nổi bật không hợp lệ.',

            'sort_order.integer' => 'Thứ tự hiển thị phải là số nguyên.',
            'sort_order.min' => 'Thứ tự hiển thị không được nhỏ hơn 0.',
        ];
    }
}