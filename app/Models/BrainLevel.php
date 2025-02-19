<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrainLevel extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function brainFunctionRanks() {
        return $this->hasMany(BrainFunctionRank::class);
    }
}
