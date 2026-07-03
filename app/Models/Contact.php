<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'type',
        'designation',
        'organization',
        'email',
        'phone',
        'notes',
    ];

    public function bookingParticipants(): HasMany
    {
        return $this->hasMany(BookingParticipant::class);
    }
}
