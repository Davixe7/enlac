<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentConfig extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function snapshots(){
        return $this->hasMany(PaymentConfigSnapshot::class);
    }

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

    public function periodBalance($startDate, $endDate){
        return Payment::where('candidate_id', $this->candidate_id)
                    ->where('sponsor_id', $this->sponsor_id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->groupBy('candidate_id')
                    ->sum('amount');
    }

    public function getSnapshotForPeriod(int $year, int $month, int $day = 1): ?PaymentConfigSnapshot
    {
        $date = \Carbon\Carbon::create($year, $month, $day);
        return $this->snapshots()
            ->where('effective_since', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('effective_until')->orWhere('effective_until', '>=', $date);
            })
            ->orderByDesc('effective_since')
            ->first();
    }
}
