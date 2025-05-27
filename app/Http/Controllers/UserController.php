<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\DTO\UserDto;
use App\DTO\UserCollectionDto;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**

     */
    public function index(): JsonResponse
    {
        $users = User::with('roles.permissions')->get();

        return response()->json(
            UserCollectionDto::fromCollection($users)
        );
    }

    /**
     * 
     * 
     */
    public function show(int $id): JsonResponse
    {
        $user = User::with('roles.permissions')->findOrFail($id);

        return response()->json(
            UserDto::fromModel($user)->toArray()
        );
    }
}