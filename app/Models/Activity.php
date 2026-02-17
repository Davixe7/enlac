<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at'];

    public function plan_category(){
        return $this->belongsTo(PlanCategory::class);
    }

    public function plans(){
        return $this->belongsToMany(Plan::class);
    }

    public function activityPlan(){
        return $this->hasMany(ActivityPlan::class);
    }

    public function scores(){
        return $this->hasManyThrough(ActivityDailyScore::class, ActivityPlan::class);
    }
}
