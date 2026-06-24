<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SponsorshipLog extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    public function Sponsorship()
    {
        return $this->belongsTo(Sponsorship::class);
    }
}
