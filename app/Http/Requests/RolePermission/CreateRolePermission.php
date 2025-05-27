<?php

namespace App\Http\Requests\RolePermission;

use Illuminate\Foundation\Http\FormRequest;
use App\DTO\RolePermissionDto;

class CreateRolePermission extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'role_id' => 'required|exists:roles,id',
            'permission_id' => 'required|exists:permissions,id',
        ];
    }

    public function toDto(): RolePermissionDto
    {
        return new RolePermissionDto(
            role_id: $this->input('role_id'),
            permission_id: $this->input('permission_id'),
        );
    }
}
