<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interviewee extends Model
{
    protected $guarded = [];

    public function candidate(){
        return $this->belongsTo(Candidate::class);
    }
}
