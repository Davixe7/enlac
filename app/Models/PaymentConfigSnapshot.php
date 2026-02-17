<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentConfigSnapshot extends Model
{
    protected $guarded = [];

    public function paymentConfig()
    {
        return $this->belongsTo(PaymentConfig::class);
    }

    /**
     * Monthly amount derived from this snapshot's amount and frequency.
     */
    public function getMonthlyAmountAttribute()
    {
        if (!$this->frequency) {
            return 0;
        }

        $yearlyPaymentCount = 12 / $this->frequency;
        $yearlyPaidAmount = $yearlyPaymentCount * $this->amount;

        return $yearlyPaidAmount / 12;
    }
}

