<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquinotherapyTransfer extends Model
{
    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

}
