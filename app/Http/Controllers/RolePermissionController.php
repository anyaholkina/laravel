<?php

namespace App\Http\Controllers;

use App\Http\Requests\RolePermission\CreateRolePermission;
use App\Models\RolePermission;
use Illuminate\Http\JsonResponse;

class RolePermissionController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(RolePermission::with(['role', 'permission'])->get());
    }

    public function store(CreateRolePermission $request): JsonResponse
    {
        $dto = $request->toDto();
        $rolePermission = RolePermission::create([
            'role_id' => $dto->role_id,
            'permission_id' => $dto->permission_id,
        ]);

        return response()->json($rolePermission, 201);
    }

    public function destroy($id): JsonResponse
    {
        $rolePermission = RolePermission::findOrFail($id);
        $rolePermission->delete();

        return response()->json(['message' => 'Связь роли с разрешением удалена']);
    }
}