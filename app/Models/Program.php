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

    public function snapshots(): HasMany
    {
        return $this->hasMany(ProgramSnapshot::class);
    }

    public function currentPrice($date = null) {
        $date = $date ?? now();

        return $this->snapshots()
            ->where('valid_since', '<=', $date)
            ->where(function($query) use ($date) {
                $query->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', $date);
            })
            ->first()?->price ?? $this->price;
    }
}
