<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRole\CreateUserRole;
use App\Models\UserRole;
use Illuminate\Http\JsonResponse;

class UserRoleController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(UserRole::with(['user', 'role'])->get());
    }

    public function store(CreateUserRole $request): JsonResponse
    {
        $dto = $request->toDto();
        $userRole = UserRole::create([
            'user_id' => $dto->user_id,
            'role_id' => $dto->role_id,
        ]);


        return response()->json($userRole, 201);
    }

    public function destroy($id): JsonResponse
    {
        $userRole = UserRole::findOrFail($id);
        $userRole->delete();

        return response()->json(['message' => 'Роль пользователя удалена']);
    }
}
