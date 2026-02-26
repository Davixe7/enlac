<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $guarded = [];

    public function candidate(){
        return $this->belongsTo(Candidate::class);
    }

    public function planCategory(){
        return $this->belongsTo(PlanCategory::class);
    }
}
