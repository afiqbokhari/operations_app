<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use LogsActivity;

    protected $fillable = [
        'event_name', 'room_id', 'start_date', 'end_date',
        'start_time', 'end_time', 'organizer', 'attendees_count',
        'setup_needed', 'catering_needed', 'notes',
        'status', 'booked_by', 'reviewed_by', 'reviewed_at', 'reject_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'reviewed_at' => 'datetime',
        'setup_needed' => 'bool',
        'catering_needed' => 'bool',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function bookedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'booked_by');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
