<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class EvaluationSchedule extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function evaluator(){
        return $this->belongsTo(User::class, 'evaluator_id')->withDefault(['name'=>'NO DISPONIBLE']);
    }

    public function candidate(){
        return $this->belongsTo(Candidate::class);
    }
}
