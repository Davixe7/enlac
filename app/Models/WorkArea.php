<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkArea extends Model
{
    protected $guarded = [];

    public function users(){
        return $this->hasMany(User::class);
    }
}
