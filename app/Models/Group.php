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

    public function program(){
        return $this->belongsTo(Program::class);
    }

    public function scopeByOwner($query, $ownerId){
        return $query->whereHas('candidate', fn($q)=>$q->whereId($ownerId))
        ->whereIsIndividual(1);
    }

    public function scopeIncludesCandidate($query, $candidateId){
        if( !$candidateId ) {return $query; }
        return $query->whereHas('candidates', fn($q)=>$q->whereId($candidateId));
    }

    public function leader()
    {
        return $this->belongsTo(User::class, 'group_leader_id');
    }

    public function assistant()
    {
        return $this->belongsTo(User::class, 'assistant_id');
    }
}
