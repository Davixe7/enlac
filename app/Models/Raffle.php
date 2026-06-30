<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Raffle extends Model
{
    protected $guarded = [];

    public function tickets(){
        return $this->hasMany(RaffleTicket::class);
    }

    public function activity(){
        return $this->belongsTo(ProcurationActivity::class, 'procuration_activity_id', 'id');
    }
}
