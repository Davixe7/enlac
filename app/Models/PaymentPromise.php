<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentPromise extends Model
{
    protected $guarded = [];
    protected $casts = [
        'date' => 'date:d/m/Y'
    ];
}
