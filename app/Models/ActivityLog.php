<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'old_values',
        'new_values',
        'description',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getChangesAttribute()
    {
        if (!$this->old_values || !$this->new_values) return null;

        $changes = [];
        $skip = ['id', 'created_at', 'updated_at', 'booked_by'];

        foreach ($this->new_values as $key => $new) {
            if (in_array($key, $skip)) continue;
            $old = $this->old_values[$key] ?? null;
            if ($old != $new) {
                $label = ucwords(str_replace('_', ' ', $key));
                $oldDisplay = is_null($old) ? 'empty' : $old;
                $newDisplay = is_null($new) ? 'empty' : $new;
                $changes[] = "<strong>{$label}:</strong> {$oldDisplay} → {$newDisplay}";
            }
        }
        return $changes;
    }

    protected $appends = ['changes'];
}
