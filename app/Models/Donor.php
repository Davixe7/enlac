<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    protected $guarded = [];

    protected $casts = [
        'prospect_for' => 'array',
        'birth_date' => 'date:Y-m-d',
        'spouse_birth_date' => 'date:Y-m-d',
        'knows_facilities' => 'boolean',
        'is_private_contact' => 'boolean',
        'is_active' => 'boolean',
        'status_changed_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::updating(function ($donor) {
            if ($donor->isDirty('is_active')) {
                $donor->status_changed_at = now();
            }
        });
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name} {$this->second_last_name}";
    }
}
