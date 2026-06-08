<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventCalendar extends Model
{
    protected $guarded = [];
    protected $casts = ['created_at' => 'date:d/m/Y',];

    public function eventsCalendary(){
        return $this->hasMany(EventCalendar::class);
    }
}