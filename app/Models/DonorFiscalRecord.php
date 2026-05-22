<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonorFiscalRecord extends Model
{
    protected $guarded = [];

    protected $casts = [
        'home_collection' => 'boolean',
        'company_anniversary' => 'date:Y-m-d',
        'billing_birth_date' => 'date:Y-m-d',
    ];

    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    protected static function booted(): void
    {
        static::saving(function (DonorFiscalRecord $record) {
            // Lista de columnas string de la migración que no aceptan NULL
            $stringFields = ['street', 'exterior_number', 'neighborhood', 'city', 'state'];

            foreach ($stringFields as $field) {
                if (is_null($record->getAttribute($field))) {
                    $record->setAttribute($field, '');
                }
            }
        });
    }
}
