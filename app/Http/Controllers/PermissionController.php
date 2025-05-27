<?php

namespace App\Http\Controllers;

use App\Http\Requests\Permission\CreatePermission;
use App\Http\Requests\Permission\ChangePermission;
use App\Models\Permission;
use App\DTO\PermissionDto;
use App\DTO\PermissionCollectionDto;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    public function index(): JsonResponse
    {
        $permissions = Permission::all();
        return response()->json(
            PermissionCollectionDto::fromModelCollection($permissions)
        );
    }

    public function show(int $id): JsonResponse
    {
        $permission = Permission::withTrashed()->findOrFail($id);
        return response()->json(PermissionDto::fromModel($permission));
    }

    public function store(CreatePermission $request): JsonResponse
    {
        $dto = $request->toDto();

        $permission = Permission::create([
            'name' => $dto->name,
            'cipher' => $dto->cipher,
            'description' => $dto->description,
        ]);

        return response()->json(PermissionDto::fromModel($permission), 201);
    }

    public function update(ChangePermission $request, int $id): JsonResponse
    {
        $dto = $request->toDto();
        $permission = Permission::findOrFail($id);

        $permission->update([
            'name' => $dto->name,
            'cipher' => $dto->cipher,
            'description' => $dto->description,
        ]);

        return response()->json(PermissionDto::fromModel($permission));
    }

    public function destroy(int $id): JsonResponse
    {
        $permission = Permission::withTrashed()->findOrFail($id);
        $permission->forceDelete();

        return response()->json(['message' => 'Разрешение удалено навсегда.']);
    }

    public function softDelete(int $id): JsonResponse
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return response()->json(['message' => 'Разрешение мягко удалено.']);
    }

    public function restore(int $id): JsonResponse
    {
        $permission = Permission::onlyTrashed()->findOrFail($id);
        $permission->restore();

        return response()->json(['message' => 'Разрешение восстановлено.']);
    }
}