<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class FrontDeskContact extends Model
{
    use LogsActivity;

    protected $fillable = [
        'name',
        'company',
    ];

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('company', 'like', "%{$search}%");
    }
}