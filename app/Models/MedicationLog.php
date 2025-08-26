<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicationLog extends Model
{
    protected $guarded = [];

    protected $casts = ['created_at' => 'date:m/d/Y'];

    public function medication(){
        return $this->belongsTo(Medication::class);
    }
}
