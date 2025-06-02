<?php

namespace App\Traits;

use App\Models\ChangeLog;
use Illuminate\Support\Facades\Auth;

trait LogsChanges
{
    public static function bootLogsChanges()
    {
        static::created(function ($model) {
            $model->logChange('create', null, $model->getAttributes());
        });

        static::updating(function ($model) {
            $before = $model->getOriginal();
            $after = $model->getAttributes();
            $model->logChange('update', $before, $after);
        });

        static::deleted(function ($model) {
            $model->logChange('delete', $model->getAttributes(), null);
        });
    }

    protected function logChange(string $action, $before, $after)
    {
        ChangeLog::create([
            'entity' => strtolower(class_basename($this)),
            'entity_id' => $this->getKey(),
            'before' => $before ? json_encode($before) : null,
            'after' => $after ? json_encode($after) : null,
            'action' => $action,
            'user_id' => Auth::id(),
        ]);
    }
}