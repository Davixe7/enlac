<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicationLog extends Model
{
    protected $guarded = [];

    public function medication(){
        return $this->belongsTo(Medication::class);
    }
}
