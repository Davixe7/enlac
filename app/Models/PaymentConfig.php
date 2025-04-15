<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentConfig extends Model
{
    protected $guarded = [];

    public function candidate(){
        return $this->belongsTo(Candidate::class);
    }

    public function sponsor(){
        return  $this->belongsToMany(Sponsor::class);
    }

    public function getMonthlyAmountAttribute(){
        return $this->amount / 12;
    }

    public function getYearlyPaymentsCountAttribute(){
        return 12 / $this->frequency;
    }

    public function scopeBySponsor($query, $sponsor_id){
        if( !$sponsor_id ){
            return $query;
        }

        return $query->whereSponsorId( $sponsor_id );
    }
}
