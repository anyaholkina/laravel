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

        return DB::transaction(function () use ($log) {
            $modelClass = 'App\\Models\\' . ucfirst($log->entity);
            $model = $modelClass::find($log->entity_id);

            if (!$model) {
                return response()->json(['message' => 'Запись не найдена'], 404);
            }

            if ($log->action === 'create') {
                
                $model->delete();
            } elseif ($log->action === 'delete') {
                
                $modelClass::create($log->before);
            } elseif ($log->action === 'update') {
               
                $model->fill($log->before);
                $model->save();
            }

            return response()->json(['message' => 'Откат выполнен успешно']);
        });
    }
}