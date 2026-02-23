<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateStatusLog extends Model
{
    protected $guarded = [];
    protected $casts = ['created_at' => 'datetime:d/m/Y'];

    public function candidate(){
        return $this->belongsTo(Candidate::class);
    }

    public function author(){
        return $this->belongsTo(User::class);
    }

    public function scopeByBeneficiary($query, $candidateId){
        if(!$candidateId) return $query;
        return $query->where('candidate_id', $candidateId);
    }
}
