<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquinotherapySchedule extends Model
{
    protected $guarded = [];
    
    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function comments()
    {
        return $this->hasMany(EquinotherapyComment::class);
    }

}
