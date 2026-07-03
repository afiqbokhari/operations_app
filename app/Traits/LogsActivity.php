<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    public static function bootLogsActivity()
    {
        static::created(function ($model) {
            $model->logActivity('created');
        });

        static::updated(function ($model) {
            $model->logActivity('updated');
        });

        static::deleted(function ($model) {
            $model->logActivity('deleted');
        });
    }

    public function logActivity($action)
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'entity_type' => class_basename($this),
            'entity_id' => $this->id,
            'old_values' => $action === 'updated' ? $this->getOriginal() : null,
            'new_values' => in_array($action, ['created', 'updated']) ? $this->getAttributes() : null,
            'description' => (auth()->user()?->name ?? 'System') . ' ' . $action . ' ' . class_basename($this) . ' #' . $this->id,
        ]);
    }
}
