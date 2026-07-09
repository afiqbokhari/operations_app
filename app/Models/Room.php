<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Room extends Model
{
    use LogsActivity;

    protected $fillable = [
        'room_code',
        'room_name',
        'floor',
        'capacity',
        'type',
        'status',
        'notes',
        'is_breakout',
    ];

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'room_features')
                    ->withTimestamps();
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function breakoutBookings(): HasMany
    {
        return $this->hasMany(BookingBreakoutRoom::class);
    }

    public function scopeOrderedByType($query)
    {
        return $query->orderByRaw("FIELD(type, 'hearing_room', 'breakout_room', 'mediation_room', 'conference_room')")
                     ->orderByRaw("LENGTH(room_code), room_code");
    }
}
