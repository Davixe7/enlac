<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SponsorAddress extends Model
{
    /** @use HasFactory<\Database\Factories\SponsorAddressFactory> */
    use HasFactory;
    protected $guarded = [];

    public function sponsor(){
        return $this->belongsTo(Sponsor::class);
    }
}
