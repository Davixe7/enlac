<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $guarded = [];

    public function candidate(){
        return $this->belongsTo(Candidate::class);
    }

    public function user(){
        return $this->belongsTo(User::class, 'created_by_id')
        ->withDefault(['name'=>'--', 'last_name'=>'--']);
    }

    public function paymentDetails(){
        return $this->hasMany(PaymentDetail::class);
    }

    public function sponsor(){
        return $this->belongsTo(Sponsor::class);
    }

    // Accesor para generar el folio con formato C-26-00001
    public function getFolioAttribute()
    {
        $year = $this->created_at ? $this->created_at->format('y') : date('y');
        return 'C-' . $year . '-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }

    public function paymentConfig(){
        return $this->belongsTo(PaymentConfig::class);
    }
}
