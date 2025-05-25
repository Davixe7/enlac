<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkArea extends Model
{
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at'];

    public function users(){
        return $this->hasMany(User::class);
    }
}
