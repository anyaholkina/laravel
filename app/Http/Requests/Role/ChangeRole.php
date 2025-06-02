<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use App\DTO\RoleDto;

class ChangeRole extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check(); 
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:roles,name,' . $this->route('id'), 
            'cipher' => 'required|string|unique:roles,cipher,' . $this->route('id'), 
            'description' => 'nullable|string',
        ];
    }

    public function toDto(): RoleDto
    {
        return new RoleDto(
            name: $this->input('name'),
            cipher: $this->input('cipher'),
            description: $this->input('description'),
            id: $this->route('id') 
        );
    }
}