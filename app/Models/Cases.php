<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cases extends Model
{
    protected $fillable = [
        'reference_number',
        'status',
        'notes',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'case_id');
    }
}
