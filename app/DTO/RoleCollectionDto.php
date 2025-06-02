<?php

namespace App\DTO;

use Illuminate\Support\Collection;

class RoleCollectionDto
{
    /**
     * @param RoleDto[] $roles
     */
    public function __construct(
        public readonly array $roles,
    ) {}

    public static function fromModelCollection(Collection $roles): self
    {
        return new self(
            $roles->map(fn($role) => RoleDto::fromModel($role))->all()
        );
    }
}