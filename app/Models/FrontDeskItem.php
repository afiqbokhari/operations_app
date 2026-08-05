<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FrontDeskItem extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        'date_received',
        'batch_number',
        'contact_id',
        'address_to',
        'passed_to', 
        'letter_date',
        'case_reference',  
        'matter_id',
        'received_via',
        'doc_type',
        'details',
        'collected_by',
        'collected_at',
        'remarks',
        'logged_by',
    ];

    protected $casts = [
        'date_received' => 'date',
        'letter_date' => 'date',
        'doc_type' => 'array',
        'collected_at' => 'datetime',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(FrontDeskContact::class);
    }

    public function matter(): BelongsTo
    {
        return $this->belongsTo(FrontDeskMatter::class);
    }

    public function collectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    public function passedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'passed_to');
    }

    public function loggedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'logged_by');
    }

    public function scopePendingPickup($query)
    {
        return $query->whereNull('collected_by');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date_received', today());
    }

    public function scopeAging($query, $days = 7)
    {
        return $query->whereNull('collected_by')
            ->whereDate('date_received', '<=', now()->subDays($days));
    }

    public static function getCurrentBatchNumber(): ?int
    {
        $hour = now()->hour;
        $minute = now()->minute;
        $time = $hour * 100 + $minute; // e.g., 8:30 = 830, 14:30 = 1430
        
        return match(true) {
            $time >= 830 && $time < 1000 => 1,   // 8:30 AM - 10:00 AM
            $time >= 1000 && $time < 1200 => 2,  // 10:00 AM - 12:00 PM
            $time >= 1200 && $time < 1430 => 3,  // 12:00 PM - 2:30 PM
            $time >= 1430 && $time < 1600 => 4,  // 2:30 PM - 4:00 PM
            $time >= 1600 && $time < 1730 => 5,  // 4:00 PM - 5:30 PM
            default => null,                      // Outside batch hours
        };
    }
}