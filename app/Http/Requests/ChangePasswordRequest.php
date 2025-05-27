<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required'],
            'new_password' => [
                'required',
                'min:8',
                'regex:/[0-9]/', 
                'regex:/[^A-Za-z0-9]/', 
                'regex:/[A-Z]/', 
                'regex:/[a-z]/', 
                'different:current_password', 
            ],
            'c_new_password' => ['required', 'same:new_password']
        ];
    }

    public function messages(): array
    {
        return [
            'new_password.regex' => 'Пароль должен содержать минимум 1 цифру, 1 символ, 1 строчную и 1 заглавную букву.',
            'new_password.different' => 'Новый пароль должен отличаться от текущего.',
            'c_new_password.same' => 'Подтверждение пароля должно совпадать с новым паролем.',
        ];
    }
}