<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServerInfoResource extends JsonResource
{
    /**
     * Преобразует ресурс в массив.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'php_version' => phpversion(),
            'server_info' => $this->resource,
        ];
    }
}