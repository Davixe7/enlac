<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $guarded = [];

    public function candidates(){
        return $this->belongsToMany(Candidate::class);
    }

    public function plans(){
        return $this->hasMany(Plan::class);
    }
}
