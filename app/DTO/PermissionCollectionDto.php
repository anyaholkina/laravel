<?php

namespace App\DTO;

use Illuminate\Support\Collection;

class PermissionCollectionDto
{
    /**
     * @param PermissionDto[] $permissions
     */
    public function __construct(
        public readonly array $permissions
    ) {}

    public static function fromModelCollection(Collection $permissions): self
    {
        return new self(
            $permissions->map(fn($p) => PermissionDto::fromModel($p))->all()
        );
    }
}