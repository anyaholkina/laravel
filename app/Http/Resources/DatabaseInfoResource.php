<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DatabaseInfoResource extends JsonResource
{
    public function toArray($request)
    {
        // Возвращаем название базы данных из конфигурации
        return [
            'database' => env('DB_DATABASE', 'default_db'), // Имя базы данных
        ];
    }
}