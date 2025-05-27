<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * 
     *
     * @return bool
     */
    public function authorize()
    {
        return true;    }

    /**
     *      *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => 'required|string|max:255', 
            'password' => 'required|string|min:8|regex:/[A-Z]/|regex:/[a-z]/|regex:/[0-9]/', 
        ];
    }

    /**
     *
     * @return array
     */
    public function messages()
    {
        return [
            'username.required' => 'Поле "Имя пользователя" обязательно для заполнения.',
            'username.string' => 'Имя пользователя должно быть строкой.',
            'username.alpha' => 'Имя пользователя может содержать только латинские буквы.',
            'username.min' => 'Имя пользователя должно содержать минимум 7 символов.',
            'username.regex' => 'Имя пользователя должно начинаться с большой буквы.',
            'password.required' => 'Поле "Пароль" обязательно для заполнения.',
            'password.string' => 'Пароль должен быть строкой.',
            'password.min' => 'Пароль должен содержать минимум 8 символов.',
            'password.regex' => 'Пароль должен содержать хотя бы одну цифру, одну строчную и одну заглавную букву.',
        ];
    }
}
