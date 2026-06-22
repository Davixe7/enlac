<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sponsorship extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function candidate(){
        return $this->belongsTo(Candidate::class);
    }

    public function sponsor(){
        return $this->belongsTo(Sponsor::class);
    }

    public function paymentConfigs(){
        return $this->hasMany(PaymentConfig::class);
    }

    public function paymentConfig(){
        return $this->hasOne(PaymentConfig::class)
        ->where('effective_since', '<=', now())
        ->whereNull('effective_until');
    }

    public function deductible_receipt(){
        return $this->hasOne(DeductibleReceipt::class);
    }

    public function getMonthlyAmountAttribute(){
        return $this->amount / $this->frequency;
    }

    public function getYearlyPaymentsCountAttribute(){
        return 12 / $this->frequency;
    }

    public function scopeBySponsor(Builder $query, ?int $sponsor_id = null){
        if( !$sponsor_id ){
            return $query;
        }

        return $query->whereSponsorId( $sponsor_id );
    }

    public function scopeByCandidate(Builder $query, ?int $candidate_id){
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

    public function getSnapshotForPeriod(int $year, int $month, int $day = 1): ?PaymentConfig
    {
        $date = \Carbon\Carbon::create($year, $month, $day);
        return $this->paymentConfigs()
            ->where('effective_since', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('effective_until')->orWhere('effective_until', '>=', $date);
            })
            ->orderByDesc('effective_since')
            ->first();
    }
}
