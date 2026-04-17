<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanType extends Model
{
    protected $guarded = [];

    public function plan_category(){
        return $this->belongsTo(PlanCategory::class);
    }
}
