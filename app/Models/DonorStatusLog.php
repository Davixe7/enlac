<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DonorStatusLog extends Model
{
    public $timestamps = false; //usamos 'changed_at' personalizado
    protected $fillable = ['donor_id', 'is_active', 'changed_at'];
}
