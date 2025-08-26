<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalProgram extends Model
{
    protected $guarded = [];
    protected $casts = ['created_at' => 'date:d/m/Y',];

    public function beneficiary(){
        return $this->belongsTo(Candidate::class);
    }

    public function activities(){
        return $this->belongsToMany(Activity::class)->withPivot('daily_goal');
    }

    public function plan(){
        return $this->belongsTo(Plan::class);
    }

    public function plan_type(){
        return $this->belongsTo(PlanType::class);
    }
}
