<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function questions()
    {
        return $this->belongsToMany(InterviewQuestion::class)
            ->withPivot('content')
            ->withTimestamps();
    }
}
