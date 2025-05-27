<?php

namespace App\DTO;

class RolePermissionDto
{
    public function __construct(
        public readonly int $role_id,
        public readonly int $permission_id,
    ) {}

    public static function fromModel(\App\Models\RolePermission $model): self
    {
        return new self(
            role_id: $model->role_id,
            permission_id: $model->permission_id,
        );
    }
}