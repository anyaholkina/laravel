<?php
namespace App\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;
use App\DTO\PermissionDto;

class ChangePermission extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:permissions,name,' . $this->route('id'),
            'cipher' => 'required|string|unique:permissions,cipher,' .$this->route('id'),
            'description' => 'nullable|string',
        ];
    }

    public function toDto(): PermissionDto
    {
        return new PermissionDto(
            name: $this->input('name'),
            cipher: $this->input('cipher'),
            description: $this->input('description'),
            id: $this->route('id'),
        );
    }
}
