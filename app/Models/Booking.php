<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Booking extends Model
{
    protected $fillable = [
        'booking_id',
        'case_id',
        'room_id',
        'booking_date',
        'session_type',
        'start_time',
        'end_time',
        'booking_type',
        'number_of_attendees',
        'booking_status',
        'billing_status',
        'special_requirements',
        'internal_notes',
        'booked_by',
    ];

    public function case(): BelongsTo
    {
        return $this->belongsTo(Cases::class, 'case_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function bookedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'booked_by');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(BookingParticipant::class);
    }

    public function breakoutRooms(): HasMany
    {
        return $this->hasMany(BookingBreakoutRoom::class);
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'booking_features')
                    ->withPivot('quantity', 'notes')
                    ->withTimestamps();
    }
}
