<?php

namespace App\Http\Requests\Customer\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
            ],

            'password' => [
                'required',
                'string',
            ],

            'device_name' => [
                'nullable',
                'string',
                'max:100',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không đúng định dạng.',

            'password.required' => 'Mật khẩu là bắt buộc.',

            'device_name.string' => 'Tên thiết bị không hợp lệ.',
            'device_name.max' => 'Tên thiết bị không được vượt quá 100 ký tự.',
        ];
    }
    protected function prepareForValidation(): void
{
    if ($this->filled('email')) {
        $this->merge([
            'email' => strtolower(
                trim($this->input('email'))
            ),
        ]);
    }
}
}