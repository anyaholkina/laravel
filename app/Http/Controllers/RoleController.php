<?php

namespace App\Http\Controllers;

use App\Http\Requests\Role\CreateRole;
use App\Http\Requests\Role\ChangeRole;
use App\Models\Role;
use App\Models\ChangeLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\DTO\RoleDto;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        $roles = Role::all();
        $rolesDto = $roles->map(fn($role) => RoleDto::fromModel($role)->toArray());
        return response()->json($rolesDto);
    }

    public function show(int $id): JsonResponse
    {
        $role = Role::withTrashed()->findOrFail($id);
        $roleDto = RoleDto::fromModel($role);
        return response()->json($roleDto->toArray());
    }

    public function store(CreateRole $request): JsonResponse
    {
        $dto = $request->toDto();

        $role = Role::create([
            'name' => $dto->name,
            'cipher' => $dto->cipher,
            'description' => $dto->description,
        ]);

        // Логирование создания
        ChangeLog::create([
            'entity' => 'Role',
            'entity_id' => $role->id,
            'before' => null,
            'after' => $role->toArray(),
            'action' => 'created',
            'user_id' => Auth::id(),
        ]);

        return response()->json(RoleDto::fromModel($role)->toArray(), 201);
    }

    public function update(ChangeRole $request, int $id): JsonResponse
    {
        $dto = $request->toDto();
        $role = Role::findOrFail($id);

        $before = $role->toArray();

        $role->update([
            'name' => $dto->name,
            'cipher' => $dto->cipher,
            'description' => $dto->description,
        ]);

        $after = $role->fresh()->toArray();

        if ($before != $after) {
            ChangeLog::create([
                'entity' => 'Role',
                'entity_id' => $role->id,
                'before' => $before,
                'after' => $after,
                'action' => 'updated',
                'user_id' => Auth::id(),
            ]);
        }

        return response()->json(RoleDto::fromModel($role)->toArray());
    }

    public function destroy(int $id): JsonResponse
    {
        $role = Role::withTrashed()->findOrFail($id);
        $before = $role->toArray();

        $role->forceDelete();

        ChangeLog::create([
            'entity' => 'Role',
            'entity_id' => $role->id,
            'before' => $before,
            'after' => null,
            'action' => 'force_deleted',
            'user_id' => Auth::id(),
        ]);

        return response()->json(['message' => 'Роль удалена навсегда.']);
    }

    public function softDelete(int $id): JsonResponse
    {
        $role = Role::findOrFail($id);
        $before = $role->toArray();

        $role->delete();

        ChangeLog::create([
            'entity' => 'Role',
            'entity_id' => $role->id,
            'before' => $before,
            'after' => null,
            'action' => 'soft_deleted',
            'user_id' => Auth::id(),
        ]);

        return response()->json(['message' => 'Роль мягко удалена.']);
    }

    public function restore(int $id): JsonResponse
    {
        $role = Role::onlyTrashed()->findOrFail($id);
        $before = $role->toArray();

        $role->restore();

        ChangeLog::create([
            'entity' => 'Role',
            'entity_id' => $role->id,
            'before' => $before,
            'after' => $role->fresh()->toArray(),
            'action' => 'restored',
            'user_id' => Auth::id(),
        ]);

        return response()->json(['message' => 'Роль восстановлена.']);
    }
}