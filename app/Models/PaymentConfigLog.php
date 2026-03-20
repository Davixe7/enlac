<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentConfigLog extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    public function paymentConfig()
    {
        return $this->belongsTo(PaymentConfig::class);
    }
}
