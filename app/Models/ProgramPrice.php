<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramPrice extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function program(){
        return $this->belongsTo(Program::class);
    }

    public function scopePending($query, $date = null){
        $date = $date ?: now();
        return $query
        ->where('valid_since', '<=', $date)
        ->where(function($query){
            $query->whereNull('valid_until')
            ->orWhere('valid_until', '>=', now());
        });
    }

    public function scopeCurrent($query){
        return $query
        ->where('valid_since', '<=', now())
        ->where(function($query){
            $query->whereNull('valid_until')
            ->orWhere('valid_until', '>=', now());
        });
    }
}
