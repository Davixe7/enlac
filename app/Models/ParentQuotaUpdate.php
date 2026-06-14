<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentQuotaUpdate extends Model
{
    protected $fillable = ['amount', 'valid_since', 'applied'];

    public static function scheduleOrCreate(float $amount, string $validSince)
    {
        return self::updateOrCreate(
            ['applied' => false], // Condición: si hay uno pendiente, lo edita
            ['amount' => $amount, 'valid_since' => $validSince] // Valores a actualizar o crear
        );
    }
}
