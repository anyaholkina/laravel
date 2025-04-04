<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServerInfoResource extends JsonResource
{
    public function toArray($request)
    {
        // Возвращаем структурированные данные о сервере
        return [
            'php_version' => phpversion(), // версия PHP
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown', // серверное ПО
            'server_address' => $_SERVER['SERVER_ADDR'] ?? 'Unknown', // IP адрес сервера
        ];
    }
}
