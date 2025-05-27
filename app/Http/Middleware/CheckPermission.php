<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * 
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Неавторизованный доступ'], 401);
        }

        $hasPermission = $user->roles()
            ->with('permissions')
            ->get()
            ->flatMap->permissions
            ->pluck('name')
            ->contains($permission);

        if (!$hasPermission) {
            return response()->json([
                'message' => "Доступ запрещён: необходимо разрешение [{$permission}]"
            ], 403);
        }

        return $next($request);
    }
}