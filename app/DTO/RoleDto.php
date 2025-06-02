<?php

namespace App\DTO;

use App\Models\Role;

class RoleDto
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly ?string $description,
        public readonly string $cipher,
        public readonly ?array $permissions = null,
    ) {}

    public static function fromModel(Role $role): self
    {
        return new self(
            id: $role->id,
            name: $role->name,
            description: $role->description,
            cipher: $role->cipher,
            permissions: $role->relationLoaded('permissions')
                ? $role->permissions->map(fn($p) => PermissionDto::fromModel($p)->toArray())->toArray()
                : [],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'cipher' => $this->cipher,
        ];
    }
}