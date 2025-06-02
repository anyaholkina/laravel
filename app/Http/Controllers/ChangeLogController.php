<?php

namespace App\Http\Controllers;

use App\Models\ChangeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChangeLogController extends Controller
{

    public function getUserLogs(Request $request)
    {
        return $this->getLogsByEntity('User');
    }

    public function getRoleLogs(Request $request)
    {
        return $this->getLogsByEntity('Role');
    }

    public function getPermissionLogs(Request $request)
    {
        return $this->getLogsByEntity('Permission');
    }

    protected function getLogsByEntity(string $entity)
    {
        $logs = ChangeLog::where('entity', strtolower($entity))
            ->orderByDesc('created_at')
            ->paginate(20);

        $logs->getCollection()->transform(function ($log) {
            if ($log->action === 'update') {
                $log->before = $this->onlyChangedFields($log->before, $log->after);
                $log->after = $this->onlyChangedFields($log->after, $log->before);
            }
            return $log;
        });

        return response()->json($logs);
    }

    
public function getRoleLogsByEntity($entity_id)
{
    $logs = \DB::table('change_logs')
        ->where('entity', 'role')
        ->where('entity_id', $entity_id)
        ->orderBy('id', 'asc')
        ->get(['id', 'entity', 'entity_id', 'before', 'after', 'action', 'user_id', 'created_at', 'updated_at']);

    return response()->json($logs);
}



    protected function onlyChangedFields(?array $primary, ?array $compare): ?array
    {
        if (!$primary || !$compare) {
            return $primary;
        }

        return array_filter($primary, function ($key) use ($primary, $compare) {
            return isset($compare[$key]) && $primary[$key] !== $compare[$key];
        }, ARRAY_FILTER_USE_KEY);
    }

  public function rollbackChange(Request $request, $logId)
{
    $log = ChangeLog::findOrFail($logId);
    $userId = auth()->id();

    return DB::transaction(function () use ($log, $userId) {
        $modelClass = 'App\\Models\\' . ucfirst($log->entity);
        
        $model = $modelClass::withTrashed()->find($log->entity_id);

        switch ($log->action) {
            case 'created':
                if (!$model) {
                    return response()->json(['message' => 'Запись не найдена для удаления'], 404);
                }

                $model->forceDelete();

                ChangeLog::create([
                    'entity' => $log->entity,
                    'entity_id' => $log->entity_id,
                    'action' => 'rollback_create',
                    'before' => $model->toArray(),
                    'after' => null,
                    'user_id' => $userId,
                ]);
                break;

            case 'deleted':
                if ($model) {
                    $model->restore();
                    $model->fill($log->before);
                    $model->save();

                    ChangeLog::create([
                        'entity' => $log->entity,
                        'entity_id' => $model->id,
                        'action' => 'rollback_restore',
                        'before' => null,
                        'after' => $log->before,
                        'user_id' => $userId,
                    ]);
                } else {
                    $restored = $modelClass::create($log->before);

                    ChangeLog::create([
                        'entity' => $log->entity,
                        'entity_id' => $restored->id,
                        'action' => 'rollback_delete',
                        'before' => null,
                        'after' => $log->before,
                        'user_id' => $userId,
                    ]);
                }
                break;

            case 'restored':
                if (!$model) {
                    return response()->json(['message' => 'Запись не найдена для отката'], 404);
                }

                $model->delete();

                ChangeLog::create([
                    'entity' => $log->entity,
                    'entity_id' => $model->id,
                    'action' => 'rollback_restore_delete',
                    'before' => $log->after,
                    'after' => null,
                    'user_id' => $userId,
                ]);
                break;

            case 'updated':
                if (!$model) {
                    return response()->json(['message' => 'Запись не найдена для отката'], 404);
                }

                $old = $model->toArray();
                $model->fill($log->before);
                $model->save();

                ChangeLog::create([
                    'entity' => $log->entity,
                    'entity_id' => $model->id,
                    'action' => 'rollback_update',
                    'before' => $old,
                    'after' => $log->before,
                    'user_id' => $userId,
                ]);
                break;

            case 'force_deleted':
                
                if ($model) {
                    return response()->json(['message' => 'Запись уже существует, откат невозможен'], 400);
                }

               
                $restored = $modelClass::create($log->before);

                ChangeLog::create([
                    'entity' => $log->entity,
                    'entity_id' => $restored->id,
                    'action' => 'rollback_force_delete',
                    'before' => null,
                    'after' => $log->before,
                    'user_id' => $userId,
                ]);
                break;

            default:return response()->json(['message' => 'Неизвестное действие для отката'], 400);
        }

        return response()->json(['message' => 'Откат выполнен успешно']);
    });
}
}