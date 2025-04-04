<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientInfoResource extends JsonResource
{
    public function toArray($request)
    {
        // Возвращаем данные о клиенте
        return [
            'ip' => $this->resource['ip'], // IP-адрес клиента
            'user_agent' => $this->resource['user_agent'], // User-Agent клиента
        ];
    }
}
