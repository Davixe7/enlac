<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $guarded = [];
    protected $casts = ['created_at' => 'date:d/m/Y',];

    public function group() {
        return $this->belongsTo(Group::class);
    }

    public function activities(){
        return $this->belongsToMany(Activity::class)->withPivot('daily_goal');
    }

    public function plan_category(){
        return $this->belongsTo(PlanCategory::class);
    }
}
