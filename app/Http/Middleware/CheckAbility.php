<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckAbility
{
    public function handle(Request $request, Closure $next, string $ability)
    {
        $user = $request->user();

        Log::info('CheckAbility middleware called', [
            'user_id' => $user ? $user->id : null,
            'token_abilities' => $user && $user->currentAccessToken() ? $user->currentAccessToken()->abilities : null,
            'required_ability' => $ability,
        ]);

        if ($user && $user->currentAccessToken() && in_array($ability, $user->currentAccessToken()->abilities)) {
            return $next($request); 
        }

        return response()->json(['message' => 'Доступ запрещён: нет необходимой способности'], 403);
    }
}