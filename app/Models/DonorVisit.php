<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonorVisit extends Model
{
    protected $fillable = [
        'donor_id',
        'visit_date',
        'enlac_user_id',
        'recommended_by',
        'reason',
        'schedule_contact_name',
        'schedule_contact_phone',
        'received_by',
        'visitors_names',
        'material_presented',
        'result',
        'comments',
        'interests_hobbies',
    ];

    public function donor(): BelongsTo {
        return $this->belongsTo(Donor::class);
    }

    public function responsible(): BelongsTo
{
    return $this->belongsTo(User::class, 'enlac_user_id');
}
}
