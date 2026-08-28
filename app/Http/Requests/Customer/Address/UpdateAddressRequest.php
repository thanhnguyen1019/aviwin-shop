<?php

namespace App\Http\Requests\Customer\Address;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'phone' => [
                'sometimes',
                'required',
                'string',
                'max:30',
            ],

            'province_name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'district_name' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'ward_name' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'address_line' => [
                'sometimes',
                'required',
                'string',
                'max:500',
            ],

            'label' => [
                'sometimes',
                'nullable',
                'string',
                'max:100',
            ],

            'is_default' => [
                'sometimes',
                'boolean',
            ],
        ];
    }
}