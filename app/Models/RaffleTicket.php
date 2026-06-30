<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaffleTicket extends Model
{
    protected $guarded = [];

    public function raffle()
    {
        return $this->belongsTo(Raffle::class);
    }

    public function buyer()
    {
        return $this->belongsTo(Donor::class, 'donor_id', 'id');
    }

    public function seller()
    {
        return $this->belongsTo(RaffleSeller::class, 'raffle_seller_id', 'id');
    }

    public function donations(){
        return $this->hasMany(Donation::class, 'raffle_ticket_id', 'id');
    }
}
