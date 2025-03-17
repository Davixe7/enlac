<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'signed_at' => 'date:d/m/Y',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function interview_questions()
    {
        return $this->belongsToMany(InterviewQuestion::class);
    }
}
