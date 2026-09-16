<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RadiomarathonCall extends Model
{
    protected $guarded = [];

    protected $casts = [
        'has_tax_receipt' => 'boolean'
    ];

    public function radiomarathon(){
        return $this->belongsTo(ProcurationActivity::class);
    }
}
