<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentConfig extends Model
{
    protected $guarded = [];

    public function candidate(){
        $this->belongsToMany(Candidate::class);
    }

    public function sponsor(){
        $this->belongsToMany(Sponsor::class);
    }

    public function getMonthlyAmountAttribute(){
        return $this->amount / 12;
    }

    public function getYearlyPaymentsCountAttribute(){
        return 12 / $this->frequency;
    }
}
