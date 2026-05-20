<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonorFiscalRecord extends Model
{
    protected $guarded = [];

    protected $casts = [
        'home_collection' => 'boolean',
        'company_anniversary' => 'date:Y-m-d',
        'billing_birth_date' => 'date:Y-m-d',
    ];

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }
}
