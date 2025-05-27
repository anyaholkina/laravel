<?php

namespace App\DTO;

class ChangeLogDTO
{
    public string $entity;
    public int $entity_id;
    public ?array $before;
    public ?array $after;
    public string $action;
    public ?int $user_id;

    public function __construct(array $data)
    {
        $this->entity = $data['entity'];
        $this->entity_id = $data['entity_id'];
        $this->before = $data['before'];
        $this->after = $data['after'];
        $this->action = $data['action'];
        $this->user_id = $data['user_id'] ?? null;
    }
}
