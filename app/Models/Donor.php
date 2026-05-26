<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Donor extends Model
{
    protected $guarded = [];

    protected $casts = [
        'prospect_for'            => 'array',
        'birth_date'              => 'date:Y-m-d',
        'spouse_birth_date'       => 'date:Y-m-d',
        'knows_facilities'        => 'boolean',
        'is_private_contact'      => 'boolean',
        'is_active'               => 'boolean',
        'status_changed_at'       => 'datetime',
    ];

    protected static function booted()
    {
        static::created(function ($donor) {
            $donor->statusLogs()->create([
                'is_active' => (bool) $donor->is_active,
                'changed_at' => now(),
            ]);
        });

        static::updating(function ($donor) {
            if ($donor->isDirty('is_active')) {
                $donor->status_changed_at = now();
            }
        });

        static::updated(function ($donor) {
            if ($donor->isDirty('is_active')) {
                $donor->statusLogs()->create([
                    'is_active' => (bool) $donor->is_active,
                    'changed_at' => now(),
                ]);
            }
        });
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name} {$this->second_last_name}";
    }

    public function fiscalRecords()
    {
        return $this->hasMany(DonorFiscalRecord::class);
    }

    public function gratitudes()
    {
        return $this->hasMany(DonorGratitude::class)->orderBy('date', 'desc');
    }

    public function visits()
    {
        return $this->hasMany(DonorVisit::class)->orderBy('visit_date', 'desc');
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(DonorShipment::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class, 'donor_id')->orderBy('payment_date', 'desc');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(DonorStatusLog::class)->orderBy('changed_at', 'desc');
    }
}
