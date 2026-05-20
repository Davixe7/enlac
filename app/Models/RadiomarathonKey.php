<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class RadiomarathonKey extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * El "booted" del modelo aplica el ordenamiento global por clave.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('order_by_code', function (Builder $builder) {
            $builder->orderBy('code', 'asc');
        });
    }
}
