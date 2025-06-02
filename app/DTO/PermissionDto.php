<?php

namespace App\DTO;

use App\Models\Permission;

class PermissionDto
{
    public function __construct(
        public readonly string $name,
        public readonly string $cipher,
        public readonly ?string $description = null,
        public readonly ?int $id = null,
    ) {}

    public static function fromModel(Permission $permission): self
    {
        return new self(
            id: $permission->id,
            name: $permission->name,
            cipher: $permission->cipher,
            description: $permission->description,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'cipher' => $this->cipher,
            'description' => $this->description,
        ];
    }
}
