<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DatabaseInfoResource extends JsonResource
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
            'database' => $this->resource['database'],
        ];
    }
}