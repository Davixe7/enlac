<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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

    public function sponsorships(){
        return $this->hasMany(Sponsorship::class);
    }

    public function candidates(){
        return $this->hasManyThrough(Candidate::class, Sponsorship::class);
    }

    public function paymentConfigs(){
        return $this->hasMany(PaymentConfig::class);
    }

    public function payments(){
        return $this->hasMany(Payment::class);
    }

    public function getFullNameAttribute(){
        $fullNameArray = array_filter([$this->name, $this->last_name, $this->second_last_name]);
        return join(" ", $fullNameArray);
    }

    public function scopeByCandidate(Builder $query, ?int $candidateId = null){
        if( !$candidateId ){
            return $query;
        }

        return $query->whereHas('sponsorships', function($query) use ($candidateId){
            $query->whereCandidateId( $candidateId );
        });
    }
}
