<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at'];

    public function plan_type(){
        return $this->belongsTo(PlanType::class);
    }
}
