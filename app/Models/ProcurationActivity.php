<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\ProcurationActivityType;
use Illuminate\Database\Eloquent\Model;

class ProcurationActivity extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'type' => ProcurationActivityType::class,
    ];

    public function raffleTickets(){
        return $this->hasMany(RaffleTicket::class);
    }
}
