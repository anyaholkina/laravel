<?php
namespace App\DTO;

use Illuminate\Database\Eloquent\Collection;

class UserCollectionDto
{
    public function __construct(
        public readonly array $users,
    ) {}

    public static function fromCollection(Collection $collection): self
    {
        return new self(
            users: $collection->map(fn($user) => UserDto::fromModel($user))->toArray(),
        );
    }
}