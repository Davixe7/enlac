<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramSnapshot extends Model
{
    protected $guarded = [];

    public function program(){
        return $this->belongsTo(Program::class);
    }
}
