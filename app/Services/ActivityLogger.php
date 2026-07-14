<?php

namespace App\Services;

use App\Models\ActivityLog;

class ActivityLogger
{
    public static function log($entityType, $entityId, $action, $changes, $description = null)
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => null,
            'new_values' => ['changes' => $changes],
            'description' => $description ?? (auth()->user()?->name ?? 'System') . " {$action} {$entityType} #{$entityId}: " . implode('; ', $changes),
        ]);
    }

    public static function compare($old, $new, $label)
    {
        $old = is_array($old) ? implode(', ', $old) : (string)($old ?? 'none');
        $new = is_array($new) ? implode(', ', $new) : (string)($new ?? 'none');

        if ($old !== $new) {
            return "{$label}: {$old} → {$new}";
        }
        return null;
    }
}
