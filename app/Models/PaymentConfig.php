<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentConfig extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected function casts(): array{
        return [
            'effective_since' => 'date',
            'effective_until' => 'date',
        ];
    }

    public function sponsorship()
    {
        return $this->belongsTo(Sponsorship::class);
    }

    public function getMonthlyAmountAttribute()
    {
        if (!$this->frequency) {
            return 0;
        }

        return $this->amount / $this->frequency;
    }

    public function getSchoolYearAttribute(){
        $month = $this->effective_since->month;
        $year  = $this->effective_since->year;
        return $month >= 8 ? $year : $year - 1;
    }

    public function getStartMonthSchoolAttribute()
    {
        $today = now();
        $currentSchoolYear = $today->month >= 8 ? $today->year : $today->year - 1;

        if ($this->schoolYear != $currentSchoolYear) {
            return 8;
        }

        return $this->effective_since->month;
    }

    public function payments(){
        return $this->hasMany(Payment::class);
    }

    public function sponsor(){
        return $this->belongsTo(Sponsor::class)->withDefault(function(){
            return [
                'full_name' => 'Cuota de Padres'
            ];
        });
    }

    public function scopeByType($query, $type){
        if(!$type){
            return $query;
        }

        return $query
        ->whereHas('sponsorship', fn($q)=>$q->where('type', 'parent'));
    }

    public function paymentDetails(){
        return $this->hasMany(PaymentDetail::class);
    }
}

