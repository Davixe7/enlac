<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    use HasFactory;

    // Permitir asignación masiva de todos los campos dinámicos enviados desde Quasar
    protected $guarded = [];

    // Casts para asegurar que los tipos lleguen limpios a JavaScript
    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'equivalent_amount_mxn' => 'decimal:2',
        'has_tax_receipt' => 'boolean',
        'boteo_ten_percent' => 'decimal:2',
    ];

    /**
     * Relación con el Donante (Smart Search)
     */
    public function donor(): BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    /**
     * Relación con el Catálogo de Actividades de Procuración que creamos antes
     */
    public function procurationActivity(): BelongsTo
    {
        return $this->belongsTo(ProcurationActivity::class);
    }
}
