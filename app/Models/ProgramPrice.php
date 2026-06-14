<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramPrice extends Model
{
    protected $guarded = [];

    public function program(){
        return $this->belongsTo(Program::class);
    }

    public function scopePending($query, $date = null){
        $date = $date ?: now();
        return $query
        ->where('valid_since', '<=', $date)
        ->whereNull('valid_until');
    }
}
