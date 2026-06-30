<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaffleSeller extends Model
{
    protected $guarded = [];

    public function tickets(){
        return $this->hasMany(RaffleTicket::class);
    }
}
