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
        return $this->belongsTo(Sponsor::class);
    }

    public function deductible_receipt(){
        return $this->hasOne(DeductibleReceipt::class);
    }

    public function getMonthlyAmountAttribute(){
        $yearlyPaymentCount = 12 / $this->frequency;
        $yearlyPaidAmount = $yearlyPaymentCount * $this->amount;
        return $yearlyPaidAmount / 12;
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

    public function scopeByCandidate($query, $candidate_id){
        if( !$candidate_id ){
            return $query;
        }

        return $query->whereCandidateId( $candidate_id );
    }
}
