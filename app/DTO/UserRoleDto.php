<?php

namespace App\DTO;

class UserRoleDto
{
    public function __construct(
        public readonly int $user_id,
        public readonly int $role_id,
    ) {}

    public static function fromModel(\App\Models\UserRole $model): self
    {
        return new self(
            user_id: $model->user_id,
            role_id: $model->role_id,
        );
    }
}
