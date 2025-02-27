<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrainFunction extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function brainFunctionRanks() {
        return $this->hasMany(BrainFunctionRank::class);
    }
}
