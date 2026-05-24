<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonorShipment extends Model
{
    protected $guarded = [];

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }
}
