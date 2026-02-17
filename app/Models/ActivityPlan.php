<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ActivityPlan extends Model
{
    protected $table = 'activity_plan';

    protected $fillable = [
        'plan_id',
        'activity_id',
        'daily_goal',
    ];

    public function scores(): HasMany
    {
        return $this->hasMany(ActivityDailyScore::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}
