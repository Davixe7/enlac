<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonorGratitude extends Model
{
    protected $fillable = [
    'donor_id',
    'date',
    'campaign_program',
    'type',
    'delivery_method',
    'recipient_name',
];

    public function donor(): BelongsTo {
        return $this->belongsTo(Donor::class);
    }
}
