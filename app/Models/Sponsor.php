<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    protected $guarded = [];

    public function addresses(){
        return $this->hasMany(SponsorAddress::class);
    }

    public function candidates(){
        return $this->belongsToMany(Candidate::class, 'payment_configs', 'candidate_id', 'sponsor_id');
    }

    public function payments(){
        return $this->hasMany(Payment::class);
    }

    public function payment_configs(){
        return $this->hasMany(PaymentConfig::class);
    }

    public function scopeByCandidate($query, $candidateId){
        if( !$candidateId ){
            return $query;
        }

        return $query->whereHas(['payment_configs' => function($query) use ($candidateId){
            $query->whereCandidateId( $candidateId );
        }]);
    }
}
