<?php

namespace App\Http\Requests\UserRole;


use Illuminate\Foundation\Http\FormRequest;

class CreateUserRole extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        return true; 
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',  
            'role_id' => 'required|exists:roles,id',  
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'user_id.required' => 'Пожалуйста, укажите пользователя.',
            'user_id.exists' => 'Выбранный пользователь не существует.',
            'role_id.required' => 'Пожалуйста, укажите роль.',
            'role_id.exists' => 'Выбранная роль не существует.',
        ];
    }

    /**
     * @return \App\DTO\UserRoleDto
     */
    public function toDto()
    {
        return new \App\DTO\UserRoleDto(
            user_id: $this->user_id,
            role_id: $this->role_id,
        );
    }
}