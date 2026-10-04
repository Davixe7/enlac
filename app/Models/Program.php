<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at'];
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function prices(): HasMany
    {
        return $this->hasMany(ProgramPrice::class);
    }

    public function getCurrentPriceAttribute($date = null)
    {
        $date = $date ? \Carbon\Carbon::parse($date)->format('Y-m-d') : now()->format('Y-m-d');

        return $this->prices()
            ->where('valid_since', '<=', $date)
            ->where(function ($query) use ($date) {
                $query->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', $date);
            })
            ->first()?->price ?? $this->price;
    }

    public function priceAt($date)
    {
        $date = $date ? \Carbon\Carbon::parse($date)->format('Y-m-d') : now()->format('Y-m-d');

        return $this->prices()
            ->where('valid_since', '<=', $date)
            ->where(function ($query) use ($date) {
                $query->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', $date);
            })
            ->first()?->price ?? $this->price;
    }

    public function programStatusLogs()
    {
        return $this->hasMany(ProgramStatusLog::class);
    }

    public function latestLog()
    {
        return $this->hasOne(ProgramStatusLog::class)->latestOfMany();
    }

    // Solo es un cambio pendiente si NO ha sido aplicado aún (applied = 0)
    public function pendingPriceUpdate()
    {
        return $this->hasOne(ProgramPrice::class)
            ->where('applied', 0)
            ->where('valid_since', '>', now()->format('Y-m-d'));
    }

    public function currentPriceRecord()
    {
        return $this->hasOne(ProgramPrice::class)
            ->where('applied', 1)
            ->latestOfMany('valid_since');
    }

    public function getValidSinceAttribute()
    {
        $currentRecord = $this->prices()
            ->where('applied', 1)
            ->where('valid_since', '<=', now()->format('Y-m-d'))
            ->latest('valid_since')
            ->first();

        return $currentRecord?->valid_since ?? $this->attributes['created_at'] ?? null;
    }
}
