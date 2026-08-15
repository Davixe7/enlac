<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentPromise extends Model
{
    protected $guarded = [];

    protected $casts = [
        'date'               => 'date:d/m/Y',
        'anonymous'          => 'boolean',
        'deductible_receipt' => 'boolean',
    ];

    /**
     * Relación con el Donante
     */
    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    /**
     * Relación con la Clave de Radiomaratón
     */
    public function radiomarathonKey(): BelongsTo
    {
        return $this->belongsTo(RadiomarathonKey::class);
    }
}
