<?php

namespace App\DTO;

class ChangeLogCollectionDTO
{
    /** @var ChangeLogDTO[] */
    public array $logs;

    public function __construct(array $logs)
    {
        $this->logs = array_map(fn($log) => new ChangeLogDTO($log), $logs);
    }
}
