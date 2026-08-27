<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RadiomarathonCall extends Model
{
    protected $guarded = [];

    public function radiomarathon(){
        return $this->belongsTo(ProcurationActivity::class);
    }
}
