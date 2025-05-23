<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory ;
    protected $guarded = [];

    public function cadidates()
    {
        return $this->belongsToMany(Candidate::class)
            ->withTimestamps();
    }
}
