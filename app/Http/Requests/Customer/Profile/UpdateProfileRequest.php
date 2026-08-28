<?php

namespace App\Http\Requests\Customer\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('name')) {
            $this->merge([
                'name' => trim($this->input('name')),
            ]);
        }

        if ($this->filled('email')) {
            $this->merge([
                'email' => strtolower(
                    trim($this->input('email'))
                ),
            ]);
        }
    }

    public function rules(): array
    {
        $user = $this->user();

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',

                Rule::unique('users', 'email')
                    ->ignore($user?->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Họ tên là bắt buộc.',
            'name.string' => 'Họ tên phải là chuỗi.',
            'name.max' => 'Họ tên không được vượt quá 255 ký tự.',

            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã được sử dụng.',
        ];
    }
}