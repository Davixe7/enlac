<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterviewQuestion extends Model
{
    use HasFactory;

    protected $fillable = ['question_text'];

    public function interviews()
    {
        return $this->belongsToMany(Interview::class)
            ->withPivot('content')
            ->withTimestamps();
    }
}
