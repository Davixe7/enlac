<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    protected $guarded = [];

    public function candidates(){
        $this->belongsToMany(Candidate::class, 'payment_configs', 'candidate_id', 'sponsor_id');
    }
}
