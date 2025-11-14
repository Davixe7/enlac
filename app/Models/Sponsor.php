<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Sponsor extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $guarded = [];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_picture')
            ->singleFile()
            ->useDisk('public');
    }

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
