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
        'batch_name',
        'contact_id',
        'address_to',
        'letter_date',
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
}