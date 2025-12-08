<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public const APPOINTMENT_TYPES = [
        0,1,2,3
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id', 'id')->withDefault(['name'=>'SIN ASIGNAR']);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByCandidate($query, $candidate_id){
        if( !$candidate_id ){
            return $query;
        }

        return $query->whereCandidateId($candidate_id);
    }
}
