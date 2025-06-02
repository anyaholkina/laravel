<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        
        return [
            'message' => 'Login successful!',
            'token' => $this->token, 
            'user' => new UserResource($this->user), 
        ];
    }
}