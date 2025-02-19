<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function evaluation()
    {
        return $this->hasOne(Evaluation::class);
    }

    public function brainFunctionRanks() {
        return $this->hasMany(BrainFunctionRank::class);
    }
}
