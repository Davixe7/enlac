<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityDailyScore extends Model
{
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at'];

    public function candidate(){
        return $this->belongsTo(Candidate::class);
    }

    public function activity(){
        return $this->belongsTo(Activity::class);
    }

    public function scopeFilterByCandidate($query, $candidate_id){
        if( !$candidate_id ){ return $query; }
        return $query->whereCandidateId($candidate_id);
    }

    public function scopeFilterByActivity($query, $activity_id){
        if( !$activity_id ){ return $query; }
        return $query->whereActivityId($activity_id);
    }
}
