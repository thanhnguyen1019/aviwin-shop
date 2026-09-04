<?php

namespace App\Http\Requests\Admin\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InventoryIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'keyword' => [
                'nullable',
                'string',
                'max:255',
            ],

            'product_id' => [
                'nullable',
                'integer',
                'exists:products,id',
            ],

            'color_id' => [
                'nullable',
                'integer',
                'exists:colors,id',
            ],

            'size_id' => [
                'nullable',
                'integer',
                'exists:sizes,id',
            ],

            'is_active' => [
                'nullable',
                Rule::in([
                    '0',
                    '1',
                    0,
                    1,
                    true,
                    false,
                    'true',
                    'false',
                ]),
            ],

            'min_stock' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'max_stock' => [
                'nullable',
                'integer',
                'min:0',
                'gte:min_stock',
            ],

            'low_stock' => [
                'nullable',
                Rule::in([
                    '0',
                    '1',
                    0,
                    1,
                    true,
                    false,
                    'true',
                    'false',
                ]),
            ],

            'low_stock_threshold' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'out_of_stock' => [
                'nullable',
                Rule::in([
                    '0',
                    '1',
                    0,
                    1,
                    true,
                    false,
                    'true',
                    'false',
                ]),
            ],

            'sort' => [
                'nullable',
                Rule::in([
                    'stock_asc',
                    'stock_desc',
                    'sku_asc',
                    'sku_desc',
                    'latest',
                ]),
            ],

            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],
        ];
    }
}