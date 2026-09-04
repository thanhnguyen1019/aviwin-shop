<?php

namespace App\Http\Requests\Admin\Dashboard;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class DashboardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from_date' => [
                'nullable',
                'date_format:Y-m-d',
            ],

            'to_date' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:from_date',
            ],

            'recent_limit' => [
                'nullable',
                'integer',
                'min:1',
                'max:50',
            ],

            'top_product_limit' => [
                'nullable',
                'integer',
                'min:1',
                'max:50',
            ],
        ];
    }

    public function after(): array
    {
        return [
            function (
                Validator $validator
            ) {
                $fromDate = $this->input(
                    'from_date'
                );

                $toDate = $this->input(
                    'to_date'
                );

                if (
                    !$fromDate
                    || !$toDate
                ) {
                    return;
                }

                $days = Carbon::parse(
                    $fromDate
                )->diffInDays(
                    Carbon::parse(
                        $toDate
                    )
                );

                if ($days > 365) {
                    $validator->errors()->add(
                        'to_date',
                        'Khoảng thời gian dashboard không được vượt quá 366 ngày.'
                    );
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'from_date.date_format' =>
                'from_date phải có định dạng Y-m-d.',

            'to_date.date_format' =>
                'to_date phải có định dạng Y-m-d.',

            'to_date.after_or_equal' =>
                'to_date phải lớn hơn hoặc bằng from_date.',

            'recent_limit.integer' =>
                'recent_limit phải là số nguyên.',

            'recent_limit.min' =>
                'recent_limit phải lớn hơn hoặc bằng 1.',

            'recent_limit.max' =>
                'recent_limit không được vượt quá 50.',

            'top_product_limit.integer' =>
                'top_product_limit phải là số nguyên.',

            'top_product_limit.min' =>
                'top_product_limit phải lớn hơn hoặc bằng 1.',

            'top_product_limit.max' =>
                'top_product_limit không được vượt quá 50.',
        ];
    }
}