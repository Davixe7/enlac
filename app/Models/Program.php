<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    use HasFactory ;
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at'];
    protected $casts = [
        'is_active' => 'boolean',
        'valid_since' => 'date:Y-m-d', // Fuerza el formato de fecha correcto
    ];

    public function prices(): HasMany
    {
        return $this->hasMany(ProgramPrice::class);
    }

    public function getCurrentPriceAttribute($date = null) {
        $date = $date ?? now();

        return $this->prices()
            ->where('valid_since', '<=', $date)
            ->where(function($query) use ($date) {
                $query->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', $date);
            })
            ->first()?->price ?? $this->price;
    }

    public function priceAt($date){
        $date = $date ?? now();

        return $this->prices()
            ->where('valid_since', '<=', $date)
            ->where(function($query) use ($date) {
                $query->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', $date);
            })
            ->first()?->price ?? $this->price;
    }

    public function programStatusLogs(){
        return $this->hasMany(ProgramStatusLog::class);
    }

    public function latestLog(){
        return $this->hasOne(ProgramStatusLog::class)->latestOfMany();
    }

    public function pendingPriceUpdate(){
        return $this->hasOne(ProgramPrice::class)
        ->where('valid_since', '>', now());
    }
}
